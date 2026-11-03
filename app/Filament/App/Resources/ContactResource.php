<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('contacts_companies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Details')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('job_title')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('department')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_primary')
                            ->label('Primary Contact for Company'),
                        Forms\Components\Toggle::make('gdpr_consent')
                            ->label('GDPR Marketing Consent Received'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_title')
                    ->searchable(),
                Tables\Columns\IconColumn::make('gdpr_consent')
                    ->boolean()
                    ->label('GDPR'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
