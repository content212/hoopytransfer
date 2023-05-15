<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\User;


class DriverJobController extends Controller
{
    public function index(Request $request) 
    {
        $user = $request->user();

        $driver = Driver::where('user_id', '=', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookings = Booking::select('bookings.*','users.name','users.surname','users.phone')
        ->join('users', 'users.id', '=', 'bookings.user_id')
        ->where('bookings.driver_id','=',$driver->id)
        ->whereIn('bookings.status',array(2, 3))
        ->orderByRaw("STR_TO_DATE(CONCAT('booking_date',' ','booking_time'), '%d/%m/%Y %T')")
        ->get()
        ->map
        ->only('id','track_code', 'name','surname','phone','from_address','to_address','booking_date','booking_time','from_lat','from_lng','to_lat','to_lng','status','status_name');

        return response()->json($bookings);
    }

    public function updateBookingStatus($booking_id, $status_id) 
    {

    }
}
