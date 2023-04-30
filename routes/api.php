<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\API\GoogleMapsApiController;
use App\Http\Controllers\API\PaymentController;
use App\Models\Booking;
use App\Models\BookingData;
use App\Models\BookingPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;



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
Route::get('/contracts/savecontract', "API\ContractController@saveContract");
Route::get('/contracts/active/{id}', "API\ContractController@detail");

Route::get('/timerule', "API\BookingsController@timeRule");
//Route::get('/caledarEvents', "API\BookingsController@calendarEvents");

Route::get('/payment', [PaymentController::class, 'index']);
Route::post('/payment', [PaymentController::class, 'payment'])->name('payment.create');
Route::get('/paymentsuccess', [PaymentController::class, 'success'])->name('payment.success');


Route::post('/webhook', function (Request $request) {

    $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    $payload = $request->getContent();
    $sig_header =  $_SERVER['HTTP_STRIPE_SIGNATURE'];
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
                'amount' => $booking_data->payment_type == 'Full' ?  $booking_data->full_discount_price : $booking_data->system_payment,
                'note' => 'Payment from #' . $booking_data->booking->id . ' booking.',
                'booking_id' => $booking_data->booking->id
            ]);
            $total = Transaction::where('driver_id', $booking_data->booking->driver_id)
                ->get()
                ->sum(function ($transaction) {
                    return ($transaction->type == 'driver_payment' or $transaction->type == 'driver_refund') ? $transaction->amount : (($transaction->type == 'driver_wage') ? -$transaction->amount : 0);
                });
            Transaction::create([
                'type' => 'driver_wage',
                'amount' => $booking_data->driver_payment,
                'balance' => $total,
                'note' => 'Wage for #' . $booking_data->booking->id . ' booking.',
                'driver_id' => $booking_data->booking->driver_id,
                'booking_id' => $booking_data->booking->id
            ]);
            $booking_data->booking->update(['status' => 1]);
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
                    'amount' => $booking_data->payment_type == 'Full' ?  $booking_data->full_discount_price : $booking_data->system_payment,
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
                        'amount' => $booking_data->driver_payment,
                        'balance' => $total,
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
                'status' => 'refund_' .  $event->data->object->status,
                'last_message' => null
            ]);
            if ($refund->status == 'succeeded') {
                Transaction::create([
                    'type' => 'refund',
                    'amount' => $booking_data->payment_type == 'Full' ?  $booking_data->full_discount_price : $booking_data->system_payment,
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
                        'amount' => $booking_data->driver_payment,
                        'balance' => $total,
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


    Route::middleware(['scope:admin,driver_manager'])->get('/getdriverdata', 'API\UserController@getDrivers');
    Route::middleware(['scope:admin,driver_manager'])->post('/driversaction', 'API\UserController@driversAction');
    Route::middleware(['scope:admin,driver_manager'])->post('/storedriver', 'API\UserController@storeDriver');

    Route::middleware(['scope:admin'])->get('/getcustomers', 'API\UserController@getCustomers');
    Route::middleware(['scope:admin'])->get('/customer/{customer}', 'API\UserController@getCustomer');
    Route::middleware(['scope:admin'])->post('/customeraction', 'API\UserController@customersAction');

    Route::middleware(['scope:customer'])->get('/getCustomer', 'API\UserController@FrontEndCustomer');
    Route::middleware(['scope:customer'])->post('/updateCustomer', 'API\UserController@FrontEndCustomerUpdate');
    Route::middleware(['scope:customer'])->get('/getBookings', 'API\BookingsController@FrontEndCustomerBookings');
    Route::middleware(['scope:customer'])->get('/getBookingsDetail/{id}', 'API\BookingsController@FrontEndCustomerBookingsDetail');

    Route::middleware(['scope:admin,editor'])->get('/bookings', 'API\BookingsController@index');
    Route::middleware(['scope:admin,editor'])->get('/bookings/{booking}', 'API\BookingsController@show');
    Route::middleware(['scope:admin,editor'])->post('/bookings/{booking}', 'API\BookingsController@update');
    Route::middleware(['scope:admin'])->delete('/bookings/{booking}', 'API\BookingsController@destroy');
    Route::middleware(['scope:admin,driver'])->get('/caledarEvents', "API\BookingsController@calendarEvents");

    Route::middleware(['scope:admin,customer'])->post('/bookings/{booking}/cancel', 'API\BookingsController@cancel');

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

    Route::post('/bookings', 'API\BookingsController@store');

    Route::post('/logout', 'API\AuthController@logout');
    Route::get('/getRole', 'API\AuthController@getrole');
    Route::get('/getName', 'API\AuthController@getUsername');
    Route::get('/getAcc', 'API\UserController@getAcc');
    Route::post('/updateAcc', 'API\UserController@updateAcc');
});
