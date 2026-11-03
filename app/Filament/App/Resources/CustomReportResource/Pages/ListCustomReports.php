<?php

namespace App\Filament\App\Resources\CustomReportResource\Pages;

use App\Filament\App\Resources\CustomReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomReports extends ListRecords
{
    protected static string $resource = CustomReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
