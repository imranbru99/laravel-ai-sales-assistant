<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\AiLog;
use App\Models\LeadScore;
use App\Models\Tenant;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalUsers = User::count();
        $totalAiInteractions = LeadScore::count() + AiLog::count();

        // Estimated MRR calculation
        $starterCount = Tenant::where('plan', 'starter')->where('status', 'active')->count();
        $growthCount = Tenant::where('plan', 'growth')->where('status', 'active')->count();
        $enterpriseCount = Tenant::where('plan', 'enterprise')->where('status', 'active')->count();
        $mrr = ($starterCount * 49) + ($growthCount * 149) + ($enterpriseCount * 399);

        return [
            Stat::make('Active Tenants', $activeTenants)
                ->description('Total active businesses')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make('Platform Users', $totalUsers)
                ->description('Across all organizations')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Estimated MRR', '$' . number_format($mrr, 0))
                ->description('Monthly Recurring Revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('AI Invocations', number_format($totalAiInteractions))
                ->description('Lead scores & follow-up drafts')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),
        ];
    }
}
