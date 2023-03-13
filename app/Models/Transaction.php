<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'driver_id',
        'booking_id',
        'type',
        'amount',
        'balance',
        'note'
    ];
    public function driver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }
}
