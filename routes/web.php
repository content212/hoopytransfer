<?php

use App\Lager;
use App\PriceList;
use App\WorkHours;
use App\WorkInput;
use App\WorkLager;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\CustomClass\CalculateSalary;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('login');

Route::get('/otptest', 'HomeController@otplogin');

Route::get('/bookings', 'BookingsController@bookingsDatatable');

Route::get('/prices', 'PricesController@PricesDatatable');

Route::post('/importexcel', 'ExcelController@import');

Route::get('/calendar', 'CalendarController@index');

Route::get('/drivers', 'DriversController@index');

Route::get('/customers', 'CustomersController@index');

Route::get('/users', 'UsersController@index');

Route::get('/logs', 'LogsController@index');

Route::get('/pricelist', 'PriceListController@index');

Route::get('/stations', 'StationController@index');

Route::get('/vehicles', 'CarController@index');

Route::get('/forbidden', 'HomeController@forbidden');

Route::get('/logout', 'HomeController@logout');

Route::post('/test', 'BookingsController@test');

Route::get('/symlink', function () {
    Artisan::call('storage:link --relative');
});
