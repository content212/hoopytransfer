<?php

namespace App\Exports;

use App\DriverWorkList;
use App\Exports\Sheets\WorkPerDriverSheet;
use App\WorkHours;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class WorkExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    protected $month;
    protected $user_id;
    protected $company_id;

    public function __construct(int $year, int $month, int $user_id, int $company_id)
    {
        $this->month = $month;
        $this->year = $year;
        $this->user_id = $user_id;
        $this->company_id = $company_id;
    }

    public function sheets(): array
    {
        if ($this->user_id == -1) {
            $drivers = WorkHours::select('work_inputs.user_id')
                ->join('work_inputs', 'work_inputs.group_id', '=', 'work_hours.group_id')
                ->groupBy('work_inputs.user_id')
                ->get();
            $sheets = [];
            foreach ($drivers as $driver) {
                $sheets[] = new WorkPerDriverSheet($this->year, $this->month, $driver->user_id, $this->company_id);
            }

            return $sheets;
        } else {
            $sheets = [];
            $sheets[] = new WorkPerDriverSheet($this->year, $this->month, $this->user_id, $this->company_id);
            return $sheets;
        }
    }
}
