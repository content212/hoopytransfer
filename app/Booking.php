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
        'car_id',
        'car_type',
        'price_id',
        'km',
        'duration',
        'from',
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
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function data()
    {
        return $this->hasOne(BookingData::class, 'booking_id', 'id');
    }
}
