<?php

namespace App\Filament\Resources\ReadyOrdersResource\Pages;

use App\Filament\Resources\ReadyOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReadyOrders extends ListRecords
{
    protected static string $resource = ReadyOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
