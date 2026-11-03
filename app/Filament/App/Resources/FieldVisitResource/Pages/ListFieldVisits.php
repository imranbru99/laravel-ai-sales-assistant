<?php

namespace App\Filament\App\Resources\FieldVisitResource\Pages;

use App\Filament\App\Resources\FieldVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFieldVisits extends ListRecords
{
    protected static string $resource = FieldVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
