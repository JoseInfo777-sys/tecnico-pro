<?php

namespace App\Filament\Resources\RepairOrderResource\Pages;

use App\Filament\Resources\RepairOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepairOrder extends EditRecord
{
    protected static string $resource = RepairOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
