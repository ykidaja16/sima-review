<?php

namespace App\Exports;

use App\Models\ParameterSop;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Collection $parameters;

    public function __construct(
        protected Collection $penilaians,
        ?Collection $parameters = null
    ) {
        $this->parameters = $parameters ?? ParameterSop::active()->orderBy('urutan')->get();
    }

    public function collection(): Collection
    {
        $canSeeEvaluator = auth()->user()?->isKacab() || auth()->user()?->isSuperAdmin();

        return $this->penilaians->map(function ($p, $i) use ($canSeeEvaluator) {
            $row = [
                'No'            => $i + 1,
                'NIP'           => $p->karyawan?->nip ?? '-',
                'Nama'          => $p->karyawan?->nama ?? '-',
                'Divisi'        => $p->karyawan?->divisi?->nama ?? '-',
                'Jabatan'       => $p->karyawan?->jabatan?->nama ?? '-',
                'Periode'       => $p->periode?->nama ?? '-',
                'Tgl Penilaian' => $p->tanggal_penilaian?->format('d/m/Y') ?? '-',
            ];

            if ($canSeeEvaluator) {
                $row['Evaluator'] = $p->evaluator?->name ?? '-';
            }

            $row['Nilai Akhir'] = number_format($p->nilai_akhir ?? 0, 2);
            $row['Catatan']     = $p->catatan ?? '';

            $detailsMap = $p->details->keyBy('parameter_id');
            foreach ($this->parameters as $param) {
                $detail = $detailsMap->get($param->id);
                $row['param_' . $param->id] = $detail ? (int) $detail->nilai : '-';
            }

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

        foreach ($this->parameters as $param) {
            $headers[] = $param->nama;
        }

        return $headers;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
