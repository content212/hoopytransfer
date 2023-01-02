<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkNote extends Model
{
    protected $fillable =
    [
        'group_id',
        'note',
        'admin_note'
    ];
}
