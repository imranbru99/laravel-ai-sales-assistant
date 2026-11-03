<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\PortalInvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PortalInvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'My Invoices';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_due')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger' => 'overdue',
                        'info' => 'sent',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('M j, Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Invoice $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo $record->getPdfDocument()->output();
                        }, "{$record->invoice_number}.pdf");
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortalInvoices::route('/'),
        ];
    }
}
