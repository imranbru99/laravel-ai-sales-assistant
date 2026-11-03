<?php

namespace App\Filament\Partner\Resources\PartnerLeadResource\Pages;

use App\Filament\Partner\Resources\PartnerLeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerLead extends CreateRecord
{
    protected static string $resource = PartnerLeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = 'referral';
        $data['status'] = 'new';
        return $data;
    }
}
