<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingData;
use App\Models\BookingPayment;
use App\Models\Price;
use App\Models\Setting;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function index(Request $request)
    {
        $input = $request->all();
        if (!isset($input['bookingId']) or !isset($input['paymentType']) or ($input['paymentType'] != 'Full' and $input['paymentType'] != 'Pre'))
            abort(404);
        $email = Booking::find($input['bookingId'])->user->email;
        return view('payment')->with(['email' => $email, 'bookingId' => $input['bookingId']]);
    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bookingId' => 'required|exists:bookings,id',
            'paymentType' => 'required'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->get('*');
            return response()->json([
                'error' => $messages
            ], 400);
        }


        try {
            $data = $request->all();
            $booking = Booking::find($data['bookingId']);
            if (!in_array($booking->status, [0, 9]))
                return response()->json([
                    'error' => 'Payment not created for this booking!'
                ]);

            if ($data['paymentType'] == 'No') {
                $user = $request->user('api');
                if (!$user || $user->role->role != "admin") {
                    abort(403);
                }
                BookingPayment::create([
                    'booking_id' => $booking->id,
                    'paymentIntent' => "NO_PAYMENT",
                    'status' => "succeeded"
                ]);
                $booking_data = $booking->data;
                Transaction::create([
                    'type' => 'booking_payment',
                    'amount' => $booking_data->payment_type == 'Full' ? $booking_data->full_discount_price : $booking_data->system_payment,
                    'note' => 'Payment from #' . $booking_data->booking->id . ' booking.',
                    'booking_id' => $booking_data->booking->id
                ]);
                Transaction::create([
                    'type' => 'driver_wage',
                    'amount' => $booking_data->payment_type == 'Full' ? $booking_data->full_discount_driver_payment : $booking_data->driver_payment,
                    'balance' => 0,
                    'note' => 'Wage for #' . $booking_data->booking->id . ' booking.',
                    'driver_id' => $booking_data->booking->driver_id,
                    'booking_id' => $booking_data->booking->id
                ]);
                $booking_data->update([
                    'payment_type' => "No"
                ]);
                $booking->update(['status' => 1]);
                return response()->json([
                    "url" => env('FRONTED_URL') . '//reservations//' . $booking->id
                ]);
            }


            if ($booking->status == 9) {
                if ($booking->payment) {
                    $paymentIntent = $this->stripe->paymentIntents->update($booking->payment->paymentIntent, [
                        'amount' => number_format((($data['paymentType'] == 'Pre') ? $booking->data->system_payment : $booking->data->discount_price), 2) * 100
                    ]);
                    $booking->payment->update([
                        'status' => $paymentIntent->status,
                        'last_message' => null,
                        'charge' => null
                    ]);
                } else {
                    $paymentIntent = $this->stripe->paymentIntents->create([
                        'amount' => number_format((($data['paymentType'] == 'Pre') ? $booking->data->system_payment : $booking->data->discount_price), 2) * 100,
                        'currency' => 'usd',
                        'automatic_payment_methods' => [
                            'enabled' => true,
                        ],
                        'metadata' => ['booking_id' => $booking->id]
                    ]);
                }
                $output = [
                    'clientSecret' => $paymentIntent->client_secret,
                ];
                return response()->json($output);
            }

            $price = Price::find($booking->price_id);
            $full_discount = Setting::firstWhere('code', 'full_discount')->value;
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
                'payment_type' => $data['paymentType'],
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

            $booking->data->update($inputs);

            if ($booking->payment) {
                $paymentIntent = $this->stripe->paymentIntents->update($booking->payment->paymentIntent, [
                    'amount' => number_format((($booking->data->payment_type == 'Pre') ? $booking->data->system_payment : $booking->data->full_discount_price), 2) * 100
                ]);
                $booking->payment->update([
                    'status' => $paymentIntent->status,
                    'last_message' => null,
                    'charge' => null
                ]);
            } else {
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => number_format((($booking->data->payment_type == 'Pre') ? $booking->data->system_payment : $booking->data->full_discount_price), 2) * 100,
                    'currency' => 'usd',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'metadata' => ['booking_id' => $booking->id]
                ]);
            }

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            return response()->json($output);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function success(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent' => 'required|exists:booking_payments,paymentIntent'
        ]);
        if ($validator->fails()) {
            abort(404);
        }
        try {
            $input = $request->all();
            $status = $this->stripe->paymentIntents->retrieve($input['payment_intent'])->status;
            if ($status == 'succeeded') {
                return view('paymentSuccess');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent' => 'required|exists:booking_payments,paymentIntent'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->get('*');
            return response()->json([
                'error' => $messages
            ], 400);
        }
        try {
            $input = $request->all();
            $refund = $this->stripe->refunds->create([
                'payment_intent' => $input['payment_intent']
            ]);
            BookingPayment::firstWhere('paymentIntent', $input['payment_intent'])->update([
                'refund' => $refund->id
            ]);
            return response()->json([
                'status' => $refund->status
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ]);
        }
    }
}
