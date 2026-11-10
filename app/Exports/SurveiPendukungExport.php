<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SurveiPendukungExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected array $demografi,
        protected string $semesterLabel,
        protected int $jumlahResponden
    ) {}

    public function view(): View
    {
        return view('survei-pelanggan.excel-pendukung', [
            'demografi'       => $this->demografi,
            'semesterLabel'   => $this->semesterLabel,
            'jumlahResponden' => $this->jumlahResponden,
        ]);
    }
}

