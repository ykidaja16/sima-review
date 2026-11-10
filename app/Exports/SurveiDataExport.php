<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SurveiDataExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected array $headers,
        protected array $rows,
        protected ?string $search = null
    ) {}

    public function view(): View
    {
        return view('survei-pelanggan.excel-data', [
            'headers' => $this->headers,
            'rows'    => $this->rows,
            'search'  => $this->search,
        ]);
    }
}

