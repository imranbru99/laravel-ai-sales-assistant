<?php

namespace App\Filament\Portal\Resources\PortalTicketResource\Pages;

use App\Filament\Portal\Resources\PortalTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPortalTickets extends ListRecords
{
    protected static string $resource = PortalTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
