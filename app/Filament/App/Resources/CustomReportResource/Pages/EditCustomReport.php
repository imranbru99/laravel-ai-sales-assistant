<?php

namespace App\Filament\App\Resources\CustomReportResource\Pages;

use App\Filament\App\Resources\CustomReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomReport extends EditRecord
{
    protected static string $resource = CustomReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
