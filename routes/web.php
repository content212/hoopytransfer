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

Route::get('/bookings', 'BookingsController@bookingsDatatable');

Route::get('/prices', 'PricesController@PricesDatatable');

Route::post('/importexcel', 'ExcelController@import');

Route::get('/calendar', 'CalendarController@index');

Route::get('/drivers', 'DriversController@index');

Route::get('/customers', 'CustomersController@index');

Route::get('/users', 'UsersController@index');

Route::get('/logs', 'LogsController@index');

Route::get('/pricelist', 'PriceListController@index');

Route::get('/lager', 'LagerController@index');

Route::get('/cars', 'CarController@index');

Route::get('/forbidden', 'HomeController@forbidden');

Route::get('/logout', 'HomeController@logout');

//Route::get('/test', function () {
//    $result = [];
//    $groups = WorkHours::select('work_hours.group_id')
//        ->join('work_inputs', 'work_inputs.group_id', '=', 'work_hours.group_id')
//        ->where('work_inputs.user_id', 25)
//        ->whereMonth('work_hours.date', 8)
//        ->whereYear('work_hours.date', 2021)
//        ->groupBy('work_hours.group_id')
//        ->get();
//    $pricelist = PriceList::where('company_id', 1)->where('is_active', 1)->first();
//    foreach ($groups as $group) {
//        $group_id = $group->group_id;
//        $workhours = WorkHours::select('6_18', '18_22', '22_0', '0_6', DB::raw('CASE excuse when 0 then 0 when 1 then 1 when 2 then 1 END as excuse'))->where('group_id', $group_id)->first();
//
//        $lager_id = WorkLager::select('lager_id')->where('group_id', $group_id)->first()->lager_id;
//        $lager_name = Lager::select('name')->where('id', $lager_id)->first()->name;
//
//        $excuse = $workhours->excuse;
//
//        $start_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'start')->where('group_id', $group_id)->first()->datetime);
//        $start_date = $start_datetime->toDateString();
//        $start_time = $start_datetime->toTimeString();
//
//        $finish_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'finish')->where('group_id', $group_id)->first()->datetime);
//        $finish_date = $finish_datetime->toDateString();
//        $finish_time = $finish_datetime->toTimeString();
//
//        $rest_start_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'rest_start')->where('group_id', $group_id)->first()->datetime);
//        $rest_start_date = $rest_start_datetime->toDateString();
//        $rest_start_time = $rest_start_datetime->toTimeString();
//
//        $rest_finish_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'rest_finish')->where('group_id', $group_id)->first()->datetime);
//        $rest_finish_date = $rest_finish_datetime->toDateString();
//        $rest_finish_time = $rest_finish_datetime->toTimeString();
//
//        $now = Carbon::now()->hours(0)->minute(0)->second(0)->millisecond(0);
//        $interval1 = $now->copy()->addMinute($workhours->{'6_18'});
//        $interval2 = $now->copy()->addMinute($workhours->{'18_22'})->copy();
//        $interval3 = $now->copy()->addMinute($workhours->{'22_0'})->copy();
//        $interval4 = $now->copy()->addMinute($workhours->{'0_6'})->copy();
//
//        $sumhour = $interval1->hour + $interval2->hour + $interval3->hour + $interval4->hour;
//        if ($sumhour < 10)
//            $sumhour = "0$sumhour";
//        $summinute = $interval1->minute + $interval2->minute + $interval3->minute + $interval4->minute;
//        if ($summinute < 10)
//            $summinute = "0$summinute";
//        $sumsecond = $interval1->second + $interval2->second + $interval3->second + $interval4->second;
//        if ($sumsecond < 10)
//            $sumsecond = "0$sumsecond";
//
//        $sum = "$sumhour:$summinute:$sumsecond";
//
//
//
//        if ($pricelist->start_date > $start_date) {
//            $pricelist = PriceList::where('start_date', '<=', $start_date)->where('end_date', '>=', $start_date)->first();
//        }
//        $interval1price = 0;
//        if ($workhours->{'6_18_day'} < 6) {
//            $interval1price = ($workhours->{'6_18'} / 60) * $pricelist->{'1_weekday'};
//        } else if ($workhours->{'6_18_day'} == 6) {
//            $interval1price = ($workhours->{'6_18'} / 60) * $pricelist->{'1_saturday'};
//        } else {
//            $interval1price = ($workhours->{'6_18'} / 60) * $pricelist->{'1_sunday'};
//        }
//
//        $interval2price = 0;
//        if ($workhours->{'18_22_day'} < 6) {
//            $interval2price = ($workhours->{'18_22'} / 60) * $pricelist->{'2_weekday'};
//        } else if ($workhours->{'18_22_day'} == 6) {
//            $interval2price = ($workhours->{'18_22'} / 60) * $pricelist->{'2_saturday'};
//        } else {
//            $interval2price = ($workhours->{'18_22'} / 60) * $pricelist->{'2_sunday'};
//        }
//
//        $interval3price = 0;
//        if ($workhours->{'18_22_day'} < 6) {
//            $interval3price = ($workhours->{'18_22'} / 60) * $pricelist->{'3_weekday'};
//        } else if ($workhours->{'18_22_day'} == 6) {
//            $interval3price = ($workhours->{'18_22'} / 60) * $pricelist->{'3_saturday'};
//        } else {
//            $interval3price = ($workhours->{'18_22'} / 60) * $pricelist->{'4_sunday'};
//        }
//
//        $interval4price = 0;
//        if ($workhours->{'0_6_day'} < 6) {
//            $interval4price = ($workhours->{'0_6'} / 60) * $pricelist->{'4_weekday'};
//        } else if ($workhours->{'0_6_day'} == 6) {
//            $interval4price = ($workhours->{'0_6'} / 60) * $pricelist->{'4_saturday'};
//        } else {
//            $interval4price = ($workhours->{'0_6'} / 60) * $pricelist->{'4_sunday'};
//        }
//
//        $sumprice = $interval1price + $interval2price + $interval3price + $interval4price;
//        $data = [
//            'lager_name'        => $lager_name,
//            'excuse'            => $excuse,
//            'start_date'        => $start_date,
//            'start_time'        => $start_time,
//            'finish_date'       => $finish_date,
//            'finish_time'       => $finish_time,
//            'rest_start_time'   => $rest_start_time,
//            'rest_finish_time'  => $rest_finish_time,
//            'interval1'         => $interval1->toTimeString(),
//            'interval2'         => $interval2->toTimeString(),
//            'interval3'         => $interval3->toTimeString(),
//            'interval4'         => $interval4->toTimeString(),
//            'sum'               => $sum,
//            'sumprice'          => $sumprice
//        ];
//        array_push($result, $data);
//    }
//    return $result;
//});
