<?php

namespace App\Filament\App\Resources\LeadCaptureFormResource\Pages;

use App\Filament\App\Resources\LeadCaptureFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadCaptureForm extends EditRecord
{
    protected static string $resource = LeadCaptureFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
