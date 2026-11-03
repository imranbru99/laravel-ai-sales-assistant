<?php

namespace App\Filament\App\Resources\FieldVisitResource\Pages;

use App\Filament\App\Resources\FieldVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFieldVisit extends EditRecord
{
    protected static string $resource = FieldVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
