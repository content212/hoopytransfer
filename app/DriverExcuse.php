<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverExcuse extends Model
{
    protected $fillable =
    [
        'driver_id',
        'month',
        'year',
        'group_id'
    ];
}
