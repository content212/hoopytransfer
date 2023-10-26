<?php

use App\Http\Controllers\API\GoogleMapsApiController;
use App\Http\Controllers\API\PaymentController;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'API\AuthController@login');
Route::post('/register', 'API\AuthOtpController@registerGenerate');
Route::post('/registervalidate', 'API\AuthOtpController@registerValidate');
Route::post('/otp/generate', 'API\AuthOtpController@generate');
Route::post('/otp/login', 'API\AuthOtpController@loginWithOtp');
Route::get('/direction', [GoogleMapsApiController::class, '__invoke']);
Route::post('/pricecalculate', 'API\PriceCalculateController@calculate');
Route::post('/track', "API\BookingsController@track");

Route::get('/contracts/active', "API\ContractController@list");
Route::get('/contracts/active/{id}', "API\ContractController@detail");

Route::get('/timerule', "API\BookingsController@timeRule");
Route::get('/settings', 'API\SettingController@index');
//Route::get('/caledarEvents', "API\BookingsController@calendarEvents");

Route::get('/payment', [PaymentController::class, 'index']);
Route::post('/payment', [PaymentController::class, 'payment'])->name('payment.create');
Route::get('/paymentsuccess', [PaymentController::class, 'success'])->name('payment.success');


Route::post('/webhook', function (Request $request) {

    $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    $payload = $request->getContent();
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;
    $secret = env('STRIPE_WEBHOOK_SECRET');
    #$secret = 'whsec_180755da0ce75da72324feea1f3d308b070eea611c78f2a477e7db50e6ceaa4a';
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig_header,
            $secret
        );
    } catch (\UnexpectedValueException $e) {
        return response()->json([], 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response()->json([], 400);
    }

    // Handle the event
    switch ($event->type) {
        case 'payment_intent.created':
            BookingPayment::create([
                'booking_id' => $event->data->object->metadata->booking_id,
                'paymentIntent' => $event->data->object->id,
                'status' => $event->data->object->status
            ]);
            break;
        case 'payment_intent.succeeded':
            $booking_data = Booking::find($event->data->object->metadata->booking_id)->data;
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
            $booking_data->booking->update(['status' => 1]);

            $shift_start_time_string = Setting::where('code', '=', 'shift_start_time')->first();
            $shift_end_time_string = Setting::where('code', '=', 'shift_end_time')->first();
            $booking_time_rule = Setting::where('code', '=', 'booking_time')->first();
            if ($shift_start_time_string != null and $shift_end_time_string != null) {
                $shift_start_time = Carbon::createFromTimeString($shift_start_time_string->value);
                $shift_end_time = Carbon::createFromTimeString($shift_end_time_string->value);
                $now = Carbon::now();

                if ($shift_start_time_string->value == "00:00") {
                    $shift_start_time->addDay();
                    $shift_end_time->addDay();
                } else if ($shift_end_time < $shift_start_time) {
                    $shift_end_time->addDay();
                }
                $booking_time_rule_time = $shift_start_time->copy()->subHours($booking_time_rule->value);
                $booking_datetime = Carbon::createFromTimeString($booking_data->booking->booking_date . ' ' . $booking_data->booking->booking_time);
                if ($now->between($booking_time_rule_time, $shift_start_time)) {
                    if ($booking_datetime->between($shift_start_time, $shift_end_time)) {
                        $shift = Shift::where('shift_date', '=', $now->format('Y-m-d'))
                            ->where('isAssigned', '=', false)
                            ->whereNotNull('driver_id')
                            ->orderBy('queue')
                            ->first();
                        if ($shift) {
                            $shift->update([
                                'booking_id' => $booking_data->booking->id,
                                'isAssigned' => true
                            ]);
                            $total = Transaction::where('driver_id', $shift->driver_id)
                                ->get()
                                ->sum(function ($transaction) {
                                    return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                                });
                            $transaction = Transaction::where('booking_id', $booking_data->booking->id)->where('type', 'driver_wage')->first();
                            $transaction->update([
                                'driver_id' => $request->post('driver_id'),
                                'balance' => ($total - $transaction->amount)
                            ]);
                            $booking_data->booking->update([
                                'status' => 2,
                                'driver_id' => $shift->driver_id
                            ]);
                        }
                    }
                }
            }
        case 'payment_intent.payment_failed':
            $payment = BookingPayment::firstWhere('paymentIntent', $event->data->object->id);
            $payment->update([
                'status' => $event->data->object->last_payment_error->code ?? $event->data->object->status,
                'charge' => $event->data->object->latest_charge,
                'last_message' => $event->data->object->last_payment_error->message ?? null
            ]);
            break;
        case 'charge.refunded':
            $payment = BookingPayment::firstWhere('paymentIntent', $event->data->object->payment_intent);
            $refund = $stripe->refunds->retrieve($payment->refund);
            $booking_data = $payment->booking->data;
            $payment->update([
                'status' => 'refund_' . $refund->status,
                'last_message' => null
            ]);
            if ($refund->status == 'succeeded') {
                Transaction::create([
                    'type' => 'refund',
                    'amount' => $booking_data->payment_type == 'Full' ? $booking_data->full_discount_price : $booking_data->system_payment,
                    'note' => 'Refund from #' . $payment->booking_id . ' booking.',
                    'booking_id' => $payment->booking_id
                ]);
                if ($payment->booking->driver_id) {
                    $total = Transaction::where('driver_id', $payment->booking->driver_id)
                        ->get()
                        ->sum(function ($transaction) {
                            return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                        });
                    Transaction::create([
                        'type' => 'driver_refund',
                        'amount' => $booking_data->payment_type == 'Full' ? $booking_data->full_discount_driver_payment : $booking_data->driver_payment,
                        'balance' => ($total + ($booking_data->payment_type == 'Full' ? $booking_data->full_discount_driver_payment : $booking_data->driver_payment)),
                        'note' => 'Refund for #' . $payment->booking->id . ' booking.',
                        'driver_id' => $payment->booking->driver_id,
                        'booking_id' => $payment->booking->id
                    ]);
                }
            }
            break;
        case 'charge.refund.updated':
            $payment = BookingPayment::firstWhere('paymentIntent', $event->data->object->payment_intent);
            $refund = $stripe->refunds->retrieve($payment->refund);
            $booking_data = $payment->booking->data;
            $payment->update([
                'status' => 'refund_' . $event->data->object->status,
                'last_message' => null
            ]);
            if ($refund->status == 'succeeded') {
                Transaction::create([
                    'type' => 'refund',
                    'amount' => $booking_data->payment_type == 'Full' ? $booking_data->full_discount_price : $booking_data->system_payment,
                    'note' => 'Refund from #' . $payment->booking_id . ' booking.',
                    'booking_id' => $payment->booking_id
                ]);
                if ($payment->booking->driver_id) {
                    $total = Transaction::where('driver_id', $payment->booking->driver_id)
                        ->get()
                        ->sum(function ($transaction) {
                            return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                        });
                    Transaction::create([
                        'type' => 'driver_refund',
                        'amount' => $booking_data->payment_type == 'Full' ? $booking_data->full_discount_driver_payment : $booking_data->driver_payment,
                        'balance' => ($total + ($booking_data->payment_type == 'Full' ? $booking_data->full_discount_driver_payment : $booking_data->driver_payment)),
                        'note' => 'Refund for #' . $payment->booking->id . ' booking.',
                        'driver_id' => $payment->booking->driver_id,
                        'booking_id' => $payment->booking->id
                    ]);
                }
            }
            break;
        default:
            info('Received unknown event type ' . $event->type);
    }

    return 200;
});


Route::middleware(['auth:api', 'role'])->group(function () {


    Route::middleware(['scope:admin'])->get('/users', 'API\UserController@index');
    Route::middleware(['scope:admin'])->post('/users', 'API\UserController@store');
    Route::middleware(['scope:admin'])->post('/usersaction', 'API\UserController@action');
    Route::middleware(['scope:admin'])->get('/users/{user}', 'API\UserController@show');
    Route::middleware(['scope:admin'])->post('/users/{user}', 'API\UserController@update');
    Route::middleware(['scope:admin'])->delete('/users/{user}', 'API\UserController@destroy');

    Route::post('/userdevices', 'API\UserController@addDevice');


    Route::middleware(['scope:admin,driver_manager'])->get('/getdriverdata', 'API\UserController@getDrivers');
    Route::middleware(['scope:admin,driver_manager'])->post('/driversaction', 'API\UserController@driversAction');
    Route::middleware(['scope:admin,driver_manager'])->post('/storedriver', 'API\UserController@storeDriver');

    Route::middleware(['scope:admin'])->get('/getcustomers', 'API\UserController@getCustomers');
    Route::middleware(['scope:admin'])->get('/customer/{customer}', 'API\UserController@getCustomer');
    Route::middleware(['scope:admin'])->post('/customeraction', 'API\UserController@customersAction');


    Route::middleware(['scope:customer,driver,admin'])->get('/userContractDetail/{user_contract_id}', 'API\ContractController@getUserContractDetail');
    Route::middleware(['scope:customer,driver,admin'])->get('/getCustomer', 'API\UserController@FrontEndCustomer');
    Route::middleware(['scope:customer,driver,admin'])->post('/deleteaccount', 'API\UserController@DeleteAccount');
    Route::middleware(['scope:customer,admin'])->post('/updateCustomer', 'API\UserController@FrontEndCustomerUpdate');
    Route::middleware(['scope:customer,driver,admin'])->get('/getBookings', 'API\BookingsController@FrontEndCustomerBookings');
    Route::middleware(['scope:customer,driver,admin'])->get('/getBookingsDetail/{id}', 'API\BookingsController@FrontEndCustomerBookingsDetail');

    Route::middleware(['scope:driver'])->get('/driver/jobs', 'API\DriverJobController@index');
    Route::middleware(['scope:driver'])->get('/driver/jobs/{id}', 'API\DriverJobController@detail');
    Route::middleware(['scope:driver'])->post('/driver/tripIsStarted/{booking_id}', 'API\DriverJobController@tripIsStarted');
    Route::middleware(['scope:driver'])->post('/driver/tripIsCompleted/{booking_id}', 'API\DriverJobController@tripIsCompleted');
    Route::middleware(['scope:driver'])->post('/driver/tripIsNotCompleted/{booking_id}', 'API\DriverJobController@tripIsNotCompleted');

    Route::middleware(['scope:admin,editor'])->get('/bookings', 'API\BookingsController@index');
    Route::middleware(['scope:admin,editor'])->get('/bookings/{booking}', 'API\BookingsController@show');
    Route::middleware(['scope:admin,editor'])->post('/bookings/{booking}', 'API\BookingsController@update');
    Route::middleware(['scope:admin,editor'])->post('/bookings/{booking}/complete', 'API\BookingsController@complete');
    Route::middleware(['scope:admin,customer'])->post('/bookings/{booking}/cancel', 'API\BookingsController@cancel');

    Route::middleware(['scope:admin'])->delete('/bookings/{booking}', 'API\BookingsController@destroy');
    Route::middleware(['scope:admin,driver'])->get('/caledarEvents', "API\BookingsController@calendarEvents");
    Route::middleware(['scope:admin,driver'])->get('/driver-bookings/{booking}', 'API\BookingsController@showDriverBooking');


    Route::middleware(['scope:admin,editor'])->get('/bookingscount/{status}', 'API\BookingsController@getBookingsCount')->name('count');;


    Route::middleware(['scope:admin'])->get('/prices', 'API\PriceController@index');
    Route::middleware(['scope:admin'])->post('/prices', 'API\PriceController@store');
    Route::middleware(['scope:admin'])->get('/prices/{price}', 'API\PriceController@show');
    Route::middleware(['scope:admin'])->post('/prices/{price}', 'API\PriceController@update');
    Route::middleware(['scope:admin'])->delete('/prices/{price}', 'API\PriceController@destroy');

    Route::middleware(['scope:admin'])->get('/logs', 'API\LogsController@index');

    Route::middleware(['scope:admin'])->get('/cars', 'API\CarController@index');
    Route::middleware(['scope:admin'])->post('/cars', 'API\CarController@store');
    Route::middleware(['scope:admin'])->get('/cars/{car}', 'API\CarController@show');
    Route::middleware(['scope:admin'])->post('/cars/{car}', 'API\CarController@update');
    Route::middleware(['scope:admin'])->delete('/cars/{car}', 'API\CarController@destroy');
    Route::middleware(['scope:admin'])->get('/carsbytype/{type}', 'API\CarController@getCarsByType');


    Route::middleware(['scope:admin'])->get('/cartypes', 'API\CarTypeController@index');
    Route::middleware(['scope:admin'])->post('/cartypes', 'API\CarTypeController@store');
    Route::middleware(['scope:admin'])->get('/cartypes/{cartype}', 'API\CarTypeController@show');
    Route::middleware(['scope:admin'])->post('/cartypes/{cartype}', 'API\CarTypeController@update');
    Route::middleware(['scope:admin'])->delete('/cartypes/{cartype}', 'API\CarTypeController@destroy');

    Route::middleware(['scope:admin'])->get('/drivers', 'API\DriverController@index');
    Route::middleware(['scope:admin'])->post('/drivers', 'API\DriverController@store');
    Route::middleware(['scope:admin'])->get('/drivers/{driver}', 'API\DriverController@show');
    Route::middleware(['scope:admin'])->post('/drivers/{driver}', 'API\DriverController@update');
    Route::middleware(['scope:admin'])->delete('/drivers/{driver}', 'API\DriverController@destroy');

    Route::middleware(['scope:admin'])->get('/stations', 'API\StationController@index');
    Route::middleware(['scope:admin'])->post('/stations', 'API\StationController@store');
    Route::middleware(['scope:admin'])->get('/stations/{station}', 'API\StationController@show');
    Route::middleware(['scope:admin'])->post('/stations/{station}', 'API\StationController@update');
    Route::middleware(['scope:admin'])->delete('/stations/{station}', 'API\StationController@destroy');

    Route::middleware(['scope:admin'])->post('/settings', 'API\SettingController@save');

    Route::middleware(['scope:admin'])->get('/contracts', 'API\ContractController@index');
    Route::middleware(['scope:admin'])->get('/contracts/{id}', 'API\ContractController@show');
    Route::middleware(['scope:admin'])->post('/contracts', 'API\ContractController@store');
    Route::middleware(['scope:admin'])->delete('/contracts/{id}', 'API\ContractController@destroy');
    Route::middleware(['scope:customer,driver, admin'])->post('/generateUserContract', 'API\ContractController@generateUserContract');

    Route::middleware(['scope:admin'])->get('/shifts', 'API\ShiftController@clanderIndex');
    Route::middleware(['scope:admin'])->get('/shifts/{date}', 'API\ShiftController@show');
    Route::middleware(['scope:admin'])->post('/shifts', 'API\ShiftController@store');


    Route::post('/bookings', 'API\BookingsController@store');

    Route::post('/logout', 'API\AuthController@logout');
    Route::get('/getRole', 'API\AuthController@getrole');
    Route::get('/getName', 'API\AuthController@getUsername');
    Route::get('/getAcc', 'API\UserController@getAcc');
    Route::post('/updateAcc', 'API\UserController@updateAcc');
});
