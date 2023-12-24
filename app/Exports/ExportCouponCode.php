<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UserCouponCode;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportCouponCode implements FromQuery, WithHeadings
{
    use Exportable;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return UserCouponCode::query()
            ->leftJoin('users', 'users.id', '=', 'user_coupon_codes.user_id')
            ->join('coupon_codes', 'coupon_codes.id', '=', 'user_coupon_codes.coupon_code_id')
            ->where('coupon_code_group_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->select('user_coupon_codes.code', 'user_coupon_codes.credit', 'user_coupon_codes.price', 'user_coupon_codes.created_at', 'user_coupon_codes.date_of_use', DB::raw("CONCAT(users.name ,' ', users.surname) as username"), 'coupon_codes.name');
    }

    public function headings(): array
    {
        return ["Code", "Credit", "Price", "Created At", "Date of Use", "User Name", "Coupon Code Name"];
    }
}
