<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\SequenceResource\Pages;
use App\Models\Sequence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SequenceResource extends Resource
{
    protected static ?string $model = Sequence::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?string $navigationLabel = 'Sequences & Cadences';
    protected static ?int $navigationSort = 7;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('sequences');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sequence Overview')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Repeater::make('steps')
                    ->relationship('steps')
                    ->schema([
                        Forms\Components\TextInput::make('step_number')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('delay_days')
                            ->numeric()
                            ->default(1)
                            ->label('Wait Days'),
                        Forms\Components\Select::make('channel')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'whatsapp' => 'WhatsApp',
                                'task' => 'Sales Task',
                            ])
                            ->default('email')
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content_template')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('steps_count')
                    ->counts('steps')
                    ->label('Steps'),
                Tables\Columns\TextColumn::make('total_enrolled')
                    ->label('Enrolled'),
                Tables\Columns\TextColumn::make('total_completed')
                    ->label('Completed'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSequences::route('/'),
            'create' => Pages\CreateSequence::route('/create'),
            'edit' => Pages\EditSequence::route('/{record}/edit'),
        ];
    }
}
