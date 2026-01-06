<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ESTA ES LA LÍNEA CRÍTICA: Asegúrate que diga exactamente esto
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class Device extends Model
{
    // Esto permite que Filament guarde los datos
    protected $fillable = ['customer_id', 'type', 'brand', 'model', 'serial_number'];

    // Esta es la relación que te está pidiendo el Select
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
