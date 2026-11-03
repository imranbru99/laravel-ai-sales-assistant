<?php

namespace App\Filament\App\Resources\FieldVisitResource\Pages;

use App\Filament\App\Resources\FieldVisitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFieldVisit extends CreateRecord
{
    protected static string $resource = FieldVisitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
