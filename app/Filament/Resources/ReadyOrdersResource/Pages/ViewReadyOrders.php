<?php

namespace App\Filament\Resources\ReadyOrdersResource\Pages;

use App\Filament\Resources\ReadyOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReadyOrders extends ViewRecord
{
    protected static string $resource = ReadyOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
