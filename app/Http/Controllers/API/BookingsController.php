<?php

namespace App\Http\Controllers\API;

use App\Log;
use App\Price;
use App\Booking;
use App\BookingPacketDetails;
use Carbon\Carbon;
use App\BookingPackets;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
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
            'id',
            DB::raw('(CASE status
            when 0 then "Waiting for confirmation"
            when 1 then " Order confirmed"
            when 2 then "To be delivered"
            when 3 then "Will be delivered"
            when 4 then "Delivered"
            when 5 then "Cancelled"
            when 6 then "Rejected" END) as status'),
            'track_code',
            'from',
            'from_name',
            'to',
            'to_name',
            'sender_name',
            'customer_name',
            'created_at'

        );
        if ($request->get('status') != '') {
            $bookings = $bookings->where('status', $request->get('status'));
        }
        return DataTables::of($bookings)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="edit m-1 btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? with(new Carbon($row->created_at))->format('d/m/Y') : '';
            })
            ->rawColumns(['edit'])
            ->make(true);
    }

    public function getPacketsDatatable($id, $type)
    {
        $packets = BookingPackets::select('id', 'price', 'discount', 'tax', 'final_price')->where('bookingId', $id)->where('type', $type)->first();
        $packet_id = -1;
        $price = 0;
        $discount = 0;
        $tax = 0;
        $final_price = 0;
        if ($packets) {
            $packet_id = $packets->id;
            $price = $packets->price;
            $discount = $packets->discount;
            $tax = $packets->tax;
            $final_price = $packets->final_price;
        }
        $details = BookingPacketDetails::select('id', 'width', 'size', 'height', 'weight')->where('packet_id', $packet_id);

        return DataTables::of($details)
            ->addColumn('edit', function ($row) {
                $btn = '<a data-id="' . $row->id . '" class="packet-edit booking-form m-1 btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->with('subtotal', $price)
            ->with('discount', $discount)
            ->with('tax', $tax)
            ->with('total', $final_price)
            ->with('packet_id', $packet_id)
            ->rawColumns(['edit'])
            ->make();
    }
    public function getPacket($id)
    {
        if (!$packet = BookingPackets::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($packet->toJson(JSON_PRETTY_PRINT), 200);
    }
    public function getPacketDetail($id)
    {
        if (!$packet = BookingPacketDetails::where('id', '=', $id)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($packet->toJson(JSON_PRETTY_PRINT), 200);
    }

    public function packetsDetailUpdate(Request $request)
    {
        if (!$packetdetail = BookingPacketDetails::where('id', '=', $request->id)->first())
            return response()->json(['message' => 'Not Found!'], 404);

        $input = $request->all();
        try {
            $packetdetail->update($input);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Database error. Code:' . $e->getCode()], 400);
        }
        $packetdetails = BookingPacketDetails::select('cubic_meters', 'weight')->where('packet_id', $packetdetail->packet_id)->where('type', $packetdetail->type)->get();
        $cubic_meters = 0;
        $kg = 0;
        foreach ($packetdetails as $detail) {
            $cubic_meters += $detail->cubic_meters;
            $kg += $detail->weight;
        }
        $packet = BookingPackets::where('id', $packetdetail->packet_id)->first();
        $booking = Booking::where('id', $packet->bookingId)->first();
        $price = Price::where('zip_code', $booking->from)->first();
        $user = User::where('id', $booking->user_id)->first();
        $userType = null;
        if ($booking->user_type)
            $userType = $booking->user_type;
        $item = new stdClass;
        $item->id = 1;
        $item->type = $packetdetail->type;
        $item->cubic_meters = $cubic_meters;
        $item->kg = $kg;
        $lastdata = app('App\Http\Controllers\API\PriceCalculateController')->calculatePrice($item, $price, (int)$booking->km, $booking->delivery_type, $user, $userType);

        $packet->update(
            [
                'cubic_meters'  => $cubic_meters,
                'kg'            => $kg,
                'price'         => $lastdata['price'],
                'tax_rate'      => $lastdata['tax_rate'],
                'tax'           => $lastdata['tax'],
                'discount'      => $lastdata['discount'],
                'discount_rate' => $lastdata['discount_rate'],
                'final_price'   => $lastdata['final_price']
            ]
        );
        return response()->json(['ok'], 200);
    }

    public function packetUpdate(Request $request)
    {
        $subtotal = $request->price;
        $d_rate = $request->discount_rate;
        $t_rate = $request->tax_rate;

        $discount = ($subtotal * $d_rate) / 100.00;
        $tax = (($subtotal - $discount) * $t_rate) / 100.00;

        $total = $subtotal - $discount + $tax;

        $data = array(
            'price'         => $subtotal,
            'discount_rate' => $d_rate,
            'discount'      => '-' . $discount,
            'tax_rate'      => $t_rate,
            'tax'           => $tax,
            'final_price'   => $total
        );
        try {
            BookingPackets::where('id', $request->id)
                ->update($data);
            Log::addToLog('Packet Log.', $request->all(), 'Edit');
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    protected function generateRandomNumber($length)
    {
        $track_code = "NB";
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
            $user_id = null;
            if ($user = $request->user('api'))
                $user_id = $user->id;
            $input = $request->all();
            $input['user_id'] = $user_id;
            $booking = Booking::create($input);
            $track_code = $this->generateRandomNumber(8);
            while (Booking::where('track_code', $track_code)->exists()) {
                $track_code = $this->generateRandomNumber(8);
            }
            $booking = Booking::where('id', $booking->id);
            $booking->update(['track_code' => $track_code]);
            $booking = $booking->first();
            $price = Price::where('zip_code', $request->from)->first();
            $list = json_decode($request->list);
            $userType = null;
            if ($request->user_type)
                $userType = $request->user_type;

            foreach ($list as $item) {
                $lastdata = app('App\Http\Controllers\API\PriceCalculateController')->calculatePrice($item, $price, (int)$request->km, $request->delivery_type, $request->user('api'), $userType);
                $packet = BookingPackets::create(
                    [
                        'bookingId'     => $booking->id,
                        'cubic_meters'  => $item->cubic_meters,
                        'kg'            => $item->kg,
                        'type'          => $item->type,
                        'price'         => $lastdata['price'],
                        'tax_rate'      => $lastdata['tax_rate'],
                        'tax'           => $lastdata['tax'],
                        'discount'      => $lastdata['discount'],
                        'discount_rate' => $lastdata['discount_rate'],
                        'final_price'   => $lastdata['final_price']
                    ]
                );
                $details = json_decode($item->details);
                foreach ($details as $detail) {
                    BookingPacketDetails::create(
                        [
                            'packet_id'     => $packet->id,
                            'type'          => $detail->type,
                            'size'          => $detail->size,
                            'height'        => $detail->height,
                            'weight'        => $detail->weight,
                            'cubic_meters'  => $detail->cubic_meters
                        ]
                    );
                }
            }
            Log::addToLog('Booking Log.', $request->all(), 'Create');
            return response($booking->toJson(JSON_PRETTY_PRINT), 200);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(int $booking)
    {
        if (!$bookings = Booking::where('id', '=', $booking)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        return response($bookings->toJson(JSON_PRETTY_PRINT), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $booking)
    {
        if (!$bookings = Booking::where('id', '=', $booking)->first())
            return response()->json(['message' => 'Not Found!'], 404);
        try {
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
     * @param  \App\Booking  $booking
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
            DB::raw('(CASE status when 0 then "Waiting" when 1 then "Preparing" when 2 then "Shipped" when 3 then "Delivered" END) as status'),
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
        return Booking::where('status', $status)->count();
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
        when 0 then "Waiting for confirmation"
        when 1 then " Order confirmed"
        when 2 then "To be delivered"
        when 3 then "Will be delivered"
        when 4 then "Delivered"
        when 5 then "Cancelled"
        when 6 then "Rejected" END) as status')
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
        when 0 then "Waiting for confirmation"
        when 1 then " Order confirmed"
        when 2 then "To be delivered"
        when 3 then "Will be delivered"
        when 4 then "Delivered"
        when 5 then "Cancelled"
        when 6 then "Rejected" END) as status')
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
}
