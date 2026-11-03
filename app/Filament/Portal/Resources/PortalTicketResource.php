<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\PortalTicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PortalTicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Support Tickets';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Create Support Request')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'General Inquiry',
                                'medium' => 'Standard Support',
                                'high' => 'Urgent Issue',
                            ])
                            ->default('medium')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
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
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'new',
                        'warning' => 'in_progress',
                        'info' => 'pending_client',
                        'success' => fn ($state) => in_array($state, ['resolved', 'closed']),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortalTickets::route('/'),
            'create' => Pages\CreatePortalTicket::route('/create'),
        ];
    }
}
