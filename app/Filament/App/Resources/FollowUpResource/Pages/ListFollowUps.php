<?php

namespace App\Filament\App\Resources\FollowUpResource\Pages;

use App\Filament\App\Resources\FollowUpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFollowUps extends ListRecords
{
    protected static string $resource = FollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
