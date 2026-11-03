<?php

namespace App\Filament\SuperAdmin\Resources\SaasPlanResource\Pages;

use App\Filament\SuperAdmin\Resources\SaasPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSaasPlans extends ListRecords
{
    protected static string $resource = SaasPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
