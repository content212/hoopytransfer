<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'role',
        'status',
        'push_enabled',
        'sms_enabled',
        'email_enabled',
        'push_title',
        'push_body',
        'sms_body',
        'email_subject',
        'email_body',
    ];

    protected $casts = [
        'push_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'email_enabled' => 'boolean',
    ];

}
