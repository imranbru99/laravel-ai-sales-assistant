<?php

namespace App\Filament\App\Widgets;

use App\Models\Deal;
use App\Models\FollowUp;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesKpiOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalLeads = Lead::count();
        $hotLeads = Lead::where('score_grade', 'A')->count();
        $openPipelineValue = Deal::where('status', 'open')->sum('value');
        $sentFollowUps = FollowUp::where('status', 'sent')->count();
        $pendingFollowUps = FollowUp::whereIn('status', ['draft', 'scheduled'])->count();
        $totalFollowUps = $sentFollowUps + $pendingFollowUps;
        $complianceRate = $totalFollowUps > 0 ? round(($sentFollowUps / $totalFollowUps) * 100) : 100;

        return [
            Stat::make('Total Prospects', number_format($totalLeads))
                ->description('Active in CRM')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('indigo'),

            Stat::make('Hot Leads (Grade A)', number_format($hotLeads))
                ->description('AI Priority Closing Queue')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),

            Stat::make('Pipeline Value', '$' . number_format($openPipelineValue, 0))
                ->description('Weighted Open Deals')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Follow-up Compliance', "{$complianceRate}%")
                ->description("{$sentFollowUps} sent, {$pendingFollowUps} in queue")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($complianceRate >= 80 ? 'success' : 'warning'),
        ];
    }
}
