<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(protected Collection $penilaians) {}

    public function collection(): Collection
    {
        $canSeeEvaluator = auth()->user()?->isKacab() || auth()->user()?->isSuperAdmin();

        return $this->penilaians->map(function ($p, $i) use ($canSeeEvaluator) {
            $row = [
                'No'           => $i + 1,
                'NIP'          => $p->karyawan?->nip ?? '-',
                'Nama'         => $p->karyawan?->nama ?? '-',
                'Divisi'       => $p->karyawan?->divisi?->nama ?? '-',
                'Jabatan'      => $p->karyawan?->jabatan?->nama ?? '-',
                'Periode'      => $p->periode?->nama ?? '-',
                'Tgl Penilaian' => $p->tanggal_penilaian?->format('d/m/Y') ?? '-',
            ];

            if ($canSeeEvaluator) {
                $row['Evaluator'] = $p->evaluator?->name ?? '-';
            }

            $row['Nilai Akhir'] = number_format($p->nilai_akhir ?? 0, 2);
            $row['Catatan']     = $p->catatan ?? '';

            return $row;
        });
    }

    public function headings(): array
    {
        $canSeeEvaluator = auth()->user()?->isKacab() || auth()->user()?->isSuperAdmin();
        $headers = ['No', 'NIP', 'Nama', 'Divisi', 'Jabatan', 'Periode', 'Tgl Penilaian'];
        if ($canSeeEvaluator) {
            $headers[] = 'Evaluator';
        }
        $headers[] = 'Nilai Akhir';
        $headers[] = 'Catatan';

        return $headers;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
