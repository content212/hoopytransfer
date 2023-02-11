<?php

namespace App;

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
}
