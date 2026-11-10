<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SurveiGrafikExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected array $items,
        protected float $avgX,
        protected float $avgY,
        protected string $periodeBulan,
        protected string $periodeNama,
        protected int $jumlahResponden
    ) {}

    public function view(): View
    {
        return view('survei-pelanggan.excel-grafik', [
            'items'            => $this->items,
            'avgX'             => $this->avgX,
            'avgY'             => $this->avgY,
            'periodeBulan'     => $this->periodeBulan,
            'periodeNama'      => $this->periodeNama,
            'jumlahResponden'  => $this->jumlahResponden,
        ]);
    }
}

