<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Marketing Automation';
    protected static ?string $navigationLabel = 'Campaigns';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('email_campaigns');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Setup')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'email' => 'Email Broadcast',
                                'sms' => 'SMS Broadcast',
                                'whatsapp' => 'WhatsApp Broadcast',
                            ])
                            ->default('email')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sending' => 'Sending',
                                'completed' => 'Completed',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('scheduled_at'),
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('content')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'email',
                        'info' => 'sms',
                        'success' => 'whatsapp',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'scheduled',
                        'primary' => 'sending',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('sent_count')->label('Sent'),
                Tables\Columns\TextColumn::make('open_count')->label('Opened'),
                Tables\Columns\TextColumn::make('click_count')->label('Clicks'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
