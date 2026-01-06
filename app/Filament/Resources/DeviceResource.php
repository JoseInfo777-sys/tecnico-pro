<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Filament\Resources\DeviceResource\RelationManagers;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationLabel = 'Equipos';
    protected static ?string $modelLabel = 'Equipo';
    protected static ?string $pluralModelLabel = 'Equipos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name') // Vincula con el nombre del cliente
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('type')->label('Tipo (PC, Celular)')->required(),
            Forms\Components\TextInput::make('brand')->label('Marca')->required(),
            Forms\Components\TextInput::make('model')->label('Modelo')->required(),
            Forms\Components\TextInput::make('serial_number')->label('N° de Serie'),
        ]);
    }

    public static function table(Table $table): Table
    {
       return $table
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

            // Mostramos el Cliente (dueño)
            Tables\Columns\TextColumn::make('customer.name')
                ->label('Dueño')
                ->searchable()
                ->sortable(),

            // Tipo de equipo con un ícono según el texto
            Tables\Columns\TextColumn::make('type')
                ->label('Tipo')
                ->badge()
                ->color('gray')
                ->icon(fn (string $state): string => match (strtolower($state)) {
                    'laptop' => 'heroicon-m-computer-desktop',
                    'celular', 'iphone', 'android' => 'heroicon-m-device-phone-mobile',
                    'tablet' => 'heroicon-m-device-tablet',
                    default => 'heroicon-m-wrench',
                }),

            Tables\Columns\TextColumn::make('brand')
                ->label('Marca')
                ->searchable(),

            Tables\Columns\TextColumn::make('model')
                ->label('Modelo')
                ->searchable(),

            Tables\Columns\TextColumn::make('serial_number')
                ->label('S/N')
                ->copyable()
                ->placeholder('Sin serie'),

            // Fecha en que se registró el equipo
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de Registro')
                ->dateTime('d/m/Y g:i A') // Esto mostrará algo como: 05/01/2026 8:08 PM
                ->timezone('America/Lima') // Esto fuerza a que se muestre la hora de Perú
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Filtro por marca
            Tables\Filters\SelectFilter::make('brand')
                ->label('Filtrar por Marca')
                ->options([
                    'Samsung' => 'Samsung',
                    'Apple' => 'Apple',
                    'HP' => 'HP',
                    'Dell' => 'Dell',
                ]),
        ])
        ->actions([
            Tables\Actions\EditAction::make()
                ->iconButton()
                ->tooltip('Editar cliente'),
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
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
