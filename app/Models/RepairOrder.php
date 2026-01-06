<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ASEGÚRATE DE QUE ESTA LÍNEA ESTÉ EXACTAMENTE ASÍ:
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class RepairOrder extends Model
{

    protected $fillable = ['device_id', 'issue', 'diagnosis' ,'status', 'price'];

    // Esta es la función que Filament busca al usar ->relationship('device', 'model')
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

}
