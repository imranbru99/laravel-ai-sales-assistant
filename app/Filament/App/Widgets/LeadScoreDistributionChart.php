<?php

namespace App\Filament\App\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadScoreDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Lead Quality Distribution (AI Grades)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $gradeA = Lead::where('score_grade', 'A')->count();
        $gradeB = Lead::where('score_grade', 'B')->count();
        $gradeC = Lead::where('score_grade', 'C')->count();
        $gradeD = Lead::where('score_grade', 'D')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => [$gradeA, $gradeB, $gradeC, $gradeD],
                    'backgroundColor' => ['#10b981', '#6366f1', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => ['Grade A (Hot)', 'Grade B (Warm)', 'Grade C (Moderate)', 'Grade D (Low)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
