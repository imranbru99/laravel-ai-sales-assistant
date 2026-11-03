<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SolutionController extends Controller
{
    public function index(): View
    {
        $presets = config('features.presets', []);
        return view('public.solutions.index', compact('presets'));
    }

    public function show(string $presetKey): View
    {
        $presets = config('features.presets', []);
        $allModules = config('features.modules', []);

        abort_if(!isset($presets[$presetKey]), 404, "Solution preset [{$presetKey}] not found.");

        $preset = $presets[$presetKey];

        $enabledModules = [];
        foreach ($preset['modules'] as $moduleKey) {
            if (isset($allModules[$moduleKey])) {
                $mod = $allModules[$moduleKey];
                $mod['slug'] = Str::slug($mod['name']);
                $enabledModules[$moduleKey] = $mod;
            }
        }

        return view('public.solutions.show', compact('preset', 'presetKey', 'enabledModules'));
    }
}
