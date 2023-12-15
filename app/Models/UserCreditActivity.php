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
        'activity_type',
    ];
    protected $casts = [
        'credit' => 'integer'
    ];
}
