<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SurveiAnalisaExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected array $analysisData,
        protected string $periodeBulan,
        protected string $periodeNama,
        protected int $jumlahResponden,
        protected float $totalNilaiKinerja,
        protected float $totalNilaiKepentingan,
        protected float $avgTpPersen
    ) {}

    public function view(): View
    {
        return view('survei-pelanggan.excel', [
            'analysisData' => $this->analysisData,
            'periodeBulan' => $this->periodeBulan,
            'periodeNama' => $this->periodeNama,
            'jumlahResponden' => $this->jumlahResponden,
            'totalNilaiKinerja' => $this->totalNilaiKinerja,
            'totalNilaiKepentingan' => $this->totalNilaiKepentingan,
            'avgTpPersen' => $this->avgTpPersen,
        ]);
    }
}

