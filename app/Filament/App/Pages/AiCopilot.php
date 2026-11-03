<?php

namespace App\Filament\App\Pages;

use App\Core\Ai\AiGateway;
use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AiCopilot extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'AI Sales Core';
    protected static ?string $navigationLabel = 'AI Copilot (Chat)';
    protected static ?string $title = 'AI Sales Copilot';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.app.pages.ai-copilot';

    public string $message = '';
    public array $conversation = [];

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('ai_copilot');
    }

    public function mount(): void
    {
        $tenant = app(TenantContext::class)->getTenant();
        $this->conversation = [
            [
                'role' => 'assistant',
                'content' => "Hello! I am your **AI Sales Copilot**. I have secure, real-time access to your CRM leads, deals, and follow-up queues for **{$tenant->name}**.\n\nAsk me anything, like:\n* *Which hot leads have not been contacted in 5 days?*\n* *Give me a pipeline summary for this month*\n* *Draft a WhatsApp follow-up for high-value prospects*",
                'time' => now()->format('H:i'),
            ]
        ];
    }

    public function askPrompt(string $prompt): void
    {
        $this->message = $prompt;
        $this->sendMessage();
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userMsg = trim($this->message);
        $this->message = '';

        $this->conversation[] = [
            'role' => 'user',
            'content' => $userMsg,
            'time' => now()->format('H:i'),
        ];

        $tenant = app(TenantContext::class)->getTenant();
        $aiGateway = app(AiGateway::class);

        $reply = $aiGateway->askCopilot($userMsg, $tenant, auth()->user());

        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $reply,
            'time' => now()->format('H:i'),
        ];
    }
}
