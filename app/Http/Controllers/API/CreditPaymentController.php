<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingData;
use App\Models\BookingPayment;
use App\Models\CouponCode;
use App\Models\Price;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCreditActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class CreditPaymentController extends Controller
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function index(Request $request)
    {
        $input = $request->all();
        if (!isset($input['coupon_code_id']))
            abort(404);

        return view('creditPayment')->with(['email' => $input['email'] ?? "", 'coupon_code_id' => $input['coupon_code_id']]);
    }

    public function payment(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'coupon_code_id' => 'required|exists:coupon_codes,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages()->get('*');
            return response()->json([
                'error' => $messages
            ], 400);
        }


        try {
            $coupon_code_id = $data['coupon_code_id'];
            $isGift = $data['is_gift'];
            $email = $data['email'];
            $user = User::where('email', $email)->first();
            $coupon_code = CouponCode::find($coupon_code_id);
            $amount = floatval($coupon_code->price);
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount * 100,
                'currency' => 'CHF',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => ['coupon_code_id' => $coupon_code_id, 'user_id' => $user->id, 'is_gift' => $isGift]
            ]);

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
        try {
            $input = $request->all();
            $payment_intent = $input['payment_intent'];
            $status = $this->stripe->paymentIntents->retrieve($payment_intent)->status;
            if ($status == 'succeeded') {
                return view('creditPaymentSuccess');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent' => 'required|exists:user_credit_activities,paymentIntent'
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
            UserCreditActivity::firstWhere('payment_intent', $input['payment_intent'])->update([
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
