<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $table = 'booking_payments';
    protected $fillable = [
        'booking_id',
        'paymentIntent',
        'status',
        'last_message',
        'charge',
        'refund'
    ];
    public function booking()
    {
        return $this->hasOne(Booking::class, 'id', 'booking_id');
    }
}
