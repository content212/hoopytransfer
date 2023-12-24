<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    protected $table = 'coupon_codes';

    protected $fillable = [
        'name',
        'active',
        'credit',
        'price',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'float',
        'credit' => 'float'
    ];
}
