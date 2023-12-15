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
        'validity',
    ];

    protected $casts = [
        'active' => 'boolean',
        'validity' => 'integer',
        'credit' => 'integer'
    ];
}
