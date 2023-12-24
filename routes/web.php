<?php

use App\CustomClass\CalculateSalary;
use App\Http\Controllers\PaymentController;
use App\Lager;
use App\PriceList;
use App\WorkHours;
use App\WorkInput;
use App\WorkLager;
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

Route::get('/customers/deleted', 'CustomersController@deletedCustomers');

Route::get('/users', 'UsersController@index');

Route::get('/contracts', 'ContractController@index');
Route::get('/couponcodes', 'CouponCodeController@index');
Route::get('/couponcodes/online', 'CouponCodeController@online');
Route::get('/couponcodes/giftcards', 'CouponCodeController@giftcards');
Route::get('/couponcodes/offline', 'CouponCodeController@offline');
Route::get('/couponcodes/offline/{id}', 'CouponCodeController@offlineDetail');
Route::get('/couponcodes/offline/export/{id}', 'API\CouponCodeController@exportCouponCodes');


Route::get('/notifications', 'NotificationController@index');
Route::post('/notifications', 'NotificationController@store');

Route::get('/logs', 'LogsController@index');

Route::get('/pricelist', 'PriceListController@index');

Route::get('/stations', 'StationController@index');

Route::get('/vehicles', 'CarController@index');

Route::get('/shifts', 'ShiftController@index');

Route::get('/forbidden', 'HomeController@forbidden');

Route::get('/logout', 'HomeController@logout');

Route::get('/accounting', 'AccountingController@index');
Route::get('/accounting/{id}', 'AccountingController@driverIndex');
Route::get('/accountingdetail', 'AccountingController@detailIndex');
Route::get('/settings', 'SettingController@index');

Route::get('/migrate', function () {
    return Artisan::call('migrate');
});


Route::get('/symlink', function () {
    $targetFolder = '/home/ch349004/web/admin.hoopytransfer.com/public_html/hoopytransfer/storage/app/images';
    $linkFolder = '/home/ch349004/web/admin.hoopytransfer.com/public_html/images';
    if (symlink($targetFolder, $linkFolder)) {
        echo 'Symlink completed';
    } else {
        echo 'Symlink Failed';
    }
});
