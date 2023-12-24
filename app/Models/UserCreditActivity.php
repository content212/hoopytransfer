<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCreditActivity extends Model
{
    protected $table = 'user_credit_activities';

    protected $fillable = [
        'user_id',
        'coupon_code_id',
        'booking_id',
        'credit',
        'note',
        'note2',
        'activity_type',
        'payment_intent',
        'payment_status',
        'refund',
        'is_gift'
    ];
    protected $casts = [
        'credit' => 'float',
        'is_gift' => 'boolean'
    ];
}
