<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\LeadCaptureFormResource\Pages;
use App\Models\LeadCaptureForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadCaptureFormResource extends Resource
{
    protected static ?string $model = LeadCaptureForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?string $navigationLabel = 'Lead Forms';
    protected static ?int $navigationSort = 9;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('lead_capture');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Settings')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->helperText('Universal Unicode slug generated automatically'),
                        Forms\Components\TextInput::make('submit_button_text')
                            ->default('Get Instant Quote'),
                        Forms\Components\TextInput::make('redirect_url')
                            ->placeholder('https://yourdomain.com/thank-you'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make('Form Fields (Drag-and-Drop)')
                    ->schema([
                        Forms\Components\KeyValue::make('fields')
                            ->keyLabel('Field Name (e.g. name, email, phone, company, budget)')
                            ->valueLabel('Field Type (text, email, tel, number, select)')
                            ->default([
                                'full_name' => 'text',
                                'work_email' => 'email',
                                'phone_number' => 'tel',
                                'company_name' => 'text',
                                'project_budget' => 'number',
                            ])
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
                Tables\Columns\TextColumn::make('slug')
                    ->badge()
                    ->color('indigo'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views'),
                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Leads Captured'),
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
            'index' => Pages\ListLeadCaptureForms::route('/'),
            'create' => Pages\CreateLeadCaptureForm::route('/create'),
            'edit' => Pages\EditLeadCaptureForm::route('/{record}/edit'),
        ];
    }
}
