<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPackets extends Model
{
    protected $fillable = [
        'bookingId',
        'cubic_meters',
        'kg',
        'type',
        'price',
        'discount',
        'discount_rate',
        'tax_rate',
        'tax',
        'final_price'
    ];
}
