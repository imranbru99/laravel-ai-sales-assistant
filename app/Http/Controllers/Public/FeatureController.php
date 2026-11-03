<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FeatureController extends Controller
{
    public function index(Request $request): View
    {
        $categories = config('features.categories', []);
        $allModules = config('features.modules', []);

        $activeCategory = $request->query('category');
        $search = strtolower($request->query('q', ''));

        $filteredModules = [];
        foreach ($allModules as $key => $module) {
            $matchesCategory = !$activeCategory || ($module['category'] ?? '') === $activeCategory;
            $matchesSearch = !$search || (
                str_contains(strtolower($module['name']), $search) ||
                str_contains(strtolower($module['description'] ?? ''), $search)
            );

            if ($matchesCategory && $matchesSearch) {
                $module['slug'] = Str::slug($module['name']);
                $filteredModules[$key] = $module;
            }
        }

        return view('public.features.index', [
            'categories' => $categories,
            'modules' => $filteredModules,
            'totalCount' => count($allModules),
            'activeCategory' => $activeCategory,
            'search' => $search,
        ]);
    }

    public function show(string $slug): View
    {
        $allModules = config('features.modules', []);
        $categories = config('features.categories', []);

        $targetModule = null;
        $moduleKey = null;

        foreach ($allModules as $key => $mod) {
            if ($key === $slug || Str::slug($mod['name']) === $slug) {
                $targetModule = $mod;
                $moduleKey = $key;
                $targetModule['slug'] = Str::slug($mod['name']);
                break;
            }
        }

        abort_if(!$targetModule, 404, "Module [{$slug}] not found.");

        $category = $categories[$targetModule['category']] ?? [
            'name' => 'General',
            'icon' => 'heroicon-o-cube',
            'description' => 'Platform module',
        ];

        // Find related modules in the same category
        $relatedModules = [];
        foreach ($allModules as $k => $m) {
            if ($k !== $moduleKey && ($m['category'] ?? '') === $targetModule['category']) {
                $m['slug'] = Str::slug($m['name']);
                $relatedModules[$k] = $m;
                if (count($relatedModules) >= 3) break;
            }
        }

        return view('public.features.show', [
            'module' => $targetModule,
            'moduleKey' => $moduleKey,
            'category' => $category,
            'relatedModules' => $relatedModules,
        ]);
    }
}
