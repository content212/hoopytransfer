<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'license_date',
        'license_class',
        'license_no',
        'country',
        'state',
        'address'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'driver_id', 'id');
    }
}
