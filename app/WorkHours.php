<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkHours extends Model
{
    protected $fillable =
    [
        'group_id',
        '6_18',
        '6_18_day',
        '18_22',
        '18_22_day',
        '22_0',
        '22_0_day',
        '0_6',
        '0_6_day',
        'date',
        'excuse'
    ];
}
