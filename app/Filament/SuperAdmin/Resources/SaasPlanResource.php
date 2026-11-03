<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SaasPlanResource\Pages;
use App\Models\SaasPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SaasPlanResource extends Resource
{
    protected static ?string $model = SaasPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Platform Tenancy';
    protected static ?string $navigationLabel = 'Subscription Plans';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Starter / Growth / Enterprise'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price_monthly')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('price_yearly')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('currency')
                            ->default('USD')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Resource Quotas')
                    ->schema([
                        Forms\Components\TextInput::make('max_users')
                            ->numeric()
                            ->default(5)
                            ->required(),
                        Forms\Components\TextInput::make('max_branches')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('max_storage_mb')
                            ->numeric()
                            ->default(1024)
                            ->label('Storage (MB)')
                            ->required(),
                        Forms\Components\TextInput::make('max_ai_tokens')
                            ->numeric()
                            ->default(100000)
                            ->label('AI Tokens / Month')
                            ->required(),
                    ])->columns(4),

                Forms\Components\Section::make('Status & Visibility')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->money('USD')
                    ->label('Monthly'),
                Tables\Columns\TextColumn::make('price_yearly')
                    ->money('USD')
                    ->label('Yearly'),
                Tables\Columns\TextColumn::make('max_users')
                    ->label('Users'),
                Tables\Columns\TextColumn::make('max_branches')
                    ->label('Branches'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSaasPlans::route('/'),
            'create' => Pages\CreateSaasPlan::route('/create'),
            'edit' => Pages\EditSaasPlan::route('/{record}/edit'),
        ];
    }
}
