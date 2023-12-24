<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCouponCode extends Model
{
    protected $table = 'user_coupon_codes';

    protected $fillable = [
        'user_id',
        'coupon_code_id',
        'coupon_code_group_id',
        'date_of_use',
        'credit',
        'code',
        'price',
        'guid',
        'is_gift'
    ];
    protected $casts = [
        'credit' => 'float',
        'price' => 'float',
        'date_of_use' => 'datetime',
        'is_gift' => 'boolean'
    ];
}
