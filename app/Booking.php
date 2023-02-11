<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'status',
        'user_id',
        'track_code',
        'driver_id',
        'car_type',
        'from',
        'km',
        'duration',
        'from_name',
        'from_address',
        'from_lat',
        'from_lng',
        'to',
        'to_name',
        'to_address',
        'to_lat',
        'to_lng',
        'booking_date',
        'booking_time'
    ];
    public static function getCount($status)
    {
        $count = Booking::where('status', $status)->count();
        return $count;
    }
}
