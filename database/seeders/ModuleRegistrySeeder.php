<?php

namespace Database\Seeders;

use App\Models\BusinessPreset;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleRegistrySeeder extends Seeder
{
    public function run(): void
    {
        $features = config('features.modules', []);
        $sortOrder = 1;

        foreach ($features as $key => $data) {
            Module::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'icon' => $data['icon'] ?? 'heroicon-o-cube',
                    'version' => '1.0.0',
                    'description' => $data['description'] ?? '',
                    'dependencies' => $data['dependencies'] ?? [],
                    'is_core' => (bool) ($data['is_core'] ?? false),
                    'default_enabled' => (bool) ($data['default_enabled'] ?? true),
                    'price_monthly' => $data['price_monthly'] ?? 0,
                    'sort_order' => $sortOrder++,
                ]
            );
        }

        $presets = config('features.presets', []);
        foreach ($presets as $key => $preset) {
            BusinessPreset::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $preset['name'],
                    'icon' => $preset['icon'] ?? 'heroicon-o-briefcase',
                    'description' => $preset['description'] ?? '',
                    'modules' => $preset['modules'] ?? [],
                    'default_settings' => $preset['default_settings'] ?? [],
                ]
            );
        }
    }
}
