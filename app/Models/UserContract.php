<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContract extends Model
{
    protected $table = 'user_contracts';

    protected $fillable = [
        'user_id',
        'contract_id',
        'name',
        'contract',
        'booking_id'
    ];
}
