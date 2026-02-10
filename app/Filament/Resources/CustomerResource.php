<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationLabel = 'Clientes'; // Nombre en el menú lateral
    protected static ?string $modelLabel = 'Cliente';      // Nombre en singular (botón "Crear Cliente")
    protected static ?string $pluralModelLabel = 'Clientes'; // Nombre en plural

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->prefix('+51') // Muestra el +51 al usuario
                    ->maxLength(9)
                    ->required()
                    // FORMULARIO: Agrega el +51 antes de guardar en la base de datos
                    ->dehydrateStateUsing(fn ($state) => $state ? '+51' . $state : null)
                    // FORMULARIO: Quita el +51 al cargar el dato para que el usuario no lo vea en el cuadro
                    ->formatStateUsing(fn ($state) => str_replace('+51', '', $state)),
                Forms\Components\TextInput::make('email')
                    ->email(),
            ])
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

            // Nombre con búsqueda habilitada
            Tables\Columns\TextColumn::make('name')
                ->label('Nombre del Cliente')
                ->searchable()
                ->sortable()
                ->weight('bold'),

            // Teléfono con ícono de acción
           Tables\Columns\TextColumn::make('phone')
                ->label('WhatsApp')
                ->icon('heroicon-m-phone')
                ->searchable()
                // OPCIONAL: Si quieres que en la tabla se vea con espacios para leerlo mejor
                ->formatStateUsing(fn (string $state): string => 
                    str_replace('+51', '+51 ', $state)
                ),

            // Email
            Tables\Columns\TextColumn::make('email')
                ->label('Correo Electrónico')
                ->icon('heroicon-m-envelope')
                ->searchable(),

            // Columna calculada: ¿Cuántos equipos tiene este cliente?
            Tables\Columns\TextColumn::make('devices_count')
                ->label('Equipos')
                ->counts('devices') // Esto requiere que tengas la relación hasMany en el Modelo
                ->badge()
                ->color('info'),

            // Fecha de registro
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de Registro')
                ->dateTime('d/m/Y g:i A') // Esto mostrará algo como: 05/01/2026 8:08 PM
                ->timezone('America/Lima') // Esto fuerza a que se muestre la hora de Perú
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Aquí podrías filtrar clientes que se registraron este mes
            Tables\Filters\Filter::make('created_at')
                ->form([
                    Forms\Components\DatePicker::make('desde'),
                    Forms\Components\DatePicker::make('hasta'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['desde'], fn($q) => $q->whereDate('created_at', '>=', $data['desde']))
                        ->when($data['hasta'], fn($q) => $q->whereDate('created_at', '<=', $data['hasta']));
                })
        ])
        ->actions([
            Tables\Actions\EditAction::make()
                ->iconButton()
                ->tooltip('Editar cliente'),
            Tables\Actions\DeleteAction::make()
                ->iconButton()
                ->tooltip('Eliminar cliente'),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
