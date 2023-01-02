<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    protected $table = 'companies';

    protected $fillable = ['customer_id', 'company_name', 'tax_department', 'tax_number', 'organization_number', 'address'];
}
