<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkInput extends Model
{
    protected $fillable =
    [
        'user_id',
        'group_id',
        'type',
        'datetime'
    ];
}
