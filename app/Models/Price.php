<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'car_type',
        'start_km',
        'finish_km',
        'opening_fee',
        'km_fee'
    ];
    public function carType()
    {
        return $this->belongsTo(CarType::class, 'car_type', 'id');
    }
}
