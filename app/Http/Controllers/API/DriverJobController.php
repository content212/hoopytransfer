<?php

namespace App\Http\Controllers\API;

use App\Helpers\BookingHelper;
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
        ->get()
        ->map
        ->only('id','track_code', 'name','surname','phone','from_address','to_address','booking_date','booking_time','from_lat','from_lng','to_lat','to_lng','status','status_name');

        //->orderByRaw("STR_TO_DATE(CONCAT('booking_date',' ','booking_time'), '%d/%m/%Y %T')")

        return response()->json($bookings);
    }

    public function detail($id,Request $request)
    {
        $user = $request->user();

        $driver = Driver::where('user_id', '=', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookings = Booking::select('bookings.*','users.name','users.surname','users.phone')
        ->join('users', 'users.id', '=', 'bookings.user_id')
        ->where('bookings.driver_id','=',$driver->id)
        ->where('bookings.id','=', $id)
        ->get()
        ->map
        ->only('id','track_code', 'name','surname','phone','from_address','to_address','booking_date','booking_time','from_lat','from_lng','to_lat','to_lng','status','status_name');

        $booking = $bookings->first();

        if(!$booking)
        {
            return response()->json(['message' => 'NotFound'], 404);
        }

        return response()->json($booking);
    }

    public function tripIsStarted($booking_id, Request $request)
    {
        $user = $request->user();

        $driver = Driver::where('user_id', '=', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::where('id', '=', $booking_id)
        ->where('status','=','2') // Eğer durum sadece Trip is expected ise sürüş başlayabilir.
        ->where('driver_id','=',$driver->id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Bad request!'], 400);
        }
        BookingHelper::SetBookingStatus($booking,0,0,0,0,3);
        return response()->json(['message' => 'Update success.'], 200);
    }

    public function tripIsCompleted($booking_id, Request $request)
    {
        $user = $request->user();

        $driver = Driver::where('user_id', '=', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::where('id', '=', $booking_id)
        ->where('status','=','3') // Eğer durum sadece Trip is started ise sürüş tamamlanabilir.
        ->where('driver_id','=',$driver->id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Bad request!'], 400);
        }
        BookingHelper::SetBookingStatus($booking,0,0,0,0,6);
        return response()->json(['message' => 'Update success.'], 200);
    }

    public function tripIsNotCompleted($booking_id, Request $request)
    {
        $user = $request->user();

        $driver = Driver::where('user_id', '=', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::where('id', '=', $booking_id)
        ->where('status','=','3') // Eğer durum sadece Trip is started ise sürüş tamamlanabilir.
        ->where('driver_id','=',$driver->id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Bad request!'], 400);
        }
        BookingHelper::SetBookingStatus($booking,0,0,0,0,7);
        return response()->json(['message' => 'Update success.'], 200);
    }
}
