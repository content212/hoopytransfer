<?php

namespace App\Models;

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
        'discount_price',
        'payment_type',
        'system_payment',
        'driver_payment',
        'total',
        'paymentIntentSecret',
        'full_discount',
        'full_discount_price',
        'full_discount_system_payment'
    ];

    public function booking()
    {
        return $this->hasOne(Booking::class, 'id', 'booking_id');
    }
}
