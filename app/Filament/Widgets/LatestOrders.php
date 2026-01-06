<?php

namespace App\Filament\Widgets;

use App\Models\RepairOrder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Últimas Órdenes de Reparación';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(RepairOrder::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m H:i'),

                Tables\Columns\TextColumn::make('device.model')
                    ->label('Equipo'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'gray',
                        'En Reparación' => 'info',
                        'Listo' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD'),
            ]);
    }
}
