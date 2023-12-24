<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingData;
use App\Models\BookingPayment;
use App\Models\Price;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCreditActivity;
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

        $useCredit = '0';
        if (isset($input['useCredit'])) {
            $useCredit = $input['useCredit'];
        }

        $email = Booking::find($input['bookingId'])->user->email;
        return view('payment')->with(['email' => $email, 'useCredit' => $useCredit, 'bookingId' => $input['bookingId']]);
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
            $user = User::find($booking->user_id);

            $useCredit = '0';
            if (isset($data['useCredit'])) {
                $useCredit = $data['useCredit'];
            }

            $credit = floatval($user->credit);

            if (!in_array($booking->status, [0, 9]))
                return response()->json([
                    'error' => 'Payment not created for this booking!'
                ]);

            if ($data['paymentType'] == 'No') {
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
                    $amount = floatval((($data['paymentType'] == 'Pre') ? $booking->data->system_payment : $booking->data->discount_price));
                    $paymentIntent = $this->stripe->paymentIntents->update($booking->payment->paymentIntent, [
                        'amount' => $amount * 100
                    ]);
                    $booking->payment->update([
                        'status' => $paymentIntent->status,
                        'last_message' => null,
                        'charge' => null
                    ]);
                } else {
                    $amount = floatval((($data['paymentType'] == 'Pre') ? $booking->data->system_payment : $booking->data->discount_price));
                    $paymentIntent = $this->stripe->paymentIntents->create([
                        'amount' => $amount * 100,
                        'currency' => 'CHF',
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

            $paymentType = $data['paymentType'];
            $full_discount = Setting::firstWhere('code', 'full_discount')->value;
            $total = ($price->opening_fee + ($price->km_fee * $booking->km));
            $discount_price = $total * (1.0 - ($price->carType->discount_rate / 100.0));
            $driver_payment = $discount_price * 0.7;
            $system_payment = $discount_price - $driver_payment;
            $full_discount_price = $discount_price * (1.0 - ($full_discount / 100.0));
            $full_discount_system_payment = $system_payment - (($discount_price * ($full_discount / 100.0)) * 0.3);
            $full_discount_driver_payment = $driver_payment - (($discount_price * ($full_discount / 100.0)) * 0.7);
            $used_credit_amount = 0.0;
            $continue_payment = true;
            if ($useCredit == '1') {
                if ($paymentType == "Full") {
                    if ($credit >= $full_discount_price) {
                        $used_credit_amount = $full_discount_price;
                        $continue_payment = false;
                    } else {
                        $used_credit_amount = $credit;
                    }
                } else {
                    if ($credit >= $system_payment) {
                        $used_credit_amount = $system_payment;
                        $continue_payment = false;
                    } else {
                        $used_credit_amount = $credit;
                    }
                }
            }


            $inputs = [
                'booking_id' => $booking->id,
                'km' => $booking->km,
                'opening_fee' => $price->opening_fee,
                'km_fee' => $price->km_fee,
                'payment_type' => $paymentType,
                'discount_rate' => $price->carType->discount_rate,
                'discount_price' => $discount_price,
                'system_payment' => $system_payment,
                'driver_payment' => $driver_payment,
                'total' => $total,
                'full_discount' => $full_discount,
                'full_discount_price' => $full_discount_price,
                'full_discount_system_payment' => $full_discount_system_payment,
                'full_discount_driver_payment' => $full_discount_driver_payment,
                'use_credit' => $useCredit == '1',
                'used_credit_amount' => $used_credit_amount
            ];

            $booking->data->update($inputs);

            if ($useCredit == '1') {

                if (!$continue_payment) {

                    $user->update([
                        'credit' => ($credit - $used_credit_amount)]);

                    UserCreditActivity::create([
                        'user_id' => $user->id,
                        'booking_id' => $booking->id,
                        'activity_type' => 'spend',
                        'credit' => $used_credit_amount,
                        'note' => 'Booking Spend',
                        'note2' => $booking->track_code,
                    ]);


                    if ($booking->payment) {
                        $booking->payment->update([
                            'paymentIntent' => "CREDIT",
                            'status' => "succeeded"
                        ]);
                    } else {
                        BookingPayment::create([
                            'booking_id' => $booking->id,
                            'paymentIntent' => "CREDIT",
                            'status' => "succeeded"
                        ]);
                    }

                    $booking->update(['status' => 1]);

                    return response()->json([
                        "url" => env('FRONTED_URL') . '//reservations//' . $booking->id
                    ]);
                }
            }


            if ($booking->payment) {
                $amount = floatval((($booking->data->payment_type == 'Pre') ? $booking->data->system_payment : $booking->data->full_discount_price));
                $amount = $amount - $used_credit_amount;
                $paymentIntent = $this->stripe->paymentIntents->update($booking->payment->paymentIntent, [
                    'amount' => $amount * 100
                ]);
                $booking->payment->update([
                    'status' => $paymentIntent->status,
                    'last_message' => null,
                    'charge' => null
                ]);
            } else {
                $amount = floatval((($booking->data->payment_type == 'Pre') ? $booking->data->system_payment : $booking->data->full_discount_price));
                $amount = $amount - $used_credit_amount;
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => $amount * 100,
                    'currency' => 'CHF',
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
            $payment_intent = $input['payment_intent'];
            $status = $this->stripe->paymentIntents->retrieve($payment_intent)->status;
            if ($status == 'succeeded') {

                $bookingPayment = BookingPayment::where('payment_intent', $payment_intent)->first();

                if ($bookingPayment) {

                    $booking = Booking::where('id', $bookingPayment->booking_id);

                    if ($booking) {

                        $bookingData = BookingData::where('booking_id', $booking->id)->first();

                        if ($bookingData && $bookingData->use_credit) {
                            $user = User::where('id', $booking->id)->first();

                            if ($user) {

                                $credit = floatval($user->credit);
                                $used_credit_amount = floatval($bookingData->used_credit_amount);

                                $user->update([
                                    'credit' => ($credit - $used_credit_amount)]);

                                UserCreditActivity::create([
                                    'user_id' => $user->id,
                                    'booking_id' => $booking->id,
                                    'activity_type' => 'spend',
                                    'credit' => $used_credit_amount,
                                    'note' => 'Booking Spend',
                                    'note2' => $booking->track_code,
                                ]);
                            }
                        }
                    }
                }

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
