<?php

namespace App\Filament\App\Resources;

use App\Core\Ai\FollowUpPlanner;
use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\FollowUpResource\Pages;
use App\Models\FollowUp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'AI Sales Core';
    protected static ?string $navigationLabel = 'Follow-up Assistant';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('ai_followups');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Message Draft')
                    ->schema([
                        Forms\Components\Select::make('channel')
                            ->options([
                                'email' => 'Email',
                                'whatsapp' => 'WhatsApp',
                                'sms' => 'SMS',
                                'call' => 'Call Script',
                                'linkedin' => 'LinkedIn Message',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'approved' => 'Approved',
                                'sent' => 'Sent',
                                'rejected' => 'Rejected',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('content')
                            ->rows(6)
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('tone')
                            ->options([
                                'consultative' => 'Consultative & Expert',
                                'urgent' => 'Urgent & Time-Sensitive',
                                'friendly' => 'Friendly & Approachable',
                                'executive' => 'Executive & Concise',
                            ])
                            ->default('consultative'),
                        Forms\Components\Select::make('language')
                            ->options([
                                'en' => 'English',
                                'bn' => 'বাংলা (Bengali)',
                            ])
                            ->default('en'),
                        Forms\Components\DateTimePicker::make('scheduled_at'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('channel')
                    ->colors([
                        'primary' => 'email',
                        'success' => 'whatsapp',
                        'info' => 'sms',
                        'warning' => 'call',
                        'purple' => 'linkedin',
                    ]),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->weight('semibold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('lead.contact_name')
                    ->label('Recipient')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'scheduled',
                        'primary' => 'approved',
                        'success' => 'sent',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('language')
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->badge(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime('M j, H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (FollowUp $record) => $record->status === 'draft')
                    ->action(function (FollowUp $record) {
                        app(FollowUpPlanner::class)->approve($record);
                        Notification::make()->title('Follow-up approved!')->success()->send();
                    }),
                Tables\Actions\Action::make('send')
                    ->label('Send Now')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('primary')
                    ->visible(fn (FollowUp $record) => in_array($record->status, ['draft', 'approved', 'scheduled']))
                    ->action(function (FollowUp $record) {
                        app(FollowUpPlanner::class)->send($record);
                        Notification::make()->title('Follow-up message dispatched successfully!')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUps::route('/'),
            'create' => Pages\CreateFollowUp::route('/create'),
            'edit' => Pages\EditFollowUp::route('/{record}/edit'),
        ];
    }
}
