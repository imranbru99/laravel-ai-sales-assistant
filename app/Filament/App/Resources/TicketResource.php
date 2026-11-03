<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Customer Service';
    protected static ?string $navigationLabel = 'Helpdesk Tickets';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('helpdesk_tickets');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Details')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->default(fn () => 'TCK-' . date('Ymd') . '-' . rand(100, 999))
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent (SLA 2h)',
                            ])
                            ->default('medium')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'pending_client' => 'Pending Client Response',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->default('new')
                            ->required(),
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'full_name')
                            ->searchable(),
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignee', 'name')
                            ->searchable(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(4)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('contact.full_name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'gray' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'new',
                        'warning' => 'in_progress',
                        'info' => 'pending_client',
                        'success' => fn ($state) => in_array($state, ['resolved', 'closed']),
                    ]),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Agent'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
