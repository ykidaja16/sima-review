<?php

namespace App\Exports;

use App\Models\Ketidaksesuaian;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FtkpExport implements FromView, WithColumnWidths, WithDrawings, WithStyles
{
    public function __construct(protected Ketidaksesuaian $ketidaksesuaian) {}

    public function view(): View
    {
        return view('ketidaksesuaian.excel', [
            'ketidaksesuaian' => $this->ketidaksesuaian,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 14,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 16,
            'G' => 16,
            'H' => 16,
        ];
    }

    public function drawings(): BaseDrawing|array
    {
        $logoPath = public_path('images/logosima.png');
        if (!file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('SIMAlab Logo');
        $drawing->setDescription('SIMAlab Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(46);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(4);

        return $drawing;
    }

    public function styles(Worksheet $sheet): array
    {
        // Pengaturan tinggi baris agar isi tidak terhimpit
        $rowHeights = [
            1  => 52, // Header: Logo, Title, FTKP No/Date
            2  => 22, // BAGIAN 1 - PENGGAGAS
            3  => 40, // 3. Penggagas & 4. Departemen
            4  => 65, // 5. Jenis Ketidaksesuaian (3 checkboxes)
            5  => 85, // 6. Penjelasan Temuan & OK/OBSERVASI/NC
            6  => 45, // 7. Penggagas & Manajer Bagian
            7  => 22, // BAGIAN 2 - TINDAKAN
            8  => 45, // 8A. Departemen & 8B. Manajer | 9. Perkiraan Tgl
            9  => 70, // 10. Akar Masalah
            10 => 80, // 11. Tindakan Korektif & 12. Tindakan Pencegahan
            11 => 22, // BAGIAN 3 - VERIFIKASI
            12 => 50, // 13. Tindakan Efektif | 14. Alasan | 15. FTKP Baru
            13 => 50, // 16. TTD Verifikasi | 17. Tanggal
        ];

        foreach ($rowHeights as $row => $height) {
            $sheet->getRowDimension($row)->setRowHeight($height);
        }

        return [
            'A1:H13' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
                'font' => [
                    'name' => 'Arial',
                    'size' => 9.5,
                ],
            ],
            'A1:E1' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'A2:H2' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'A7:H7' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'A11:H11' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
