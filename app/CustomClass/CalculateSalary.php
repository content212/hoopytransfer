<?php

namespace App\CustomClass;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalculateSalary
{
    private $user_id;
    private $start;
    private $end;
    private $rest_start;
    private $rest_end;
    private $excuse;
    protected $times = [
        [06, 18],
        [18, 22],
        [22, 23],
        [23, 06]
    ];
    public $result = [];

    function __construct($user_id, $start, $end, $rest_start, $rest_end, $excuse)
    {
        $this->user_id = $user_id;
        $this->start = new Carbon($start);
        $this->end = new Carbon($end);
        $this->rest_start = new Carbon($rest_start);
        $this->rest_end = new Carbon($rest_end);
        $this->excuse = $excuse;
        $this->calculate();
    }

    function calculate()
    {
        $this->result["user_id"] = $this->user_id;
        $this->result["total_work"] = $this->diffWorkMinutes() - $this->diffRestMinutes();
        $this->result["total_rest"] = $this->diffRestMinutes();
        $this->result["job_interval"] = [$this->start->toDateTimeString(), $this->end->toDateTimeString()];
        $this->result["rest_interval"] = [$this->rest_start->toDateTimeString(), $this->rest_end->toDateTimeString()];
        $this->result["excuse"] = $this->excuse;
        foreach ($this->times as $time) {
            $this->dayCalculate(...$time);
        }
    }
    function diffWorkMinutes()
    {
        return $this->start->diffInMinutes($this->end);
    }
    function diffRestMinutes()
    {
        return $this->rest_start->diffInMinutes($this->rest_end);
    }
    function dayCalculate($start, $end)
    {
        $work = 0;
        $rest = 0;

        $_start_period = $this->start->copy()->setHour($start)->setMinute(00)->setSecond(00);
        if ($start  == 23) {
            $_start_period = $this->start->copy()->setHour($start)->setMinute(59)->setSecond(59);
            $_end_period = $this->start->copy()->addDays(1)->setHour($end)->setMinute(00)->setSecond(00);
        } else {
            if ($end == 23) {
                $_end_period = $this->start->copy()->setHour($end)->setMinute(59)->setSecond(59);
            } else {
                $_end_period = $this->start->copy()->setHour($end)->setMinute(00)->setSecond(00);
            }
        }

        $maximum = $_start_period->diffInMinutes($_end_period, false);

        // salary calculator
        if ($_start_period >= $this->start) {
            if ($this->end < $_start_period) {
            } else {
                $within = $_start_period->diffInMinutes($this->end, false);
                $work = $within;
            }
        } else {
            if ($this->start >= $_end_period) {
            } else {
                if ($this->end < $_end_period) {
                    $within = $this->start->diffInMinutes($this->end, false);
                    $work = $within;
                } else {
                    $within = $this->start->diffInMinutes($_end_period, false);
                    $work = $within;
                }
            }
        }

        // maximum night time limit
        if ($work > $maximum) {
            $work = $maximum;
        }

        // rest calculator
        if ($_start_period >= $this->rest_start) {
            if ($this->rest_end < $_start_period) {
            } else {
                $within = $_start_period->diffInMinutes($this->rest_end, false);
                $rest = $within;
            }
        } else {
            if ($this->rest_start >= $_end_period) {
            } else {
                if ($this->rest_end < $_end_period) {
                    $within = $this->rest_start->diffInMinutes($this->rest_end, false);
                    $rest = $within;
                } else {
                    $within = $this->rest_start->diffInMinutes($_end_period, false);
                    $rest = $within;
                }
            }
        }

        if ($end == 23) {
            $end = 0;
        }
        if ($start == 23) {
            $start = 0;
        }

        $this->result["$start" . "_" . "$end"] = [
            "is_weekend" => $_end_period->isWeekend(),
            "day"   => $_end_period->dayOfWeek,
            "work" => $work,
            "rest" => $rest,
            "result" => $work - $rest,
        ];
    }

    function result()
    {
        return $this->result;
    }
}
