<?php

namespace App\Core\Ai;

use App\Core\Tenancy\TenantContext;
use App\Models\AiLog;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use ImranDevBd\AiHub\Facades\AIHub;
use Illuminate\Support\Facades\Log;

class AiGateway
{
    protected DemoAiDriver $demoDriver;

    public function __construct(DemoAiDriver $demoDriver)
    {
        $this->demoDriver = $demoDriver;
    }

    public function getActiveProvider(?Tenant $tenant = null): string
    {
        $tenant = $tenant ?? app(TenantContext::class)->getTenant();
        return $tenant?->getSetting('ai', 'provider', 'demo') ?? config('ai-hub.default_provider', 'demo');
    }

    public function scoreLead(Lead $lead): array
    {
        $startTime = microtime(true);
        $provider = $this->getActiveProvider($lead->tenant);

        // If in demo mode or offline driver configured
        if ($provider === 'demo' || !config('ai-hub.providers.openai.api_key')) {
            $result = $this->demoDriver->scoreLead($lead);
        } else {
            try {
                // Call laravel-ai-hub
                $prompt = "Analyze this B2B lead and return JSON with score (0-100), grade (A/B/C/D), explanation, and recommended_action:\n" .
                    json_encode([
                        'contact' => $lead->contact_name,
                        'company' => $lead->company_name,
                        'email' => $lead->email,
                        'source' => $lead->source,
                        'budget' => $lead->budget,
                    ]);

                $response = AIHub::completion($prompt);
                $decoded = json_decode($response, true);

                $result = is_array($decoded) && isset($decoded['score']) ? $decoded : $this->demoDriver->scoreLead($lead);
            } catch (\Throwable $e) {
                Log::warning("AI Lead Scoring failed via provider [{$provider}], falling back to Demo driver: {$e->getMessage()}");
                $result = $this->demoDriver->scoreLead($lead);
            }
        }

        $latency = (int) round((microtime(true) - $startTime) * 1000);
        $this->logUsage('lead_scoring', $provider, 150, 220, 0.0015, $latency, $lead->tenant_id);

        return $result;
    }

    public function draftFollowUp(Lead|Deal $record, array $options = []): array
    {
        $startTime = microtime(true);
        $provider = $this->getActiveProvider($record->tenant);

        if ($provider === 'demo' || !config('ai-hub.providers.openai.api_key')) {
            $result = $this->demoDriver->draftFollowUp($record, $options);
        } else {
            try {
                $recipient = ($record instanceof Lead) ? $record->contact_name : ($record->contact?->full_name ?? 'Client');
                $channel = $options['channel'] ?? 'email';
                $lang = $options['language'] ?? 'en';
                $tone = $options['tone'] ?? 'consultative';

                $prompt = "Draft a sales follow-up {$channel} in {$lang} with {$tone} tone for {$recipient}. Return JSON with 'subject' and 'content'.";
                $response = AIHub::completion($prompt);
                $decoded = json_decode($response, true);

                $result = is_array($decoded) && isset($decoded['content']) ? $decoded : $this->demoDriver->draftFollowUp($record, $options);
            } catch (\Throwable $e) {
                Log::warning("AI Follow-up drafting failed via provider [{$provider}], falling back to Demo driver: {$e->getMessage()}");
                $result = $this->demoDriver->draftFollowUp($record, $options);
            }
        }

        $latency = (int) round((microtime(true) - $startTime) * 1000);
        $this->logUsage('followup_draft', $provider, 200, 300, 0.002, $latency, $record->tenant_id);

        return $result;
    }

    public function askCopilot(string $query, ?Tenant $tenant = null, ?User $user = null): string
    {
        $startTime = microtime(true);
        $tenant = $tenant ?? app(TenantContext::class)->getTenant();
        $provider = $this->getActiveProvider($tenant);

        $answer = $this->demoDriver->askCopilot($query, $tenant, $user);

        $latency = (int) round((microtime(true) - $startTime) * 1000);
        $this->logUsage('copilot_chat', $provider, 100, 150, 0.001, $latency, $tenant?->id);

        return $answer;
    }

    public function enrichCompany(string $urlOrName, ?Tenant $tenant = null): array
    {
        $startTime = microtime(true);
        $result = $this->demoDriver->enrichCompany($urlOrName);

        $latency = (int) round((microtime(true) - $startTime) * 1000);
        $this->logUsage('enrichment', 'catalog_scraper', 120, 280, 0.001, $latency, $tenant?->id);

        return $result;
    }

    protected function logUsage(string $action, string $provider, int $inTokens, int $outTokens, float $cost, int $latency, ?int $tenantId): void
    {
        try {
            AiLog::create([
                'tenant_id' => $tenantId,
                'user_id' => auth()->id(),
                'action_type' => $action,
                'provider' => $provider,
                'model' => $provider === 'demo' ? 'demo-engine-v1' : 'gpt-4o',
                'input_tokens' => $inTokens,
                'output_tokens' => $outTokens,
                'cost' => $cost,
                'latency_ms' => $latency,
            ]);
        } catch (\Throwable $e) {
            // Silently catch logging issues to avoid failing user requests
        }
    }
}
