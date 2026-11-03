<?php

namespace App\Filament\Portal\Resources\PortalTicketResource\Pages;

use App\Filament\Portal\Resources\PortalTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePortalTicket extends CreateRecord
{
    protected static string $resource = PortalTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['ticket_number'] = 'TCK-' . date('Ymd') . '-' . rand(100, 999);
        $data['status'] = 'new';
        return $data;
    }
}
