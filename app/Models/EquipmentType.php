<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_mask'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
