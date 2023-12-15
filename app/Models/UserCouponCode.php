<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCouponCode extends Model
{
    protected $table = 'user_coupon_codes';

    protected $fillable = [
        'user_id',
        'coupon_code_id',
        'start_date',
        'expiration_date',
        'date_of_use',
        'credit',
        'code',
        'guid',
    ];
    protected $casts = [
        'credit' => 'integer',
        'start_date' => 'datetime',
        'expiration_date' => 'datetime',
        'date_of_use' => 'datetime',
    ];
}
