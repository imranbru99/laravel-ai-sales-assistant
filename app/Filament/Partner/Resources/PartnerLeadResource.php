<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\PartnerLeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerLeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'My Referral Leads';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Submit Referral Prospect')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\TextInput::make('budget')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'new',
                        'warning' => 'contacted',
                        'info' => 'qualified',
                        'success' => 'won',
                        'danger' => 'lost',
                    ]),
                Tables\Columns\BadgeColumn::make('score_grade')
                    ->label('AI Grade')
                    ->colors([
                        'success' => 'A',
                        'info' => 'B',
                        'warning' => 'C',
                        'danger' => 'D',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnerLeads::route('/'),
            'create' => Pages\CreatePartnerLead::route('/create'),
        ];
    }
}
