<?php

namespace App\Filament\App\Pages;

use App\Core\Ai\AiGateway;
use App\Core\Modules\ModuleManager;
use App\Models\Lead;
use Filament\Pages\Page;

class LeadScoringSimulator extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'AI Sales Core';
    protected static ?string $navigationLabel = 'Scoring Simulator';
    protected static ?string $title = 'AI Lead Scoring Simulator';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.app.pages.lead-scoring-simulator';

    public string $contactName = 'Rahim Ahmed';
    public string $companyName = 'Bengal Global Trading';
    public string $email = 'rahim@bengaltrade.com';
    public string $phone = '+8801711223344';
    public string $source = 'website';
    public float $budget = 15000;
    public string $status = 'qualified';

    public ?array $result = null;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('ai_lead_scoring');
    }

    public function mount(): void
    {
        $this->simulate();
    }

    public function simulate(): void
    {
        $dummyLead = new Lead([
            'contact_name' => $this->contactName,
            'company_name' => $this->companyName,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => $this->source,
            'budget' => $this->budget,
            'status' => $this->status,
            'last_contacted_at' => now()->subDay(),
        ]);

        $this->result = app(AiGateway::class)->scoreLead($dummyLead);
    }
}
