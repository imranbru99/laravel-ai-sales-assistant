<?php

namespace App\Filament\App\Resources\SequenceResource\Pages;

use App\Filament\App\Resources\SequenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSequences extends ListRecords
{
    protected static string $resource = SequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
