<?php

namespace App\Filament\App\Pages;

use App\Core\Ai\AiGateway;
use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use App\Models\InboxConversation;
use App\Models\InboxMessage;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class UnifiedInbox extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?string $navigationLabel = 'Unified Inbox';
    protected static ?string $title = 'Unified Omnichannel Inbox';
    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.app.pages.unified-inbox';

    public ?int $activeConversationId = null;
    public string $selectedChannel = 'all';
    public string $replyContent = '';
    public bool $isInternalNote = false;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('unified_inbox');
    }

    public function getConversationsProperty(): Collection
    {
        $query = InboxConversation::with(['lead', 'contact', 'messages'])
            ->latest('last_message_at');

        if ($this->selectedChannel !== 'all') {
            $query->where('channel', $this->selectedChannel);
        }

        $conversations = $query->get();

        if ($this->activeConversationId === null && $conversations->isNotEmpty()) {
            $this->activeConversationId = $conversations->first()->id;
        }

        return $conversations;
    }

    public function getActiveConversationProperty(): ?InboxConversation
    {
        if ($this->activeConversationId === null) {
            return null;
        }

        return InboxConversation::with(['messages.sender', 'lead', 'contact'])->find($this->activeConversationId);
    }

    public function selectConversation(int $id): void
    {
        $this->activeConversationId = $id;
        $this->replyContent = '';
    }

    public function generateAiReply(): void
    {
        $conversation = $this->activeConversation;
        if (!$conversation) {
            return;
        }

        $lastMessage = $conversation->messages->where('is_internal_note', false)->last();
        $target = $conversation->lead ?? $conversation->contact;

        if ($target) {
            $draft = app(AiGateway::class)->draftFollowUp($target, [
                'channel' => $conversation->channel,
                'tone' => 'consultative',
            ]);
            $this->replyContent = $draft['content'] ?? '';
            Notification::make()->title('AI Draft generated! Review and click Send.')->success()->send();
        } else {
            $this->replyContent = "Thank you for reaching out. We have received your inquiry and our team is preparing a customized proposal for your review.";
        }
    }

    public function sendReply(): void
    {
        if (empty(trim($this->replyContent))) {
            return;
        }

        $conversation = $this->activeConversation;
        if (!$conversation) {
            return;
        }

        $tenant = app(TenantContext::class)->getTenant();

        InboxMessage::create([
            'tenant_id' => $tenant->id,
            'conversation_id' => $conversation->id,
            'sender_type' => $this->isInternalNote ? 'agent' : 'agent',
            'sender_id' => auth()->id(),
            'sender_name' => auth()->user()?->name ?? 'Sales Executive',
            'channel' => $conversation->channel,
            'content' => trim($this->replyContent),
            'is_internal_note' => $this->isInternalNote,
            'status' => 'delivered',
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => 'open',
        ]);

        $this->replyContent = '';
        $this->isInternalNote = false;

        Notification::make()->title($this->isInternalNote ? 'Internal note added.' : 'Reply sent successfully!')->success()->send();
    }
}
