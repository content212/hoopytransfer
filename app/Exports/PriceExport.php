<?php

namespace App\Exports;

use App\Price;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PriceExport implements FromQuery, WithEvents, WithTitle, WithHeadings, WithCustomStartCell, WithMapping, WithColumnWidths
{
    use Exportable;

    public function query()
    {
        return Price::select('area', 'zip_code', 'bp_km_price', 'bp_small_6', 'bp_small_3', 'bp_small_2', 'bp_small_express', 'bp_small_timed', 'bp_medium_6', 'bp_medium_3', 'bp_medium_2', 'bp_medium_express', 'bp_medium_timed', 'bp_large_6', 'bp_large_3', 'bp_large_2', 'bp_large_express', 'bp_large_timed', 'lp_km', 'lp_price', 'lp_extra');
    }
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 10,
            'C' => 7,
            'D' => 7,
            'E' => 7,
            'F' => 7,
            'G' => 7,
            'H' => 7,
            'I' => 7,
            'J' => 7,
            'K' => 7,
            'L' => 7,
            'M' => 7,
            'N' => 7,
            'O' => 7,
            'P' => 7,
            'Q' => 7,
            'R' => 7,
            'S' => 7,
            'T' => 7,
            'U' => 7,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $highestRow = $event->getDelegate()->getHighestRow();

                $event->sheet->getStyle('A1:U' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '000000'],
                    ],]
                ]);

                $event->sheet->getStyle('C1:H' . $highestRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('99B7E7');

                $event->sheet->getStyle('I1:M' . $highestRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BBEBE5');

                $event->sheet->getStyle('N1:R' . $highestRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAC4F2');

                $event->sheet->getStyle('S1:U' . $highestRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD966');
            }
        ];
    }
    public function map($price): array
    {
        return [
            $price->area,
            $price->zip_code,
            $price->bp_km_price,
            $price->bp_small_6,
            $price->bp_small_3,
            $price->bp_small_2,
            $price->bp_small_express,
            $price->bp_small_timed,
            $price->bp_medium_6,
            $price->bp_medium_3,
            $price->bp_medium_2,
            $price->bp_medium_express,
            $price->bp_medium_timed,
            $price->bp_large_6,
            $price->bp_large_3,
            $price->bp_large_2,
            $price->bp_large_express,
            $price->bp_large_timed,
            $price->lp_km,
            $price->lp_price,
            $price->lp_extra
        ];
    }
    public function headings(): array
    {
        return [
            'bolge',
            'posta_kodu',
            'bp_km_fiyat',
            'bp_small_6_saat',
            'bp_small_3_saat',
            'bp_small_2_saat',
            'bp_small_express',
            'bp_small_zamanli',
            'bp_medium_6_saat',
            'bp_medium_3_saat',
            'bp_medium_2_saat',
            'bp_medium_express',
            'bp_medium_zamanli',
            'bp_large_6_saat',
            'bp_large_3_saat',
            'bp_large_2_saat',
            'bp_large_express',
            'bp_large_zamanli',
            'lp_km',
            'lp_price',
            'lp_extra'
        ];
    }
    public function startCell(): string
    {
        return 'A1';
    }
    public function styles(Worksheet $sheet)
    {
        return [
            'I,J,K,L,M,N'   => ['font' => ['color' => ['rgb' => 'BBEBE5'],]],
            'N,O,P,Q,R'     => ['font' => ['color' => ['rgb' => 'EAC4F2'],]],
            'S,T,U'         => ['font' => ['color' => ['rgb' => 'FFD966'],]],
        ];
    }

    public function title(): string
    {
        return "price-list";
    }
}
