<?php

namespace App\Core\Modules;

use App\Core\Tenancy\TenantContext;
use App\Models\Branch;
use App\Models\BranchModule;
use App\Models\BusinessPreset;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\TenantModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ModuleManager
{
    protected string $cachePrefix = 'module_manager_';

    public function isModuleEnabled(string $moduleKey, ?Tenant $tenant = null, ?Branch $branch = null): bool
    {
        $tenant = $tenant ?? app(TenantContext::class)->getTenant();
        $branch = $branch ?? app(TenantContext::class)->getBranch();

        if (!$tenant) {
            return true; // Outside of tenant context (e.g. public or super admin)
        }

        // Branch override check
        if ($branch) {
            $branchOverride = Cache::remember(
                "{$this->cachePrefix}branch_{$branch->id}_{$moduleKey}",
                3600,
                fn () => BranchModule::where('branch_id', $branch->id)->where('module_key', $moduleKey)->first()
            );

            if ($branchOverride !== null) {
                return (bool) $branchOverride->is_enabled;
            }
        }

        // Tenant check
        return Cache::remember(
            "{$this->cachePrefix}tenant_{$tenant->id}_{$moduleKey}",
            3600,
            function () use ($tenant, $moduleKey) {
                $tenantModule = TenantModule::where('tenant_id', $tenant->id)
                    ->where('module_key', $moduleKey)
                    ->first();

                if ($tenantModule !== null) {
                    return (bool) $tenantModule->is_enabled;
                }

                // Fall back to module default
                $module = Module::where('key', $moduleKey)->first();
                return $module ? (bool) ($module->is_core || $module->default_enabled) : false;
            }
        );
    }

    public function enableModule(string $moduleKey, Tenant $tenant, array $settings = [], ?int $userId = null): void
    {
        TenantModule::updateOrCreate(
            ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
            [
                'is_enabled' => true,
                'settings' => $settings,
                'enabled_at' => now(),
                'enabled_by' => $userId ?? auth()->id(),
            ]
        );

        // Auto-enable dependencies
        $module = Module::where('key', $moduleKey)->first();
        if ($module && !empty($module->dependencies)) {
            foreach ($module->dependencies as $depKey) {
                if (!$this->isModuleEnabled($depKey, $tenant)) {
                    $this->enableModule($depKey, $tenant, [], $userId);
                }
            }
        }

        $this->clearCache($tenant);
    }

    public function disableModule(string $moduleKey, Tenant $tenant): void
    {
        $module = Module::where('key', $moduleKey)->first();
        if ($module && $module->is_core) {
            throw new \InvalidArgumentException("Core module [{$moduleKey}] cannot be disabled.");
        }

        TenantModule::updateOrCreate(
            ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
            ['is_enabled' => false]
        );

        $this->clearCache($tenant);
    }

    public function setBranchOverride(string $moduleKey, Branch $branch, bool $isEnabled, array $settings = []): void
    {
        BranchModule::updateOrCreate(
            ['branch_id' => $branch->id, 'module_key' => $moduleKey],
            ['is_enabled' => $isEnabled, 'settings' => $settings]
        );

        Cache::forget("{$this->cachePrefix}branch_{$branch->id}_{$moduleKey}");
    }

    public function applyPreset(Tenant $tenant, string $presetKey): void
    {
        $preset = BusinessPreset::where('key', $presetKey)->first();
        if (!$preset) {
            throw new \InvalidArgumentException("Business preset [{$presetKey}] not found.");
        }

        $enabledKeys = $preset->modules ?? [];

        // Enable each module specified in the preset
        foreach ($enabledKeys as $moduleKey) {
            TenantModule::updateOrCreate(
                ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
                [
                    'is_enabled' => true,
                    'enabled_at' => now(),
                    'enabled_by' => auth()->id(),
                ]
            );
        }

        // Disable other non-core modules
        $allModules = Module::all();
        foreach ($allModules as $module) {
            if (!$module->is_core && !in_array($module->key, $enabledKeys, true)) {
                TenantModule::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'module_key' => $module->key],
                    ['is_enabled' => false]
                );
            }
        }

        $tenant->update([
            'business_type' => $presetKey,
            'settings' => array_merge($tenant->settings ?? [], $preset->default_settings ?? []),
        ]);

        $this->clearCache($tenant);
    }

    public function getModules(): Collection
    {
        return Cache::remember('all_modules_catalog', 3600, fn () => Module::orderBy('sort_order')->get());
    }

    public function getEnabledModules(Tenant $tenant): Collection
    {
        return $this->getModules()->filter(fn (Module $module) => $this->isModuleEnabled($module->key, $tenant));
    }

    public function clearCache(?Tenant $tenant = null): void
    {
        Cache::forget('all_modules_catalog');
        if ($tenant) {
            $allModules = Module::all();
            foreach ($allModules as $mod) {
                Cache::forget("{$this->cachePrefix}tenant_{$tenant->id}_{$mod->key}");
            }
        }
    }
}
