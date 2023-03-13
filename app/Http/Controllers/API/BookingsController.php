<?php

namespace App\Http\Controllers\API;

use App\Models\Log;
use App\Models\Booking;
use App\Models\BookingUserInfo;
use Carbon\Carbon;
use App\BookingPackets;
use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Models\Transaction;
use App\Rules\InsuranceExp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Yajra\DataTables\Contracts\DataTable;

use function Symfony\Component\VarDumper\Dumper\esc;

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
            //DB::raw('(CASE bookings.status
            //when 0 then \'Waiting for Booking\'
            //when 1 then \'Trip is expected\'
            //when 2 then \'Waiting for Booking\'
            //when 3 then \'Trip is completed\'
            //when 4 then \'Trip is not Completed\'
            //when 5 then \'Canceled by Customer\'
            //when 6 then \'Canceled by System\'
            //when 7 then \'Completed\' END) as status'),
            'bookings.status as status',
            'bookings.track_code',
            'bookings.from',
            'bookings.from_name',
            'bookings.to',
            'bookings.to_name',
            DB::raw('(CASE
            WHEN bookings.user_id IS NULL THEN booking_user_infos.name
            ELSE users.name END ) as user_name'),
            'bookings.created_at'
        )
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'bookings.user_id')->whereNotNull('bookings.user_id');
            })
            ->leftJoin('booking_user_infos', 'booking_user_infos.booking_id', '=', 'bookings.id');
        if ($request->get('status') != '') {
            $bookings = $bookings->where('bookings.status', $request->get('status'));
        }
        return DataTables::of($bookings)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y') : '';
            })
            ->editColumn('status', function (Booking $booking) {
                return $booking->getStatus();
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    protected function generateRandomNumber($length)
    {
        $track_code = "HPT";
        srand((float) microtime() * 1000000);

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
            $input['car_type'] = Price::find($input['price_id'])->carType->id;
            $booking = Booking::create($input);
            $track_code = $this->generateRandomNumber(8);
            while (Booking::where('track_code', $track_code)->exists()) {
                $track_code = $this->generateRandomNumber(8);
            }
            $booking = Booking::where('id', $booking->id);
            $booking->update(['track_code' => $track_code]);
            $booking = $booking->first();
            Log::addToLog('Booking Log.', $request->all(), 'Create');
            return response($booking->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(int $booking)
    {
        if (!$bookings = Booking::where('id', '=', $booking)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        if ($bookings->user_id)
            $user = User::where('id', $bookings->user_id)->first();
        else
            $user = BookingUserInfo::where('booking_id', $bookings->id)->first();
        $bookings['status_name'] = $bookings->getStatus();
        $user = [
            'name' => $user->name . " " . $user->surname,
            'phone' => $user->phone,
            'email' => $user->email
        ];

        return response(json_encode(array_merge($user, $bookings->toarray())), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $booking)
    {
        if (!$bookings = Booking::where('id', '=', $booking)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
            $rules = array(
                'car_id' => [new InsuranceExp($booking)],
            );
            $messages = array();
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $messages = $validator->messages()->get('*');
                return response()->json([
                    'error' => $messages
                ], 400);
            }

            if ($request->post('driver_id') and $request->post('car_id') and $bookings->status == '1') {
                $request->merge(['status' => '2']);
                Transaction::where('booking_id', $bookings->id)->where('type', 'driver_wage')->first()->update([
                    'driver_id' => $request->post('driver_id')
                ]);
            }

            $bookings->update($request->all());
            Log::addToLog('Booking Log.', $request->all(), 'Edit');
            return response($bookings->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
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
            if (Carbon::parse($booking->booking_date . " " . $booking->booking_time)->diffInHours(now()) < $booking->service->free_cancellation or !in_array($booking->status, [0, 1, 2, 8])) {
                return response()->json(['message' => 'You can not cancel this booking!'], 400);
            }
            $refundRequest = new Request([
                'payment_intent' => $booking->payment->paymentIntent
            ]);
            if (((new PaymentController)->refund($refundRequest))->status() == 200) {
                $booking->update([
                    'status' => $user->role->role == 'customer' ? 3 : 4
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
                    'track_code'    => $booking->track_code,
                    'sender_name'   => $booking->sender_name != '' ? $this->private_str($booking->sender_name, 3, strlen($booking->sender_name) - 1) : '',
                    'company_name'  => $booking->company_name != '' ? $this->private_str($booking->company_name, 3, strlen($booking->company_name) - 1) : '',
                    'status'        => $booking->status
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
            DB::raw('sum(booking_packets.price) as total_price'),
            'from_name',
            'to_name',
            DB::raw('(CASE status
        when 0 then \'Waiting for Booking\'
        when 1 then \' Trip is expected\'
        when 2 then \'Waiting for Confirmation\'
        when 3 then \'Trip is completed\'
        when 4 then \'Trip is not Completed\'
        when 5 then \'Canceled by Customer\'
        when 6 then \'Canceled by System\'
        when 7 then \'Completed\' END) as status'),
        )
            ->join('booking_packets', 'booking_packets.bookingId', '=', 'bookings.id')
            ->where('bookings.user_id', $user->id)
            ->groupBy('bookings.id')
            ->get();

        return $bookings;
    }
    public function FrontEndCustomerBookingsDetail($id)
    {
        if (!$booking = Booking::select(
            'bookings.*',
            DB::raw('(CASE status
        when 0 then \'Waiting for Booking\'
        when 1 then \' Trip is expected\'
        when 2 then \'Waiting for Confirmation\'
        when 3 then \'Trip is completed\'
        when 4 then \'Trip is not Completed\'
        when 5 then \'Canceled by Customer\'
        when 6 then \'Canceled by System\'
        when 7 then \'Completed\' END) as status'),
        )->where('id', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        $user = Auth::user();
        $packets = BookingPackets::where('bookingId', $booking->id)->get();

        if ($booking->user_id == $user->id) {
            return response()->json(['booking' => $booking, 'packets' => $packets], 200);
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
        if ($request->user('api')->role->role  == 'driver') {
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
}
