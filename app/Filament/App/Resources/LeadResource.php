<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationGroup = 'CRM & Sales';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('leads');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lead Overview')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\Select::make('owner_id')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'qualified' => 'Qualified',
                                'proposal' => 'Proposal Sent',
                                'won' => 'Won',
                                'lost' => 'Lost',
                            ])
                            ->default('new')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Qualification & AI Signals')
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('source')
                            ->options([
                                'website' => 'Website Inbound',
                                'referral' => 'Referral',
                                'cold_outreach' => 'Cold Outreach',
                                'campaign' => 'Marketing Campaign',
                                'ad' => 'Paid Advertising',
                                'event' => 'Conference / Event',
                                'other' => 'Other',
                            ])
                            ->default('website'),
                        Forms\Components\TextInput::make('score')
                            ->numeric()
                            ->default(50)
                            ->disabled()
                            ->helperText('Automatically calculated by AI Lead Scoring engine'),
                        Forms\Components\Select::make('score_grade')
                            ->options([
                                'A' => 'Grade A (Hot Lead)',
                                'B' => 'Grade B (Warm Lead)',
                                'C' => 'Grade C (Moderate)',
                                'D' => 'Grade D (Low Fit)',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('recommended_next_action')
                            ->columnSpanFull()
                            ->rows(2),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('score_grade')
                    ->label('AI Grade')
                    ->colors([
                        'success' => 'A',
                        'info' => 'B',
                        'warning' => 'C',
                        'danger' => 'D',
                    ])
                    ->formatStateUsing(fn ($record) => "Grade {$record->score_grade} ({$record->score})")
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'new',
                        'info' => 'contacted',
                        'warning' => 'qualified',
                        'success' => fn ($state) => in_array($state, ['proposal', 'won']),
                        'danger' => 'lost',
                    ]),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Assigned To')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'proposal' => 'Proposal Sent',
                        'won' => 'Won',
                        'lost' => 'Lost',
                    ]),
                Tables\Filters\SelectFilter::make('score_grade')
                    ->options([
                        'A' => 'Grade A (Hot)',
                        'B' => 'Grade B (Warm)',
                        'C' => 'Grade C (Moderate)',
                        'D' => 'Grade D (Low)',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
