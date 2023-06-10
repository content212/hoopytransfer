<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';

    protected $fillable = [
        'shift_date',
        'queue',
        'driver_id',
        'isAssigned',
        'booking_id'
    ];
}
