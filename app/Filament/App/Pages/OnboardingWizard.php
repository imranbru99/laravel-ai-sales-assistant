<?php

namespace App\Filament\App\Pages;

use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use App\Models\Branch;
use App\Models\BusinessPreset;
use App\Models\Tenant;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class OnboardingWizard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationGroup = 'Settings & Modules';
    protected static ?string $navigationLabel = 'Onboarding Wizard';
    protected static ?string $title = 'Business Onboarding Wizard';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.app.pages.onboarding-wizard';

    public int $currentStep = 1;
    public int $totalSteps = 4;

    // Step 1: Preset
    public string $selectedPreset = 'agency';

    // Step 2: Localization
    public string $companyName = '';
    public string $currency = 'USD';
    public string $timezone = 'UTC';
    public string $locale = 'en';

    // Step 3: Headquarters & Branches
    public string $hqName = 'Headquarters';
    public string $hqCity = 'Dhaka';
    public string $secondaryBranchName = 'Chittagong Regional Hub';

    // Step 4: AI Provider
    public string $aiProvider = 'demo';
    public string $brandVoiceTone = 'Professional, consultative, and value-oriented';

    public function mount(): void
    {
        $tenant = app(TenantContext::class)->getTenant();
        if ($tenant) {
            $this->companyName = $tenant->name;
            $this->currency = $tenant->currency;
            $this->timezone = $tenant->timezone;
            $this->locale = $tenant->locale;
            $this->selectedPreset = $tenant->business_type ?: 'agency';
        }
    }

    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function finishOnboarding(): void
    {
        $tenant = app(TenantContext::class)->getTenant();
        if (!$tenant) {
            Notification::make()->title('No tenant found.')->danger()->send();
            return;
        }

        // 1. Update tenant info
        $tenant->update([
            'name' => $this->companyName ?: $tenant->name,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'business_type' => $this->selectedPreset,
            'settings' => array_merge($tenant->settings ?? [], [
                'ai_provider' => $this->aiProvider,
                'brand_voice_tone' => $this->brandVoiceTone,
                'onboarding_completed' => true,
            ]),
        ]);

        // 2. Apply business preset
        app(ModuleManager::class)->applyPreset($tenant, $this->selectedPreset);

        // 3. Ensure headquarters branch exists
        Branch::firstOrCreate(
            ['tenant_id' => $tenant->id, 'is_headquarters' => true],
            [
                'name' => $this->hqName ?: 'Main HQ',
                'city' => $this->hqCity ?: 'Capital City',
                'is_active' => true,
            ]
        );

        if (!empty($this->secondaryBranchName)) {
            Branch::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $this->secondaryBranchName],
                [
                    'city' => 'Chittagong',
                    'is_headquarters' => false,
                    'is_active' => true,
                ]
            );
        }

        Notification::make()
            ->title('Onboarding Complete!')
            ->body('Your business is configured with the chosen preset, branches, and AI settings.')
            ->success()
            ->send();

        $this->redirect(route('filament.app.pages.dashboard'));
    }
}
