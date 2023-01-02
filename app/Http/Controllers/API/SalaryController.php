<?php

namespace App\Http\Controllers\API;

use App\WorkInput;
use Illuminate\Http\Request;
use App\CustomClass\CalculateSalary;
use App\DriverExcuse;
use App\Http\Controllers\Controller;
use App\Lager;
use App\WorkHours;
use App\WorkLager;
use App\WorkNote;
use Carbon\Carbon;

class CalculateInterval
{
}
class SalaryController extends Controller
{
    private  $curretInterval;
    private function nextInterval($interval)
    {
        if ($interval == "6_18") {
            $this->curretInterval = "18_22";
            return "18_22";
        } else if ($interval == "18_22") {
            $this->curretInterval = "22_0";
            return "22_0";
        } else if ($interval == "22_0") {
            $this->curretInterval = "0_6";
            return "0_6";
        } else if ($interval == "0_6") {
            $this->curretInterval = "6_18";
            return "6_18";
        }
    }
    private function CalculateOvertime($interval, $overtime, $input)
    {
        $result["6_18"] = $input["6_18"]["result"];
        $result["18_22"] = $input["18_22"]["result"];
        $result["22_0"] = $input["22_0"]["result"];
        $result["0_6"] = $input["0_6"]["result"];
        $this->curretInterval = $interval;
        $total_work = $input['total_work'];
        $overtime = $overtime * 60;



        while ($total_work > $overtime) {
            $toplam = $result[$this->curretInterval];
            echo json_encode($result);
            if ($toplam > $overtime) {
                $fark = $toplam - $overtime;
                $result["$this->curretInterval"] -= $fark;
                $next = $this->nextInterval($this->curretInterval);
                $result["$next"] += $fark;
                $total_work -= $overtime;
            } else {
                while (true) {
                    if ($toplam > $overtime) {
                        break;
                    }
                    $next = $this->nextInterval($this->curretInterval);
                    if ($this->curretInterval == $interval) {
                        //$total_work -= $toplam;
                        echo "total work = $total_work";
                        //break;
                    }
                    $toplam += $result["$next"];
                }
                if ($toplam > $overtime) {
                    echo "toplam : $toplam-overtime : $overtime-current : $this->curretInterval next : $next";
                    $fark = $toplam - $overtime;
                    $result["$this->curretInterval"] -= $fark;
                    echo '22_0 : ' . $result["22_0"];

                    $next = $this->nextInterval($this->curretInterval);
                    $result["$next"] += $fark;
                    $total_work -= $overtime;
                } else {
                    $total_work -= $toplam;
                }
            }
        }

        return $result;
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $lager = Lager::where('id', $input['lager'])->first();
        $group_id = 1;
        $lastest = WorkInput::latest('created_at')->first();
        if ($lastest)
            $group_id = $lastest->group_id + 1;
        $start = WorkInput::create(
            [
                'user_id'   => $input['user_id'],
                'group_id'  => $group_id,
                'type'      => 'start',
                'datetime'  => $input['start_datetime']
            ]
        );
        $finish = WorkInput::create(
            [
                'user_id'   => $input['user_id'],
                'group_id'  => $group_id,
                'type'      => 'finish',
                'datetime'  => $input['finish_datetime']
            ]
        );
        $rest_start = WorkInput::create(
            [
                'user_id'   => $input['user_id'],
                'group_id'  => $group_id,
                'type'      => 'rest_start',
                'datetime'  => $input['rest_start']
            ]
        );
        $rest_finish = WorkInput::create(
            [
                'user_id'   => $input['user_id'],
                'group_id'  => $group_id,
                'type'      => 'rest_finish',
                'datetime'  => $input['rest_finish']
            ]
        );

        $excuse = 0;
        if ($input['excuse'] == 1) {
            $start_datetime = new Carbon($start->datetime);
            $excuse_count = DriverExcuse::where('month', $start_datetime->month)->where('year', $start_datetime->year)->where('driver_id', $input['user_id'])->count();
            if ($excuse_count == 1)
                $excuse = 1;
            else if ($excuse_count > 1)
                $excuse = 2;
            DriverExcuse::create(
                [
                    'driver_id' => $input['user_id'],
                    'month'     => $start_datetime->month,
                    'year'      => $start_datetime->year,
                    'group_id'  => $group_id
                ]
            );
        }



        $salary = new CalculateSalary($input['user_id'], $start->datetime, $finish->datetime, $rest_start->datetime, $rest_finish->datetime, 0);

        $result = $salary->result();


        WorkLager::create(
            [
                'group_id'  => $group_id,
                'lager_id'  => $lager->id
            ]
        );

        if ($lager->isOvertime) {
            $overtime = $lager->overtime;
            $start_datetime = new Carbon($start->datetime);
            $start_time = $start_datetime->toTimeString();

            $start6_18 = Carbon::createFromTimeString('06:00')->toTimeString();
            $end6_18 = Carbon::createFromTimeString('17:59')->toTimeString();

            $start18_22 = Carbon::createFromTimeString('18:00')->toTimeString();
            $end18_22 = Carbon::createFromTimeString('21:59')->toTimeString();

            $start22_0 = Carbon::createFromTimeString('22:00')->toTimeString();
            $end22_0 = Carbon::createFromTimeString('23:59');

            $start0_6 = Carbon::createFromTimeString('00:00')->toTimeString();
            $end0_6 = Carbon::createFromTimeString('06:00')->toTimeString();

            $interval = 0;
            if ($start_time >= $start6_18 && $start_time <= $end6_18) {
                $interval = "6_18";
            } else if ($start_time >= $start18_22 && $start_time <= $end18_22) {
                $interval = "18_22";
            } else if ($start_time >= $start22_0 && $start_time <= $end22_0) {
                $interval = "22_0";
            } else if ($start_time >= $start0_6 && $start_time <= $end0_6) {
                $interval = "0_6";
            }

            $withOvertime = $this->CalculateOvertime($interval, $overtime, $result);
            $result["6_18"]["result"] = $withOvertime["6_18"];
            $result["18_22"]["result"] = $withOvertime["18_22"];
            $result["22_0"]["result"] = $withOvertime["22_0"];
            $result["0_6"]["result"] = $withOvertime["0_6"];
        }
        WorkNote::create(
            [
                'group_id'  => $group_id,
                'note'      => $input['note']
            ]
        );
        return WorkHours::create(
            [
                'group_id'  => $group_id,
                '6_18'      => $result["6_18"]["result"],
                '6_18_day'  => $result["6_18"]["day"],
                '18_22'     => $result["18_22"]["result"],
                '18_22_day' => $result["18_22"]["day"],
                '22_0'      => $result["22_0"]["result"],
                '22_0_day'  => $result["22_0"]["day"],
                '0_6'       => $result["0_6"]["result"],
                '0_6_day'   => $result["0_6"]["day"],
                'date'      => $start_datetime->toDateString(),
                'excuse'    => $excuse
            ]
        );
    }
}
