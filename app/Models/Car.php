<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    protected $fillable = [
        'plate',
        'type',
        'insurance_date',
        'inspection_date',
        'station_id'
    ];
}
