<?php

namespace App\Http\Controllers\API;

use App\BookingPackets;
use App\Helpers\BookingHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingData;
use App\Models\BookingService;
use App\Models\CarType;
use App\Models\Driver;
use App\Models\Log;
use App\Models\Price;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\UserContract;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class BookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bookings = Booking::select(
            'bookings.id',
            'bookings.status as status',
            'bookings.track_code',
            'bookings.from',
            'bookings.from_name',
            'bookings.to',
            'bookings.to_name',
            DB::raw('(CASE
            WHEN bookings.user_id IS NULL THEN booking_user_infos.name
            ELSE users.name END ) as user_name'),
            DB::raw("CONCAT(u.name ,' ', u.surname) as driver_name"),
            'bookings.created_at'
        )->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'bookings.user_id')->whereNotNull('bookings.user_id');
        })

            ->leftJoin('booking_user_infos', 'booking_user_infos.booking_id', '=', 'bookings.id')
            ->leftJoin('drivers as d', function ($join) {
                $join->on('d.id', '=', 'bookings.driver_id');
            })
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'd.user_id');
            });

        if ($request->get('status') != '') {
            $bookings = $bookings->where('bookings.status', $request->get('status'));
        }

        return DataTables::of($bookings)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y H:i:s') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    protected function generateRandomNumber($length): string
    {
        $track_code = "HPT";
        srand((float)microtime() * 1000000);

        $data = "123456123456789071234567890890";

        for ($i = 0; $i < $length; $i++) {
            $track_code .= substr($data, (rand() % (strlen($data))), 1);
        }
        return $track_code;
    }

    public function store(Request $request)
    {
        try {
            if ($user = $request->user('api'))
                $user_id = $user->id;
            $input = $request->all();
            $input['user_id'] = $user_id;
            $input['car_type'] = $input['price_id'] != -1 ? Price::find($input['price_id'])->carType->id : null;
            $input['status'] = $input['price_id'] != -1 ? 0 : 9;
            $input['booking_date'] = date('Y-m-d', strtotime($input['booking_date']));
            $booking = Booking::create($input);
            $track_code = $this->generateRandomNumber(8);
            while (Booking::where('track_code', $track_code)->exists()) {
                $track_code = $this->generateRandomNumber(8);
            }
            $booking->update(['track_code' => $track_code]);

            if ($input['car_type'] != null) {
                $car_type = CarType::find($input['car_type']);
                $booking_service = BookingService::create(array(
                    'booking_id' => $booking->id,
                    'name' => $car_type->name,
                    'image' => $car_type->image,
                    'person_capacity' => $car_type->person_capacity,
                    'baggage_capacity' => $car_type->baggage_capacity,
                    'free_cancellation' => $car_type->free_cancellation
                ));
            }

            if ($booking->price_id != -1) {
                $price = Price::find($booking->price_id);
                $full_discount = Setting::firstWhere('code', 'full_discount')->value ?? 0;
                $total = ($price->opening_fee + ($price->km_fee * $booking->km));
                $discount_price = $total * (1.0 - ($price->carType->discount_rate / 100.0));
                $driver_payment = $discount_price * 0.7;
                $system_payment = $discount_price - $driver_payment;
                $full_discount_price = $discount_price * (1.0 - ($full_discount / 100.0));
                $inputs = [
                    'booking_id' => $booking->id,
                    'km' => $booking->km,
                    'opening_fee' => $price->opening_fee,
                    'km_fee' => $price->km_fee,
                    'discount_rate' => $price->carType->discount_rate,
                    'discount_price' => $discount_price,
                    'system_payment' => $system_payment,
                    'driver_payment' => $driver_payment,
                    'total' => $total,
                    'full_discount' => $full_discount,
                    'full_discount_price' => $full_discount_price,
                    'full_discount_system_payment' => $system_payment - (($discount_price * ($full_discount / 100.0)) * 0.3),
                    'full_discount_driver_payment' => $driver_payment - (($discount_price * ($full_discount / 100.0)) * 0.7)
                ];

            } else {
                $inputs = [
                    'booking_id' => $booking->id,
                    'km' => $booking->km,
                    'opening_fee' => 0,
                    'km_fee' => 0,
                    'discount_rate' => 0,
                    'discount_price' => 0,
                    'system_payment' => 0,
                    'driver_payment' => 0,
                    'total' => 0,
                    'full_discount' => 0,
                    'full_discount_price' => 0,
                    'full_discount_system_payment' => 0,
                    'full_discount_driver_payment' => 0
                ];
            }

            $booking_data = new BookingData($inputs);
            $booking->data()->save($booking_data);
            $booking->load('data');
            Log::addToLog('Booking Log.', $request->all(), 'Create');
            return response($booking->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function show(int $booking)
    {
        $user = Auth::user();
        if (!$bookings = Booking::where('id', '=', $booking)
            ->with('service', 'data', 'user')
            ->first())
            return response()->json(['message' => 'Not Found!'], 404);
        if ($bookings['status'] == 9) {
            $bookings['car_type'] = 'request';
        }

        return response($bookings->toJson(JSON_PRETTY_PRINT), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $booking)
    {
        if (!$bookings = Booking::where('id', '=', $booking)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {

            $carType = intval($request->post('car_type'));
            $carId = intval($request->post('car_id'));
            $driverId = intval($request->post('driver_id'));
            $price = doubleval($request->post('price'));


            /*
            if ($request->post('driver_id') and $request->post('car_id') and $bookings->status == '1') {
                $request->merge(['status' => '2']);
                $total = Transaction::where('driver_id', $request->post('driver_id'))
                    ->get()
                    ->sum(function ($transaction) {
                        return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                    });
                $transaction = Transaction::where('booking_id', $bookings->id)->where('type', 'driver_wage')->first();
                $transaction->update([
                    'driver_id' => $request->post('driver_id'),
                    'balance' => ($total - $transaction->amount)
                ]);
            }
            */

            BookingHelper::SetBookingStatus($bookings, $carType, $carId, $driverId, $price, $bookings->status);

            Log::addToLog('Booking Log.', $request->all(), 'Edit');
            return response($bookings->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $booking)
    {
        if (!$bookings = Booking::where('id', '=', $booking)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $bookings->delete();
            Log::addToLog('Booking Log.', $bookings, 'Delete');
            return response()->json(['message' => 'Deleted!'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function cancel(Request $request, $booking_id)
    {
        try {
            $user = $request->user('api');
            $booking = Booking::firstWhere('id', $booking_id);
            if (!$booking)
                return response()->json(['message' => 'Not Found!'], 404);
            if ($user->role->role == 'customer' and $booking->user_id != $user->id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            if (Carbon::parse($booking->booking_date . " " . $booking->booking_time)->diffInHours(now()) < $booking->service->free_cancellation or !in_array($booking->status, [0, 1, 2, 9])) {
                return response()->json(['message' => 'You can not cancel this booking. Cancellable time has expired.'], 400);
            }
            $refundRequest = new Request([
                'payment_intent' => $booking->payment->paymentIntent
            ]);
            if (((new PaymentController)->refund($refundRequest))->status() == 200) {
                $booking->update([
                    'status' => $user->role->role == 'customer' ? 4 : 5
                ]);
                //iptal işlemi yapıldı. para iadesi yapıldı.
                //bildirim vs. gönderilecek.
                BookingHelper::SetBookingStatus($booking, 0, 0, 0, 0, $booking->status);

            }
            $shift = Shift::where('shift_date', '=', $booking->booking_date)
                ->where('isAssigned', '=', true)
                ->where('driver_id', '=', $booking->driver_id)
                ->where('booking_id', '=', $booking->id)
                ->first();
            if ($shift) {
                $shift->update([
                    'isAssigned' => false,
                    'booking_id' => null
                ]);
            }
            Log::addToLog('Booking Log.', $request->all(), 'Cancel');
            return response()->json([
                'message' => 'Booking canceled successfully'
            ]);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    private function private_str($str, $start, $end)
    {
        $after = mb_substr($str, 0, $start, 'utf8');
        $repeat = str_repeat('*', $end);
        $before = mb_substr($str, ($start + $end), strlen($str), 'utf8');
        return $after . $repeat . $before;
    }

    public function track(Request $request)
    {
        $booking = Booking::select(
            'sender_name',
            DB::raw('(CASE status when 0 then \'Waiting\' when 1 then \'Preparing\' when 2 then \'Shipped\' when 3 then \'Trip is not Completed\' END) as status'),
            'track_code',
            'company_name',
        )
            ->where('track_code', $request->track_code)->first();
        if ($booking) {
            return response()->json(
                [
                    'track_code' => $booking->track_code,
                    'sender_name' => $booking->sender_name != '' ? $this->private_str($booking->sender_name, 3, strlen($booking->sender_name) - 1) : '',
                    'company_name' => $booking->company_name != '' ? $this->private_str($booking->company_name, 3, strlen($booking->company_name) - 1) : '',
                    'status' => $booking->status
                ],
                200
            );
        } else {
            return response()->json(['message' => 'Not Found!'], 404);
        }
    }

    public function getBookingsCount($status)
    {
        return Booking::getCount($status);
    }

    public function FrontEndCustomerBookings()
    {
        $user = Auth::user();
        $bookings = Booking::select(
            'bookings.id',
            'track_code',
            'bookings.created_at',
            'from_name',
            'to_name',
            'status',
            'booking_date',
            'car_type',
        )
            ->with('service:id,free_cancellation', 'data')
            #->addSelect(['free_cancellation' => CarType::select('free_cancellation')])
            ->where('bookings.user_id', $user->id)
            ->orWhere('bookings.other_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map
            ->only('id', 'track_code', 'created_at', 'from_name', 'to_name', 'booking_date', 'status_name', 'status', 'service', 'data', 'payment_status');

        return $bookings;
    }

    public function FrontEndCustomerBookingsDetail($id)
    {
        if (!$booking = Booking::where('id', $id)
            ->with('service', 'data', 'user')
            ->first())
            return response()->json(['message' => 'Not Found!'], 404);

        $user = Auth::user();

        if ($booking->user_id == $user->id || $booking->other_user_id == $user->id) {

            $bookingContracts = UserContract::where('booking_id', '=', $booking->id)->select('id', 'name')->get();

            $booking['contracts'] = $bookingContracts;

            return response()->json($booking, 200);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }

    private function random_color_part()
    {
        return str_pad(dechex(mt_rand(50, 150)), 2, '0', STR_PAD_LEFT);
    }

    private function random_color()
    {
        return "#" . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }

    public function calendarEvents(Request $request)
    {

        $bookings = Booking::whereDate('booking_date', '>=', $request->start)
            ->whereDate('booking_date', '<=', $request->end)
            ->where('status', '=', 2);
        if ($request->user('api')->role->role == 'driver') {
            $bookings = $bookings->where('driver_id', '=', Driver::firstWhere('user_id', $request->user('api')->id)->id);
        }
        $bookings = $bookings->get();
        $data = [];
        foreach ($bookings as $booking) {

            array_push($data, [
                'id' => $booking->id,
                'title' => Carbon::parse($booking->booking_time)->format('h:i') . " - " . $booking->id . " - " . $booking->user->name . " " . $booking->user->surname,
                'start' => $booking->booking_date,
                'backgroundColor' => $this->random_color(),
                'allDay' => true
            ]);
        }
        return response()->json($data);
    }

    public function timeRule(Request $request)
    {
        $time_rule = Setting::firstWhere('code', 'booking_time')->value;
        return $time_rule;
    }
}
