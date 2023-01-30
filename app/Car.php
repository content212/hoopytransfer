<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    protected $fillable = ['plate', 'type', 'person_capacity', 'baggage_capacity', 'insurance_date', 'inspection_date', 'station_id'];
}
