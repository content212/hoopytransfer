<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkLager extends Model
{
    protected $fillable =
    [
        'group_id',
        'lager_id'
    ];
}
