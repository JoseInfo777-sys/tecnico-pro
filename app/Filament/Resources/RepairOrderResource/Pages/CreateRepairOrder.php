<?php

namespace App\Filament\Resources\RepairOrderResource\Pages;

use App\Filament\Resources\RepairOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRepairOrder extends CreateRecord
{
    protected static string $resource = RepairOrderResource::class;

    //VOLVER A INDEX DESPUES DE GUARDAR
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
