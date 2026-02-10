<?php

namespace App\Filament\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables;

use App\Filament\Resources\RepairOrderResource\Pages;
use App\Filament\Resources\RepairOrderResource\RelationManagers;
use App\Models\RepairOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RepairOrderResource extends Resource
{
    protected static ?string $model = RepairOrder::class;

    protected static ?string $navigationLabel = 'Órdenes de Reparación';
    protected static ?string $modelLabel = 'Orden de Reparación';
    protected static ?string $pluralModelLabel = 'Órdenes de Reparación';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            
            Forms\Components\Select::make('device_id')
                ->label('Equipo / Cliente')
                ->relationship('device', 'model')
                ->searchable()
                ->preload()
                ->required()
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->model} ({$record->brand}) - {$record->customer->name}")
                ->getSearchResultsUsing(function (string $search) {
                    return \App\Models\Device::query()
                        ->where('model', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($device) => [
                            $device->id => "{$device->model} ({$device->brand}) - {$device->customer->name}"
                        ]);
                }) // <-- Fíjate aquí: DEBE SER UNA COMA, NO PUNTO Y COMA
                    ->getOptionLabelsUsing(fn (array $values): array => \App\Models\Device::whereIn('id', $values)
                    ->get()
                    ->mapWithKeys(fn ($device) => [
                        $device->id => "{$device->model} ({$device->brand}) - {$device->customer->name}"
                    ])->toArray()
                ), 


            Forms\Components\Textarea::make('issue')
                ->label('Falla reportada')
                ->required(),
            Forms\Components\Select::make('status')
                ->options([
                    //'Pendiente' => 'Pendiente',
                    'En Reparacion' => 'En Reparación',
                    'Listo' => 'Listo para entrega',
                    'Entregado' => 'Entregado',
                ])->default('Pendiente'),
            Forms\Components\TextInput::make('price')
                ->label('Precio / Costo')
                ->numeric()
                ->prefix('S/') // Cambiamos el $ por S/
                ->placeholder('0.00'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordClasses(fn (RepairOrder $record) => match ($record->status) {
            'En Reparacion' => 'bg-red-100/80 dark:bg-red-900/40',
            'Listo'          => 'bg-orange-100/80 dark:bg-orange-900/40',
            'Entregado'      => 'bg-green-100/80 dark:bg-green-900/40',
            default          => null,
        })

        ->columns([
            // Esta es la columna de numeración (Ítem)
            Tables\Columns\TextColumn::make('index')
                ->label('#')
                ->state(static function (Tables\Contracts\HasTable $livewire, $record) : string {
                    // Obtenemos el total de registros y le restamos la posición actual
                    return (string) (
                        $livewire->getTableRecords()->total() - 
                        (($livewire->getTableRecords()->currentPage() - 1) * $livewire->getTableRecords()->perPage()) - 
                        array_search($record->getKey(), $livewire->getTableRecords()->modelKeys())
                    );
                })
                ->alignCenter()
                ->grow(false),
                
            // Muestra el modelo del equipo
            Tables\Columns\TextColumn::make('device.model')
                ->description(fn (RepairOrder $record): string => $record->device->customer->name)
                ->label('Equipo / Cliente'),

            // Muestra la falla (limitada a 30 caracteres para que no ocupe mucho espacio)
            Tables\Columns\TextColumn::make('issue')
                ->label('Falla')
                ->limit(30),

            // El Estado con insignias de colores (Badges)
            Tables\Columns\TextColumn::make('status')
                ->label('Estado')
                ->weight('bold') // Para que la letra resalte más al ser solo color
                ->color(fn (string $state): string => match ($state) {
                    'En Reparacion' => 'danger',  // Rojo intenso
                    'Listo'         => 'warning', // Naranja/Ámbar intenso
                    'Entregado'     => 'success', // Verde intenso
                    default         => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'En Reparacion' => 'EN REPARACIÓN',
                    'Listo'         => 'LISTO PARA ENTREGA',
                    'Entregado'     => 'ENTREGADO',
                    default         => $state,
                }),
            
            Tables\Columns\TextColumn::make('price')
                ->label('Precio')
                ->formatStateUsing(fn ($state) => 'S/ ' . number_format($state, 2, '.', ','))
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de Ingreso')
                ->dateTime('d/m/Y g:i A') // Esto mostrará algo como: 05/01/2026 8:08 PM
                ->timezone('America/Lima') // Esto fuerza a que se muestre la hora de Perú
                ->sortable(),

        ])
        ->filters([
            // Filtro rápido para ver solo lo que está "Pendiente"
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    //'Pendiente' => 'Pendiente',
                    'En Reparacion' => 'En Reparación',
                    'Listo' => 'Listo',
                ]),
        ])
        
        ->actions([
            Tables\Actions\EditAction::make()
                ->iconButton()
                ->tooltip('Editar orden'),
            
            // Acción de WhatsApp
            Tables\Actions\Action::make('whatsapp')
                ->label('Avisar Cliente')
                ->icon('heroicon-m-chat-bubble-left-right')
                ->color('success')
                ->iconButton() // <--- Esta es la clave para que NO tenga label
                // Usamos url() en lugar de action() para evitar problemas de ejecución
                ->url(fn (RepairOrder $record): string => "https://wa.me/" . 
                    preg_replace('/[^0-9]/', '', $record->device->customer->phone) . 
                    "?text=" . urlencode(
                        "Hola " . $record->device->customer->name . 
                        ", tu " . $record->device->brand . " " . $record->device->model . 
                        " está en estado: *" . $record->status . "*" .
                        ($record->status === 'Listo' ? ". Ya puedes retirarlo. Costo: $" . $record->price : "")
                    )
                )
                ->openUrlInNewTab() // Abre en pestaña nueva
                ->visible(fn (RepairOrder $record): bool => in_array($record->status, ['Listo', 'Entregado'])),
            
            //Aqui para generar los tickets de PDF
            Tables\Actions\Action::make('download_ticket')
                ->label('Ticket PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->iconButton() // Solo icono
                ->action(function (RepairOrder $record) { // Aquí usamos el nombre tal cual está arriba
                    $pdf = Pdf::loadView('ticket', ['order' => $record]);
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, "Ticket-{$record->id}.pdf");
                }),
        ])

         // EL CIERRE DE LA TABLA Y EL SORT VAN AQUÍ ADENTRO:
        ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepairOrders::route('/'),
            'create' => Pages\CreateRepairOrder::route('/create'),
            'edit' => Pages\EditRepairOrder::route('/{record}/edit'),
        ];
    }
}
