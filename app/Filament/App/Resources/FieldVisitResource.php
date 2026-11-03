<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\FieldVisitResource\Pages;
use App\Models\FieldVisit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FieldVisitResource extends Resource
{
    protected static ?string $model = FieldVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'People & HR';
    protected static ?string $navigationLabel = 'Field Sales Visits';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('field_sales');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Visit Details')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required(),
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'full_name'),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->default(now()->addDay())
                            ->required(),
                        Forms\Components\DateTimePicker::make('check_in_at'),
                        Forms\Components\DateTimePicker::make('check_out_at'),
                        Forms\Components\TextInput::make('latitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('outcome')
                            ->placeholder('e.g. Contract negotiation / Sample demonstrated'),
                        Forms\Components\Textarea::make('visit_notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Client Visited')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Field Rep'),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('check_in_at')
                    ->label('Checked In')
                    ->boolean()
                    ->trueIcon('heroicon-o-map-pin')
                    ->trueColor('success'),
                Tables\Columns\TextColumn::make('outcome'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFieldVisits::route('/'),
            'create' => Pages\CreateFieldVisit::route('/create'),
            'edit' => Pages\EditFieldVisit::route('/{record}/edit'),
        ];
    }
}
