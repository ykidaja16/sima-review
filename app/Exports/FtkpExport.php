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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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
            'A' => 4,  // Buffer margin kiri
            'B' => 13,
            'C' => 13,
            'D' => 17,
            'E' => 17,
            'F' => 17,
            'G' => 16,
            'H' => 16,
            'I' => 16,
            'J' => 4,  // Buffer margin kanan
        ];
    }

    public function drawings(): BaseDrawing|array
    {
        $logoPath = public_path('images/logosima.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/logosima.jpg');
        }

        if (!file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('SIMAlab Logo');
        $drawing->setDescription('SIMAlab Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(46);
        $drawing->setCoordinates('B2');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(4);

        return $drawing;
    }

    public function styles(Worksheet $sheet): array
    {
        // Pengaturan Page Setup & Margins untuk Cetak (Print)
        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
        $sheet->getPageMargins()->setLeft(0.75);
        $sheet->getPageMargins()->setRight(0.75);

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setHorizontalCentered(true);

        // Pengaturan tinggi baris agar isi lapang & proporsional
        $rowHeights = [
            1  => 14, // Spacer atas
            2  => 52, // Header Formulir
            3  => 22, // BAGIAN 1 - PENGGAGAS
            4  => 40, // 3. Penggagas & 4. Departemen
            5  => 65, // 5. Jenis Ketidaksesuaian
            6  => 85, // 6. Penjelasan Temuan & Checklist
            7  => 45, // 7. Penggagas & Manajer Bagian
            8  => 22, // BAGIAN 2 - TINDAKAN
            9  => 45, // 8A/8B & 9. Perkiraan Tgl
            10 => 70, // 10. Akar Masalah
            11 => 80, // 11. Tindakan Korektif & 12. Tindakan Pencegahan
            12 => 22, // BAGIAN 3 - VERIFIKASI
            13 => 50, // 13. Tindakan Efektif | 14. Alasan | 15. FTKP Baru
            14 => 50, // 16. TTD | 17. Tanggal
            15 => 14, // Spacer bawah
        ];

        foreach ($rowHeights as $row => $height) {
            $sheet->getRowDimension($row)->setRowHeight($height);
        }

        return [
            'B2:I14' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
                'font' => [
                    'name' => 'Arial',
                    'size' => 9.5,
                ],
            ],
            'B2:F2' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'B3:I3' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'B8:I8' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'B12:I12' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
