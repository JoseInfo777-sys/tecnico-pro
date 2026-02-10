<?php

namespace App\Filament\Resources\DeviceResource\Pages;

use App\Filament\Resources\DeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDevice extends CreateRecord
{
    protected static string $resource = DeviceResource::class;

    //VOLVER A INDEX DESPUES DE GUARDAR
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
