<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Models\BookingData;
use App\Http\Controllers\Controller;
use App\Models\BookingPayment;
use App\Models\Price;
use App\Models\Setting;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
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
        return view('payment');
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
            if (!in_array($booking->status, [0, 8]))
                return response()->json([
                    'error' => 'Payment not created for this booking!'
                ]);
            $price = Price::find($booking->price_id);
            $full_discount = Setting::firstWhere('code', 'full_discount')->value;
            $inputs = [
                'booking_id' => $booking->id,
                'km' => $booking->km,
                'opening_fee' => $price->opening_fee,
                'km_fee' => $price->km_fee,
                'discount_rate' => $price->carType->discount_rate,
                'payment_type' => $data['paymentType'],
                'system_payment' => ($price->opening_fee + ($price->km_fee *  $booking->km)) * (1.0 - ($price->carType->discount_rate / 100.0)) * 0.3,
                'driver_payment' => ($price->opening_fee + ($price->km_fee *  $booking->km)) * (1.0 - ($price->carType->discount_rate / 100.0)) * 0.7,
                'total' => ($price->opening_fee + ($price->km_fee *  $booking->km)) * (1.0 - ($price->carType->discount_rate / 100.0)),
                'full_discount' => $full_discount ?? 0
            ];
            if ($inputs['payment_type'] == 'Full') {
                $inputs['system_payment'] = $inputs['system_payment'] - ($inputs['total'] * ($inputs['full_discount'] / 100.0));
                $inputs['total'] = $inputs['total'] * (1.0 - ($inputs['full_discount'] / 100.0));
            }
            if ($booking->data) {
                $booking->data->update($inputs);
                $paymentIntent = $this->stripe->paymentIntents->update($booking->payment->paymentIntent, [
                    'amount' => number_format((($booking->data->payment_type == 'Pre') ? $booking->data->system_payment : $booking->data->total), 2) * 100
                ]);
                $booking->payment->update([
                    'status' => $paymentIntent->status,
                    'last_message' => null,
                    'charge' => null
                ]);
            } else {
                $booking_data = new BookingData($inputs);
                $booking->data()->save($booking_data);
                $booking->load('data');
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => (($booking->data->payment_type == 'Pre') ? $booking->data->system_payment : $booking->data->total) * 100,
                    'currency' => 'usd',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'metadata' => ['booking_id' => $booking->id]
                ]);
            }

            //if ($booking->data->payment_type == 'Full') {
            //    $booking->data->update([
            //        'system_payment' => $booking->data->system_payment - ($booking->data->total * ($booking->data->full_discount / 100.0)),
            //        'total' => $booking->data->total * (1.0 - ($booking->data->full_discount / 100.0))
            //    ]);
            //}


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
