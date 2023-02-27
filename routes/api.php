<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\API\GoogleMapsApiController;
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
//Route::get('/caledarEvents', "API\BookingsController@calendarEvents");


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

    Route::post('/bookings', 'API\BookingsController@store');

    Route::post('/logout', 'API\AuthController@logout');
    Route::get('/getRole', 'API\AuthController@getrole');
    Route::get('/getName', 'API\AuthController@getUsername');
    Route::get('/getAcc', 'API\UserController@getAcc');
    Route::post('/updateAcc', 'API\UserController@updateAcc');
});
