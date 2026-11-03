<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\DealResource\Pages;
use App\Models\Deal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('deals');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Opportunity Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),
                        Forms\Components\Select::make('pipeline_id')
                            ->relationship('pipeline', 'name')
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('stage_id')
                            ->relationship('stage', 'name')
                            ->required(),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'full_name')
                            ->searchable(),
                        Forms\Components\DatePicker::make('expected_close_date'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'won' => 'Won',
                                'lost' => 'Lost',
                            ])
                            ->default('open')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stage.name')
                    ->label('Pipeline Stage')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'open',
                        'success' => 'won',
                        'danger' => 'lost',
                    ]),
                Tables\Columns\IconColumn::make('is_rotting')
                    ->label('Stalled')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check-circle')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('expected_close_date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeals::route('/'),
            'create' => Pages\CreateDeal::route('/create'),
            'edit' => Pages\EditDeal::route('/{record}/edit'),
        ];
    }
}
