<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'car_id',
        'license_date',
        'license_class',
        'license_no',
        'country',
        'state',
        'address'
    ];
}
