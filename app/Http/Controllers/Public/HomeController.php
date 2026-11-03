<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = config('features.categories', []);
        $allModules = config('features.modules', []);
        $presets = config('features.presets', []);

        $featuredKeys = [
            'lead_scoring',
            'followup_assistant',
            'ai_copilot',
            'leads_management',
            'pipelines_deals',
            'omnichannel_inbox',
            'quotations',
            'invoicing',
            'attendance_hub',
        ];

        $featuredModules = [];
        foreach ($featuredKeys as $key) {
            if (isset($allModules[$key])) {
                $featuredModules[$key] = $allModules[$key];
            }
        }

        $plans = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('saas_plans')) {
            $plans = SaasPlan::where('is_active', true)->orderBy('price_monthly')->get();
        }

        return view('public.home', compact('categories', 'featuredModules', 'presets', 'plans'));
    }
}
