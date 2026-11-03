<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\WorkflowResource\Pages;
use App\Models\Workflow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?string $navigationLabel = 'Workflow Automations';
    protected static ?int $navigationSort = 8;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('workflow_builder');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Trigger & Status')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('trigger_event')
                            ->options([
                                'LeadCreated' => 'When a New Lead is Created',
                                'LeadScoredHot' => 'When a Lead is Scored Grade A (Hot)',
                                'StageChanged' => 'When a Deal Stage is Changed',
                                'EmailReplied' => 'When a Prospect Replies to an Email',
                                'PaymentReceived' => 'When an Invoice is Paid',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Automated Actions')
                    ->schema([
                        Forms\Components\KeyValue::make('actions')
                            ->keyLabel('Action Type (e.g. notify_team, assign_owner, create_task, webhook)')
                            ->valueLabel('Configuration / Parameters')
                            ->required(),
                    ]),
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
                Tables\Columns\BadgeColumn::make('trigger_event')
                    ->colors(['primary']),
                Tables\Columns\TextColumn::make('run_count')
                    ->label('Times Executed'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_run_at')
                    ->dateTime('M j, H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflows::route('/'),
            'create' => Pages\CreateWorkflow::route('/create'),
            'edit' => Pages\EditWorkflow::route('/{record}/edit'),
        ];
    }
}
