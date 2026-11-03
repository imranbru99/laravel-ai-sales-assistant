<?php

namespace App\Filament\App\Resources\ApprovalRequestResource\Pages;

use App\Filament\App\Resources\ApprovalRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovalRequests extends ListRecords
{
    protected static string $resource = ApprovalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
