<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Commerce & Finance';
    protected static ?string $navigationLabel = 'Orders & Invoices';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('invoicing');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Overview')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->default(fn () => 'INV-' . date('Ymd') . '-' . rand(100, 999))
                            ->required(),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'full_name')
                            ->searchable(),
                        Forms\Components\DatePicker::make('due_date')
                            ->default(now()->addDays(14)),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent to Client',
                                'paid' => 'Paid in Full',
                                'partial' => 'Partially Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('balance_due')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_due')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'sent',
                        'success' => 'paid',
                        'primary' => 'partial',
                        'danger' => 'overdue',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Invoice $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo $record->getPdfDocument()->output();
                        }, "{$record->invoice_number}.pdf");
                    }),
                Tables\Actions\Action::make('recordPayment')
                    ->label('Pay')
                    ->icon('heroicon-m-banknotes')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->default(fn (Invoice $record) => $record->balance_due > 0 ? $record->balance_due : $record->total_amount)
                            ->required(),
                        Forms\Components\Select::make('method')
                            ->options([
                                'stripe' => 'Credit / Debit Card (Stripe)',
                                'bkash' => 'bKash Mobile Payment',
                                'nagad' => 'Nagad Mobile Payment',
                                'sslcommerz' => 'SSLCommerz Gateway',
                                'bank' => 'Bank Wire Transfer',
                                'cash' => 'Cash',
                            ])
                            ->default('bank')
                            ->required(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->placeholder('e.g. TXN-998822'),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $paidAmount = (float) $data['amount'];

                        Payment::create([
                            'tenant_id' => $record->tenant_id,
                            'invoice_id' => $record->id,
                            'amount' => $paidAmount,
                            'currency' => $record->currency,
                            'method' => $data['method'],
                            'transaction_id' => $data['transaction_id'] ?? null,
                            'status' => 'completed',
                            'recorded_by' => auth()->id(),
                        ]);

                        $newPaidTotal = $record->paid_amount + $paidAmount;
                        $newBalance = max(0, $record->total_amount - $newPaidTotal);
                        $status = ($newBalance <= 0) ? 'paid' : 'partial';

                        $record->update([
                            'paid_amount' => $newPaidTotal,
                            'balance_due' => $newBalance,
                            'status' => $status,
                            'paid_at' => ($status === 'paid') ? now() : null,
                        ]);

                        Notification::make()->title('Payment recorded successfully!')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
