<?php

namespace App\Exports\Sheets;

use App\User;
use App\Lager;
use App\WorkHours;
use App\WorkInput;
use App\WorkLager;
use Carbon\Carbon;
use App\DriverWorkList;
use App\PriceList;
use App\WorkNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class WorkPerDriverSheet implements FromArray, WithEvents, WithTitle, WithStyles, WithHeadings, WithCustomStartCell, WithMapping, WithColumnWidths
{

    private $month;
    private $year;
    private $user_id;
    private $user_name;
    private $company_id;

    public function __construct(int $year, int $month, int $user_id, int $company_id)
    {
        $this->month = $month;
        $this->year  = $year;
        $this->user_id = $user_id;
        $this->user_name = User::select('name')->where('id', $this->user_id)->first()->name;
        $this->company_id = $company_id;
    }


    public function array(): array
    {
        $result = [];
        $groups = WorkHours::select('work_hours.group_id')
            ->join('work_inputs', 'work_inputs.group_id', '=', 'work_hours.group_id')
            ->where('work_inputs.user_id', $this->user_id)
            ->whereMonth('work_hours.date', $this->month)
            ->whereYear('work_hours.date', $this->year)
            ->groupBy('work_hours.group_id')
            ->get();
        $pricelist = PriceList::where('company_id', $this->company_id)->where('is_active', 1)->first();
        foreach ($groups as $group) {
            $group_id = $group->group_id;
            $workhours = WorkHours::select('6_18', '18_22', '22_0', '0_6', 'excuse')->where('group_id', $group_id)->first();

            $lager_id = WorkLager::select('lager_id')->where('group_id', $group_id)->first()->lager_id;
            $lager_name = Lager::select('name')->where('id', $lager_id)->first()->name;

            $excuse = 0;

            if ($workhours->excuse > 0)
                $excuse = 1;

            $start_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'start')->where('group_id', $group_id)->first()->datetime);
            $start_date = $start_datetime->toDateString();
            $start_time = $start_datetime->format('H:i');

            $finish_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'finish')->where('group_id', $group_id)->first()->datetime);
            $finish_date = $finish_datetime->toDateString();
            $finish_time = $finish_datetime->format('H:i');

            $rest_start_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'rest_start')->where('group_id', $group_id)->first()->datetime);
            $rest_start_date = $rest_start_datetime->toDateString();
            $rest_start_time = $rest_start_datetime->format('H:i');

            $rest_finish_datetime = new Carbon(WorkInput::select('datetime')->where('type', 'rest_finish')->where('group_id', $group_id)->first()->datetime);
            $rest_finish_date = $rest_finish_datetime->toDateString();
            $rest_finish_time = $rest_finish_datetime->format('H:i');

            $now = Carbon::now()->hours(0)->minute(0)->second(0)->millisecond(0);
            $interval1 = $now->copy()->addMinute($workhours->{'6_18'});
            $interval2 = $now->copy()->addMinute($workhours->{'18_22'});
            $interval3 = $now->copy()->addMinute($workhours->{'22_0'});
            $interval4 = $now->copy()->addMinute($workhours->{'0_6'});

            $sumhour = $interval1->hour + $interval2->hour + $interval3->hour + $interval4->hour;
            if ($sumhour < 10)
                $sumhour = "0$sumhour";
            $summinute = $interval1->minute + $interval2->minute + $interval3->minute + $interval4->minute;
            if ($summinute < 10)
                $summinute = "0$summinute";

            $sum = "$sumhour:$summinute";



            if ($pricelist->start_date > $start_date) {
                $pricelist = PriceList::where('start_date', '<=', $start_date)->where('end_date', '>=', $start_date)->first();
            }
            $interval1price = 0;
            if ($workhours->{'6_18_day'} < 6) {
                $interval1price = ($workhours->{'6_18'} / 60) * $pricelist->{'1_weekday'};
            } else if ($workhours->{'6_18_day'} == 6) {
                $interval1price = ($workhours->{'6_18'} / 60) * $pricelist->{'1_saturday'};
            } else {
                $interval1price = ($workhours->{'6_18'} / 60) * $pricelist->{'1_sunday'};
            }

            $interval2price = 0;
            if ($workhours->{'18_22_day'} < 6) {
                $interval2price = ($workhours->{'18_22'} / 60) * $pricelist->{'2_weekday'};
            } else if ($workhours->{'18_22_day'} == 6) {
                $interval2price = ($workhours->{'18_22'} / 60) * $pricelist->{'2_saturday'};
            } else {
                $interval2price = ($workhours->{'18_22'} / 60) * $pricelist->{'2_sunday'};
            }

            $interval3price = 0;
            if ($workhours->{'22_0_day'} < 6) {
                $interval3price = ($workhours->{'22_0'} / 60) * $pricelist->{'3_weekday'};
            } else if ($workhours->{'18_22_day'} == 6) {
                $interval3price = ($workhours->{'22_0'} / 60) * $pricelist->{'3_saturday'};
            } else {
                $interval3price = ($workhours->{'22_0'} / 60) * $pricelist->{'4_sunday'};
            }

            $interval4price = 0;
            if ($workhours->{'0_6_day'} < 6) {
                $interval4price = ($workhours->{'0_6'} / 60) * $pricelist->{'4_weekday'};
            } else if ($workhours->{'0_6_day'} == 6) {
                $interval4price = ($workhours->{'0_6'} / 60) * $pricelist->{'4_saturday'};
            } else {
                $interval4price = ($workhours->{'0_6'} / 60) * $pricelist->{'4_sunday'};
            }

            $sumprice = $interval1price + $interval2price + $interval3price + $interval4price;
            if ($workhours->excuse == 1)
                $sumprice = 0;
            else if ($workhours->excuse == 2)
                $sumprice = $sumprice * 0.80;

            $noteinput = WorkNote::select('note', 'admin_note')->where('group_id', $group_id)->first();
            $note = "";
            $admin_note = "";
            if ($noteinput->note)
                $note = $noteinput->note;
            if ($noteinput->admin_note)
                $admin_note = $noteinput->admin_note;

            $data = [
                'lager_name'        => "$lager_name",
                'excuse'            => "$excuse",
                'start_date'        => "$start_date",
                'start_time'        => "$start_time",
                'finish_date'       => "$finish_date",
                'finish_time'       => "$finish_time",
                'rest_start_time'   => "$rest_start_time",
                'rest_finish_time'  => "$rest_finish_time",
                'note'              => $note,
                'admin_note'        => $admin_note,
                'interval1'         => $interval1->format('H:i'),
                'interval2'         => $interval2->format('H:i'),
                'interval3'         => $interval3->format('H:i'),
                'interval4'         => $interval4->format('H:i'),
                'sum'               => "$sum",
                'sumprice'          => "$sumprice"
            ];
            array_push($result, $data);
        }
        return $result;
    }



    public function columnWidths(): array
    {
        return [
            'A' => 9,
            'B' => 4.5,
            'C' => 9,
            'D' => 7,
            'E' => 9,
            'F' => 7,
            'G' => 5,
            'H' => 5,
            'I' => 5,
            'J' => 8,
            'K' => 6,
            'L' => 5,
            'M' => 5,
            'N' => 5,
            'O' => 5,
            'P' => 10,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(57);
                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(24);
                $event->sheet->getDelegate()->getRowDimension(3)->setRowHeight(24);
                $event->sheet->getDelegate()->getRowDimension(4)->setRowHeight(30);

                $event->sheet->getDelegate()->mergeCells('A1:P1');
                $event->sheet->setCellValue('A1', 'Flexy Sverige AB');
                $event->sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('A9D08E');
                $event->sheet->getStyle('A1')->getAlignment()->applyFromArray(
                    array('horizontal' => 'center', 'vertical' => 'center')
                );


                $event->sheet->getDelegate()->mergeCells('A2:B2');
                $event->sheet->setCellValue('A2', 'Personal');
                $event->sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A2:B2')->applyFromArray([
                    'alignment' => array('horizontal' => 'right', 'vertical' => 'center'),
                    'borders' => ['outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['argb' => '000000'],
                    ],]
                ]);



                $event->sheet->getDelegate()->mergeCells('C2:P2');
                $event->sheet->setCellValue('C2', $this->user_name);
                $event->sheet->getStyle('C2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('C2:P2')->applyFromArray([
                    'alignment' => array('horizontal' => 'left', 'vertical' => 'center'),
                    'borders' => ['outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['argb' => '000000'],
                    ],]
                ]);


                $event->sheet->getDelegate()->mergeCells('A3:B3');
                $event->sheet->setCellValue('A3', 'År /Månad');
                $event->sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A3:B3')->applyFromArray([
                    'alignment' => array('horizontal' => 'right', 'vertical' => 'center'),
                    'borders' => ['outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['argb' => '000000'],
                    ],]
                ]);

                $event->sheet->getDelegate()->mergeCells('C3:P3');
                $event->sheet->setCellValue('C3', $this->year . '/' . $this->month);
                $event->sheet->getStyle('C3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('C3:P3')->applyFromArray([
                    'alignment' => array('horizontal' => 'left', 'vertical' => 'center'),
                    'borders' => ['outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['argb' => '000000'],
                    ],]
                ]);

                $event->sheet->getStyle('A4:P36')->applyFromArray([
                    'borders' => ['allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '000000'],
                    ],]
                ]);

                $event->sheet->getStyle('A4:P4')
                    ->getAlignment()
                    ->setWrapText(true);

                $event->sheet->getStyle('A6:P6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A8:P8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A10:P10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A12:P12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A14:P14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A16:P16')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A18:P18')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A20:P20')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A22:P22')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A24:P24')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A26:P26')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A28:P28')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A30:P30')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A32:P32')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A34:P34')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
                $event->sheet->getStyle('A36:P36')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
            }
        ];
    }
    public function map($work): array
    {
        return [
            $work['lager_name'],
            $work['excuse'],
            $work['start_date'],
            $work['start_time'],
            $work['finish_date'],
            $work['finish_time'],
            $work['rest_start_time'],
            $work['rest_finish_time'],
            $work['note'],
            $work['admin_note'],
            $work['sum'],
            $work['interval1'],
            $work['interval2'],
            $work['interval3'],
            $work['interval4'],
            $work['sumprice'],
        ];
    }
    public function headings(): array
    {
        return [
            'Lager',
            'Sjuk',
            'DATUM Start',
            'Start Tid',
            'Datum Sulut',
            'Slut Tid',
            'Rast Start',
            'Rast Slut',
            'Övrigt',
            'Admin Note',
            'Summa',
            '06:00-18:00',
            '18:00-22:00',
            '22:00-00:00',
            '00:00-06:00',
            'Förtjänst (Kr)',
        ];
    }
    public function startCell(): string
    {
        return 'A4';
    }
    public function styles(Worksheet $sheet)
    {
        return [

            1    => ['font' => ['bold' => true, 'size' => '12', 'color' => ['rgb' => 'FFFFFF'],]],
            'A2' => ['font' => ['bold' => true, 'size' => '9', 'color' => ['rgb' => '000000'],]],
            'A3' => ['font' => ['bold' => true, 'size' => '9', 'color' => ['rgb' => '000000'],]],
            'C2' => ['font' => ['bold' => false, 'size' => '9', 'color' => ['rgb' => '000000'],]],
            'C3' => ['font' => ['bold' => false, 'size' => '9', 'color' => ['rgb' => '000000'],]],
            'A4:P36' => ['font' => ['bold' => false, 'size' => '9', 'color' => ['rgb' => '000000']]]
        ];
    }

    public function title(): string
    {
        return $this->user_name;
    }
}
