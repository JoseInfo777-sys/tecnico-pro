<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email'];

    public function devices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Device::class);
    }
}
