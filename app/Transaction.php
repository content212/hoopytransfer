<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'driver_id',
        'type',
        'amount',
        'balance',
        'note'
    ];
}
