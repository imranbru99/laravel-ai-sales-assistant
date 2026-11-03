<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\ApprovalRequestResource\Pages;
use App\Models\ApprovalRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApprovalRequestResource extends Resource
{
    protected static ?string $model = ApprovalRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Analytics & Governance';
    protected static ?string $navigationLabel = 'Approval Workflows';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('approval_workflows');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Approval Details')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'discount' => 'High Discount Request',
                                'expense' => 'Expense Reimbursement',
                                'quote_over_limit' => 'Quotation Over Limit',
                                'leave' => 'Leave Request',
                                'purchase_order' => 'Purchase Order Approval',
                            ])
                            ->required(),
                        Forms\Components\Select::make('requested_by')
                            ->relationship('requester', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('tier')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('decision_notes')
                            ->placeholder('Remarks or justification for decision')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'discount' => 'Discount',
                        'expense' => 'Expense',
                        'quote_over_limit' => 'Quote Limit',
                        'leave' => 'Leave',
                        'purchase_order' => 'Purchase Order',
                        default => ucfirst($state),
                    })
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requested By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tier')
                    ->label('Tier')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Decided By')
                    ->placeholder('Awaiting review')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'discount' => 'Discount',
                        'expense' => 'Expense',
                        'quote_over_limit' => 'Quote Limit',
                        'leave' => 'Leave',
                        'purchase_order' => 'Purchase Order',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ApprovalRequest $record) => $record->status === 'pending')
                    ->action(function (ApprovalRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Request approved')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (ApprovalRequest $record) => $record->status === 'pending')
                    ->action(function (ApprovalRequest $record) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Request rejected')->danger()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalRequests::route('/'),
            'create' => Pages\CreateApprovalRequest::route('/create'),
            'edit' => Pages\EditApprovalRequest::route('/{record}/edit'),
        ];
    }
}
