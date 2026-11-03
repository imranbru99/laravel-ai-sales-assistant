<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('activities');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Activity Details')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'call' => 'Phone Call',
                                'meeting' => 'Meeting / Demo',
                                'email' => 'Email',
                                'whatsapp' => 'WhatsApp Chat',
                                'sms' => 'SMS',
                                'visit' => 'On-Site Visit',
                                'task' => 'Follow-up Task',
                            ])
                            ->default('call')
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('due_at')
                            ->default(now()->addDay()),
                        Forms\Components\Toggle::make('is_completed')
                            ->label('Mark as Completed'),
                        Forms\Components\Select::make('lead_id')
                            ->relationship('lead', 'contact_name')
                            ->searchable(),
                        Forms\Components\Select::make('deal_id')
                            ->relationship('deal', 'title')
                            ->searchable(),
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'full_name')
                            ->searchable(),
                        Forms\Components\TextInput::make('outcome')
                            ->placeholder('e.g. Left voicemail / Scheduled demo'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'call',
                        'success' => 'meeting',
                        'info' => 'email',
                        'warning' => 'task',
                        'gray' => 'visit',
                    ]),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('lead.contact_name')
                    ->label('Lead / Account'),
                Tables\Columns\TextColumn::make('due_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Done'),
                Tables\Columns\TextColumn::make('outcome'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
