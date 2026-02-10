<?php

namespace App\Filament\Resources\ReadyOrdersResource\Pages;

use App\Filament\Resources\ReadyOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReadyOrders extends EditRecord
{
    protected static string $resource = ReadyOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
