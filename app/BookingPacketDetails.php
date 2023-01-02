<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPacketDetails extends Model
{
    protected $fillable = [
        'packet_id',
        'type',
        'width',
        'size',
        'height',
        'weight',
        'cubic_meters'
    ];
}
