<?php

namespace App\Exports;

use App\Models\Ketidaksesuaian;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FtkpExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(protected Ketidaksesuaian $ketidaksesuaian) {}

    public function view(): View
    {
        return view('ketidaksesuaian.excel', [
            'ketidaksesuaian' => $this->ketidaksesuaian,
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A1:H30' => [
                'alignment' => [
                    'wrapText' => true,
                ],
            ],
        ];
    }
}
