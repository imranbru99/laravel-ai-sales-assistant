<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\CustomReportResource\Pages;
use App\Models\CustomReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomReportResource extends Resource
{
    protected static ?string $model = CustomReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Analytics & Governance';
    protected static ?string $navigationLabel = 'Custom Reports';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('custom_report_builder');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Monthly Won Deals by Rep'),
                        Forms\Components\Select::make('module_key')
                            ->label('Data Source Module')
                            ->options([
                                'leads' => 'Leads & Scores',
                                'deals' => 'Deals & Pipelines',
                                'invoices' => 'Invoices & Payments',
                                'attendance' => 'Staff Attendance',
                                'tickets' => 'Customer Support Tickets',
                            ])
                            ->required(),
                        Forms\Components\Select::make('chart_type')
                            ->options([
                                'bar' => 'Bar Chart',
                                'line' => 'Line Trend',
                                'pie' => 'Donut / Pie Chart',
                                'table' => 'Tabular Summary',
                            ])
                            ->default('bar')
                            ->required(),
                        Forms\Components\TextInput::make('group_by')
                            ->placeholder('e.g. status, owner, month')
                            ->default('status'),
                        Forms\Components\TagsInput::make('fields')
                            ->placeholder('Add column name (press Enter)')
                            ->default(['id', 'title', 'value', 'created_at'])
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Automated Email Schedule')
                    ->schema([
                        Forms\Components\Select::make('schedule_frequency')
                            ->options([
                                'daily' => 'Daily Morning Digest',
                                'weekly' => 'Weekly Monday Summary',
                                'monthly' => 'Monthly Executive Report',
                            ])
                            ->placeholder('Manual only (no automated email)'),
                        Forms\Components\TextInput::make('schedule_email')
                            ->email()
                            ->placeholder('recipient@example.com'),
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
                Tables\Columns\TextColumn::make('module_key')
                    ->label('Module')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('chart_type')
                    ->label('Visualization')
                    ->badge(),
                Tables\Columns\TextColumn::make('schedule_frequency')
                    ->label('Schedule')
                    ->placeholder('On-demand')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('module_key')
                    ->options([
                        'leads' => 'Leads',
                        'deals' => 'Deals',
                        'invoices' => 'Invoices',
                        'attendance' => 'Attendance',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomReports::route('/'),
            'create' => Pages\CreateCustomReport::route('/create'),
            'edit' => Pages\EditCustomReport::route('/{record}/edit'),
        ];
    }
}
