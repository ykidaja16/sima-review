<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringIndikatorExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(protected array $data) {}

    public function view(): View
    {
        return view('monitoring.indikator-excel', $this->data);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A1:H200' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}
