<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        $plans = SaasPlan::where('is_active', true)->orderBy('price_monthly')->get();

        if ($plans->isEmpty()) {
            $plans = collect([
                (object) [
                    'name' => 'Starter',
                    'slug' => 'starter',
                    'description' => 'For small businesses, shops, and agile boutique sales teams.',
                    'price_monthly' => '49.00',
                    'price_yearly' => '470.00',
                    'max_users' => 5,
                    'max_branches' => 1,
                    'max_storage_mb' => 2048,
                    'max_ai_tokens' => 250000,
                    'is_featured' => false,
                ],
                (object) [
                    'name' => 'Growth',
                    'slug' => 'growth',
                    'description' => 'For scaling B2B agencies, distributors, real-estate firms, and clinics.',
                    'price_monthly' => '149.00',
                    'price_yearly' => '1430.00',
                    'max_users' => 20,
                    'max_branches' => 5,
                    'max_storage_mb' => 10240,
                    'max_ai_tokens' => 1000000,
                    'is_featured' => true,
                ],
                (object) [
                    'name' => 'Enterprise',
                    'slug' => 'enterprise',
                    'description' => 'For garment exporters, institutions, enterprises, and high-volume operations.',
                    'price_monthly' => '399.00',
                    'price_yearly' => '3830.00',
                    'max_users' => 100,
                    'max_branches' => 25,
                    'max_storage_mb' => 51200,
                    'max_ai_tokens' => 5000000,
                    'is_featured' => false,
                ],
            ]);
        }

        $allModules = config('features.modules', []);

        return view('public.pricing', compact('plans', 'allModules'));
    }
}
