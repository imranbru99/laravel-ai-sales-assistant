<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('contacts_companies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('domain')
                            ->prefix('https://')
                            ->placeholder('acme.com'),
                        Forms\Components\TextInput::make('industry')
                            ->placeholder('Software / Manufacturing / Retail'),
                        Forms\Components\Select::make('employee_range')
                            ->options([
                                '1-10' => '1 - 10 employees',
                                '11-50' => '11 - 50 employees',
                                '51-200' => '51 - 200 employees',
                                '201-500' => '201 - 500 employees',
                                '500+' => '500+ employees',
                            ]),
                        Forms\Components\TextInput::make('annual_revenue')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('owner_id')
                            ->relationship('owner', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('country'),
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
                Tables\Columns\TextColumn::make('domain')
                    ->searchable(),
                Tables\Columns\TextColumn::make('industry')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_range'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Account Owner'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
