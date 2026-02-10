<?php

namespace App\Filament\Pages;

use App\Models\RepairOrder;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class ReporteEquiposListos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Reportes Generales';
    protected static ?string $title = 'Centro de Reportes';
    protected static string $view = 'filament.pages.reporte-equipos-listos';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'fecha_inicio_listos' => now()->startOfMonth()->format('Y-m-d'),
            'fecha_fin_listos' => now()->format('Y-m-d'),
            'fecha_inicio_entregados' => now()->startOfMonth()->format('Y-m-d'),
            'fecha_fin_entregados' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECCIÓN 1: EQUIPOS LISTOS
                Section::make('Reporte de Equipos Listos')
                    ->description('Equipos que ya terminaron su reparación pero siguen en el taller.')
                    ->icon('heroicon-m-check-circle')
                    ->schema([
                        DatePicker::make('fecha_inicio_listos')->label('Desde:')->required(),
                        DatePicker::make('fecha_fin_listos')->label('Hasta:')->required(),
                        
                        Actions::make([
                            Action::make('descargarListos')
                                ->label('Generar Reporte Listos')
                                ->icon('heroicon-m-arrow-down-tray')
                                ->color('warning')
                                ->action('generarReporteListos'),
                        ])->columnSpanFull()->alignCenter(),
                    ])->columns(2),

                // SECCIÓN 2: EQUIPOS ENTREGADOS + MONTO TOTAL
                Section::make('Reporte de Equipos Entregados')
                    ->description('Historial de entregas realizadas y total de ingresos.')
                    ->icon('heroicon-m-truck')
                    ->schema([
                        DatePicker::make('fecha_inicio_entregados')->label('Desde:')->required(),
                        DatePicker::make('fecha_fin_entregados')->label('Hasta:')->required(),
                        
                        Actions::make([
                            Action::make('descargarEntregados')
                                ->label('Generar Reporte Entregas (PDF)')
                                ->icon('heroicon-m-document-text')
                                ->color('success')
                                ->action('generarReporteEntregados'),
                        ])->columnSpanFull()->alignCenter(),
                    ])->columns(2),

                // SECCIÓN 3: RESPALDO DE SEGURIDAD
                Section::make('Copia de Seguridad (Backup)')
                    ->description('Realiza un respaldo manual de la base de datos y archivos en el disco externo.')
                    ->icon('heroicon-m-circle-stack')
                    ->schema([
                        Actions::make([
                            Action::make('hacerBackup')
                                ->label('Realizar Backup ahora')
                                ->icon('heroicon-m-arrow-path')
                                ->color('info')
                                ->requiresConfirmation()
                                ->modalHeading('¿Confirmar Respaldo?')
                                ->modalDescription('Asegúrese de que el disco externo esté conectado antes de continuar.')
                                ->action('ejecutarBackup'),
                        ])->columnSpanFull()->alignCenter(),
                    ]),
            ])
            ->statePath('data');
    }

    // Lógica para Equipos Listos
    public function generarReporteListos()
    {
        $datos = $this->form->getState();
        $ordenes = RepairOrder::where('status', 'Listo')
            ->whereBetween('updated_at', [$datos['fecha_inicio_listos'] . ' 00:00:00', $datos['fecha_fin_listos'] . ' 23:59:59'])
            ->get();

        return $this->descargarPDF($ordenes, $datos['fecha_inicio_listos'], $datos['fecha_fin_listos'], 'EQUIPOS LISTOS');
    }

    // Lógica para Equipos Entregados
    public function generarReporteEntregados()
    {
        $datos = $this->form->getState();
        $ordenes = RepairOrder::where('status', 'Entregado')
            ->whereBetween('updated_at', [$datos['fecha_inicio_entregados'] . ' 00:00:00', $datos['fecha_fin_entregados'] . ' 23:59:59'])
            ->get();

        return $this->descargarPDF($ordenes, $datos['fecha_inicio_entregados'], $datos['fecha_fin_entregados'], 'EQUIPOS ENTREGADOS');
    }

    // Función privada para no repetir código del PDF
    private function descargarPDF($ordenes, $desde, $hasta, $titulo)
    {
        if ($ordenes->isEmpty()) {
            \Filament\Notifications\Notification::make()->title('Sin datos')->danger()->send();
            return;
        }

        $totalRecaudado = $ordenes->sum('price');

        $pdf = Pdf::loadView('reportes.general', [
            'ordenes' => $ordenes,
            'desde' => $desde,
            'hasta' => $hasta,
            'titulo' => $titulo,
            'total' => $totalRecaudado,
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "Reporte-{$titulo}.pdf");
    }

    public function ejecutarBackup()
    {
        try {
            // 1. Ejecutamos el comando personalizado que creamos en console.php
            // Este comando ya tiene la lógica de --only-db, copiar al D y limpiar.
            Artisan::call('sistema:backup-windows');

            // 2. Obtenemos la salida del comando para mostrarla si quieres (opcional)
            $salida = Artisan::output();

            Notification::make()
                ->title('Backup Completado con Éxito')
                ->body('La base de datos se respaldó.')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            // Si algo falla, lo capturamos y te avisamos
            Notification::make()
                ->title('Error en el proceso de Backup')
                ->body('Detalle: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}