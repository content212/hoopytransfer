<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingUserInfo extends Model
{
    protected $table = 'booking_user_infos';

    protected $fillable = [
        'booking_id',
        'name',
        'surname',
        'email',
        'phone',
        'country',
        'state',
        'company_name'
    ];
}
