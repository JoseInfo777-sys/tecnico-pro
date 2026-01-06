<?php

namespace App\Filament\Widgets;

use App\Models\RepairOrder;
use App\Models\Customer;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected function getStats(): array
    {
        return [
            // Total de Clientes
            Stat::make('Clientes Totales', Customer::count())
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            // Equipos Pendientes (Cuentas las órdenes que no están 'Listo' ni 'Entregado')
            Stat::make('Equipos en Taller', RepairOrder::whereIn('status', ['Pendiente', 'En Reparación'])->count())
                ->description('Pendientes por terminar')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('warning'),

            // Dinero por Cobrar (Suma de precios de órdenes en estado 'Listo' que no se han entregado)
            Stat::make('Por Cobrar', '$' . number_format(RepairOrder::where('status', 'Listo')->sum('price'), 2))
                ->description('Dinero de equipos terminados')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

}
