<?php

namespace App\Filament\App\Resources\SequenceResource\Pages;

use App\Filament\App\Resources\SequenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSequence extends EditRecord
{
    protected static string $resource = SequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
