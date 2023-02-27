<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingData extends Model
{
    protected $table = 'booking_data';

    protected $fillable = [
        'booking_id',
        'km',
        'opening_fee',
        'km_fee',
        'discount_rate',
        'payment_type',
        'system_payment',
        'driver_payment',
        'total'
    ];

    public function booking()
    {
        return $this->hasOne(Booking::class, 'id', 'booking_id');
    }
}
