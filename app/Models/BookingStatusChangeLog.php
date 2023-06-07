<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStatusChangeLog extends Model
{
    public $timestamps = false;
    protected $table = 'booking_status_change_logs';
    protected $fillable = [
        'booking_id',
        'user_id',
        'status'
    ];
}
