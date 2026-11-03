<?php

namespace App\Filament\App\Pages;

use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use App\Models\BusinessPreset;
use App\Models\Module;
use App\Models\Tenant;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ModuleMarketplace extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Settings & Modules';
    protected static ?string $navigationLabel = 'Module Marketplace';
    protected static ?string $title = 'Module Marketplace & Presets';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.app.pages.module-marketplace';

    public string $selectedCategory = 'all';
    public string $search = '';

    public function getTenant(): ?Tenant
    {
        return app(TenantContext::class)->getTenant();
    }

    public function getCategoriesProperty(): array
    {
        return config('features.categories', []);
    }

    public function getPresetsProperty(): Collection
    {
        return BusinessPreset::all();
    }

    public function getModulesProperty(): Collection
    {
        $tenant = $this->getTenant();
        $query = Module::orderBy('sort_order');

        if ($this->selectedCategory !== 'all') {
            $query->where('category', $this->selectedCategory);
        }

        $modules = $query->get();

        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $modules = $modules->filter(function ($module) use ($search) {
                return str_contains(strtolower($module->name), $search)
                    || str_contains(strtolower($module->description ?? ''), $search);
            });
        }

        $moduleManager = app(ModuleManager::class);

        return $modules->map(function (Module $module) use ($moduleManager, $tenant) {
            $isEnabled = $tenant ? $moduleManager->isModuleEnabled($module->key, $tenant) : false;
            $moduleData = config("features.modules.{$module->key}", []);

            return [
                'id' => $module->id,
                'key' => $module->key,
                'name' => $module->name,
                'category' => $module->category,
                'description' => $module->description,
                'icon' => $module->icon,
                'is_core' => $module->is_core,
                'price_monthly' => $module->price_monthly,
                'dependencies' => $module->dependencies ?? [],
                'features' => $moduleData['features'] ?? [],
                'is_enabled' => $isEnabled,
            ];
        });
    }

    public function toggleModule(string $moduleKey): void
    {
        $tenant = $this->getTenant();
        if (!$tenant) {
            Notification::make()->title('No tenant found in current session.')->danger()->send();
            return;
        }

        $moduleManager = app(ModuleManager::class);
        $isCurrentlyEnabled = $moduleManager->isModuleEnabled($moduleKey, $tenant);

        if ($isCurrentlyEnabled) {
            try {
                $moduleManager->disableModule($moduleKey, $tenant);
                Notification::make()
                    ->title("Module [{$moduleKey}] disabled successfully.")
                    ->warning()
                    ->send();
            } catch (\InvalidArgumentException $e) {
                Notification::make()
                    ->title($e->getMessage())
                    ->danger()
                    ->send();
            }
        } else {
            $moduleManager->enableModule($moduleKey, $tenant);
            Notification::make()
                ->title("Module [{$moduleKey}] enabled successfully!")
                ->success()
                ->send();
        }
    }

    public function applyPreset(string $presetKey): void
    {
        $tenant = $this->getTenant();
        if (!$tenant) {
            Notification::make()->title('No tenant found in current session.')->danger()->send();
            return;
        }

        try {
            app(ModuleManager::class)->applyPreset($tenant, $presetKey);
            $preset = BusinessPreset::where('key', $presetKey)->first();

            Notification::make()
                ->title("Preset [{$preset->name}] applied successfully!")
                ->body('Relevant modules have been activated and navigation has been updated.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title("Failed to apply preset: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }
}
