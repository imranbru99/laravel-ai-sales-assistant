<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\PortalQuotationResource\Pages;
use App\Models\Quotation;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PortalQuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'My Quotes & Proposals';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->limit(35),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'declined',
                    ]),
                Tables\Columns\TextColumn::make('valid_until')
                    ->date('M j, Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Quotation $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo $record->getPdfDocument()->output();
                        }, "{$record->quote_number}.pdf");
                    }),
                Tables\Actions\Action::make('accept')
                    ->label('Approve & Sign')
                    ->icon('heroicon-m-check-badge')
                    ->color('primary')
                    ->visible(fn (Quotation $record) => $record->status === 'sent')
                    ->action(function (Quotation $record) {
                        $record->update([
                            'status' => 'accepted',
                            'accepted_at' => now(),
                        ]);
                        Notification::make()->title('Quotation accepted! Our team has been notified.')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortalQuotations::route('/'),
        ];
    }
}
