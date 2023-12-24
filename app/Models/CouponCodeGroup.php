<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCodeGroup extends Model
{
    protected $table = 'coupon_code_groups';

    protected $fillable = [
        'name',
        'quantity',
        'character_length',
        'prefix',
        'coupon_code_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'character_length' => 'integer',
    ];
}
