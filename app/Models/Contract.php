<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';

    protected $fillable = [
        'name',
        'prefix',
        'suffix',
        'contract',
        'position',
        'active',
        'display_order',
        'required',
        'selected'
    ];
    
    protected $casts = [
        'selected' => 'boolean',
        'active' => 'boolean',
        'required' => 'boolean',
    ];

}
