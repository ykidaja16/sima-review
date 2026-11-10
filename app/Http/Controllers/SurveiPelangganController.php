<?php

namespace App\Http\Controllers;

use App\Exports\SurveiAnalisaExport;
use App\Exports\SurveiDataExport;
use App\Exports\SurveiGrafikExport;
use App\Exports\SurveiPendukungExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SurveiPelangganController extends Controller
{
    private const PER_PAGE  = 50;
    private const CACHE_TTL = 300; // 5 menit

    public function index(Request $request): View
    {
        $page   = max(1, (int) $request->get('page', 1));
        $search = trim((string) $request->get('search', ''));

        [$headers, $rows, $error] = $this->fetchData();

        // Cari index kolom Timestamp
        $timestampIndex = null;
        foreach ($headers as $idx => $h) {
            if (mb_strtolower(trim($h)) === 'timestamp') {
                $timestampIndex = $idx;
                break;
            }
        }

        // Jika ada kolom Timestamp, urutkan waktu terbaru dulu lalu format ke tahun bulan tanggal jam:menit (24 jam)
        if ($timestampIndex !== null && count($rows) > 0) {
            usort($rows, function ($a, $b) use ($timestampIndex) {
                $timeA = isset($a[$timestampIndex]) ? strtotime((string) $a[$timestampIndex]) : 0;
                $timeB = isset($b[$timestampIndex]) ? strtotime((string) $b[$timestampIndex]) : 0;
                return $timeB <=> $timeA; // Terbaru di atas
            });

            foreach ($rows as &$r) {
                if (isset($r[$timestampIndex]) && trim((string) $r[$timestampIndex]) !== '') {
                    $ts = strtotime((string) $r[$timestampIndex]);
                    if ($ts) {
                        $r[$timestampIndex] = date('Y-m-d H:i', $ts);
                    }
                }
            }
            unset($r);
        }

        // Filter pencarian jika ada
        if ($search !== '' && count($rows) > 0) {
            $keyword = mb_strtolower($search);
            $rows = array_values(array_filter($rows, function (array $row) use ($keyword) {
                foreach ($row as $cell) {
                    if (str_contains(mb_strtolower((string) $cell), $keyword)) {
                        return true;
                    }
                }
                return false;
            }));
        }

        $total      = count($rows);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * self::PER_PAGE;
        $pagedRows  = array_slice($rows, $offset, self::PER_PAGE);

        return view('survei-pelanggan.index', compact(
            'headers', 'pagedRows', 'total', 'page', 'totalPages', 'search', 'error'
        ));
    }

    /**
     * Export Excel Data Mentah Survei Pelanggan
     */
    public function exportDataExcel(Request $request): BinaryFileResponse
    {
        $search = trim((string) $request->get('search', ''));
        [$headers, $rows, $error] = $this->fetchData();

        if ($search !== '' && count($rows) > 0) {
            $keyword = mb_strtolower($search);
            $rows = array_values(array_filter($rows, function (array $row) use ($keyword) {
                foreach ($row as $cell) {
                    if (str_contains(mb_strtolower((string) $cell), $keyword)) {
                        return true;
                    }
                }
                return false;
            }));
        }

        $filename = 'Data-Survei-Pelanggan-' . date('Ymd-His') . '.xlsx';

        return Excel::download(new SurveiDataExport($headers, $rows, $search ?: null), $filename);
    }

    /**
     * Export PDF Data Mentah Survei Pelanggan
     */
    public function exportDataPdf(Request $request): Response
    {
        $search = trim((string) $request->get('search', ''));
        [$headers, $rows, $error] = $this->fetchData();

        if ($search !== '' && count($rows) > 0) {
            $keyword = mb_strtolower($search);
            $rows = array_values(array_filter($rows, function (array $row) use ($keyword) {
                foreach ($row as $cell) {
                    if (str_contains(mb_strtolower((string) $cell), $keyword)) {
                        return true;
                    }
                }
                return false;
            }));
        }

        $filename = 'Data-Survei-Pelanggan-' . date('Ymd-His') . '.pdf';
        $pdf = Pdf::loadView('survei-pelanggan.pdf-data', [
            'headers' => $headers,
            'rows'    => $rows,
            'search'  => $search ?: null,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Sub menu analisa data survei pelanggan per semester
     */
    public function analisa(Request $request): View
    {
        $calc = $this->getAnalysisCalculation($request);

        return view('survei-pelanggan.analisa', $calc);
    }

    /**
     * Export Excel Analisa Survei Pelanggan
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $calc = $this->getAnalysisCalculation($request);
        $filename = 'Analisa-Survei-Pelanggan-' . str_replace([' ', '/'], '-', $calc['periodeBulan']) . '.xlsx';

        return Excel::download(new SurveiAnalisaExport(
            $calc['analysisData'],
            $calc['periodeBulan'],
            $calc['periodeNama'],
            $calc['jumlahResponden'],
            $calc['totalNilaiKinerja'],
            $calc['totalNilaiKepentingan'],
            $calc['avgTpPersen']
        ), $filename);
    }

    /**
     * Export PDF Analisa Survei Pelanggan
     */
    public function exportPdf(Request $request): Response
    {
        $calc = $this->getAnalysisCalculation($request);
        $filename = 'Analisa-Survei-Pelanggan-' . str_replace([' ', '/'], '-', $calc['periodeBulan']) . '.pdf';

        $pdf = Pdf::loadView('survei-pelanggan.pdf', $calc)->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Sub menu internal grafik (Diagram Kartesius IPA)
     */
    public function grafik(Request $request): View
    {
        $calc = $this->getGrafikCalculation($request);

        return view('survei-pelanggan.grafik', $calc);
    }

    /**
     * Export Excel Internal Grafik (IPA)
     */
    public function exportGrafikExcel(Request $request): BinaryFileResponse
    {
        $calc = $this->getGrafikCalculation($request);
        $filename = 'Internal-Grafik-Survei-' . str_replace([' ', '/'], '-', $calc['periodeBulan']) . '.xlsx';

        return Excel::download(new SurveiGrafikExport(
            $calc['items'],
            $calc['avgX'],
            $calc['avgY'],
            $calc['periodeBulan'],
            $calc['periodeNama'],
            $calc['jumlahResponden']
        ), $filename);
    }

    /**
     * Export PDF Internal Grafik (IPA)
     */
    public function exportGrafikPdf(Request $request): Response
    {
        $calc = $this->getGrafikCalculation($request);
        $filename = 'Internal-Grafik-Survei-' . str_replace([' ', '/'], '-', $calc['periodeBulan']) . '.pdf';

        $pdf = Pdf::loadView('survei-pelanggan.pdf-grafik', $calc)->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Sub menu Data Pendukung (Profil Demografi Responden)
     */
    public function pendukung(Request $request): View
    {
        $calc = $this->getDemografiCalculation($request);

        return view('survei-pelanggan.pendukung', $calc);
    }

    /**
     * Export Excel Data Pendukung (Demografi)
     */
    public function exportPendukungExcel(Request $request): BinaryFileResponse
    {
        $calc = $this->getDemografiCalculation($request);
        $filename = 'Data-Pendukung-Survei-' . str_replace(' ', '-', $calc['semesterLabel']) . '.xlsx';

        return Excel::download(new SurveiPendukungExport(
            $calc['demografi'],
            $calc['semesterLabel'],
            $calc['jumlahResponden']
        ), $filename);
    }

    /**
     * Export PDF Data Pendukung (Demografi)
     */
    public function exportPendukungPdf(Request $request): Response
    {
        $calc = $this->getDemografiCalculation($request);
        $filename = 'Data-Pendukung-Survei-' . str_replace(' ', '-', $calc['semesterLabel']) . '.pdf';

        $pdf = Pdf::loadView('survei-pelanggan.pdf-pendukung', $calc)->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Helper perhitungan analisa data survei
     */
    private function getAnalysisCalculation(Request $request): array
    {
        [$headers, $rows, $error] = $this->fetchData();

        // Cari index kolom Timestamp
        $timestampIndex = 0;
        foreach ($headers as $idx => $h) {
            if (mb_strtolower(trim($h)) === 'timestamp') {
                $timestampIndex = $idx;
                break;
            }
        }

        // Kumpulkan tahun yang tersedia dari data
        $availableYears = [];
        foreach ($rows as $row) {
            $ts = $row[$timestampIndex] ?? '';
            if ($ts) {
                $time = strtotime((string) $ts);
                if ($time) {
                    $availableYears[(int) date('Y', $time)] = true;
                }
            }
        }

        $availableYears = array_keys($availableYears);
        rsort($availableYears);

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $selectedYear = (int) $request->get('tahun', $availableYears[0]);
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $availableYears[0];
        }

        $selectedSemester = (int) $request->get('semester', 1);
        if ($selectedSemester !== 1 && $selectedSemester !== 2) {
            $selectedSemester = 1;
        }

        // Filter baris sesuai semester & tahun
        $filteredRows = [];
        foreach ($rows as $row) {
            $ts = $row[$timestampIndex] ?? '';
            if ($ts) {
                $time = strtotime((string) $ts);
                if ($time) {
                    $y = (int) date('Y', $time);
                    $m = (int) date('m', $time);
                    $sem = $m <= 6 ? 1 : 2;
                    if ($y === $selectedYear && $sem === $selectedSemester) {
                        $filteredRows[] = $row;
                    }
                }
            }
        }

        $jumlahResponden = count($filteredRows);

        // Kriteria Kualitas Layanan (9 item)
        $criteriaList = [
            1 => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya',
            2 => 'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan',
            3 => 'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan',
            4 => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan',
            5 => 'Kenyamanan, kebersihan & kerapian area layanan dan ruang pemeriksaan',
            6 => 'Bagaimana pendapat Saudara tentang kompetensi/ kemampuan petugas dalam pelayanan',
            7 => 'Bagaimana pendapat Saudara perilaku petugas dalam pelayanan terkait kesopanan dan keramahan',
            8 => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana',
            9 => 'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan',
        ];

        // Petakan kolom Kinerja & Kepentingan
        $criteriaCols = [];
        foreach ($criteriaList as $num => $label) {
            $kCol = null;
            $iCol = null;
            foreach ($headers as $idx => $h) {
                $hl = mb_strtolower($h);
                if (str_starts_with(trim($h), "$num.") || str_contains($h, "$num.")) {
                    if (str_contains($hl, 'kinerja')) {
                        $kCol = $idx;
                    } elseif (str_contains($hl, 'kepentingan')) {
                        $iCol = $idx;
                    }
                }
            }
            if ($kCol === null) $kCol = 8 + ($num - 1) * 2;
            if ($iCol === null) $iCol = 9 + ($num - 1) * 2;
            $criteriaCols[$num] = ['label' => $label, 'kCol' => $kCol, 'iCol' => $iCol];
        }

        $analysisData = [];
        $totalNilaiKinerja = 0;
        $totalNilaiKepentingan = 0;

        foreach ($criteriaCols as $num => $c) {
            $kCounts = [4 => 0, 3 => 0, 2 => 0, 1 => 0];
            $iCounts = [4 => 0, 3 => 0, 2 => 0, 1 => 0];

            foreach ($filteredRows as $r) {
                $kv = (int) ($r[$c['kCol']] ?? 0);
                $iv = (int) ($r[$c['iCol']] ?? 0);
                if (isset($kCounts[$kv])) $kCounts[$kv]++;
                if (isset($iCounts[$iv])) $iCounts[$iv]++;
            }

            // Bobot: SP=4, P=3, TP=2, STP=1. Nilai = Total Skor / 4
            $kNilai = ($kCounts[4] * 4 + $kCounts[3] * 3 + $kCounts[2] * 2 + $kCounts[1] * 1) / 4;
            $iNilai = ($iCounts[4] * 4 + $iCounts[3] * 3 + $iCounts[2] * 2 + $iCounts[1] * 1) / 4;
            $tpPersen = $iNilai > 0 ? ($kNilai / $iNilai) * 100 : 0;

            $totalNilaiKinerja += $kNilai;
            $totalNilaiKepentingan += $iNilai;

            $analysisData[] = [
                'no' => $num,
                'label' => $c['label'],
                'k_sp' => $kCounts[4],
                'k_p' => $kCounts[3],
                'k_tp' => $kCounts[2],
                'k_stp' => $kCounts[1],
                'k_nilai' => $kNilai,
                'i_sp' => $iCounts[4],
                'i_p' => $iCounts[3],
                'i_tp' => $iCounts[2],
                'i_stp' => $iCounts[1],
                'i_nilai' => $iNilai,
                'tp_persen' => $tpPersen,
            ];
        }

        $avgTpPersen = count($analysisData) > 0 ? array_sum(array_column($analysisData, 'tp_persen')) / count($analysisData) : 0;

        $periodeNama = $selectedSemester === 1 ? 'Periode I ( Januari - Juni ' . $selectedYear . ' )' : 'Periode II ( Juli - Desember ' . $selectedYear . ' )';
        $periodeBulan = $selectedSemester === 1 ? 'JANUARI - JUNI ' . $selectedYear : 'JULI - DESEMBER ' . $selectedYear;

        return compact(
            'analysisData', 'availableYears', 'selectedYear', 'selectedSemester',
            'periodeNama', 'periodeBulan', 'jumlahResponden', 'totalNilaiKinerja',
            'totalNilaiKepentingan', 'avgTpPersen', 'error'
        );
    }

    /**
     * Helper perhitungan internal grafik (Diagram Kartesius IPA)
     */
    private function getGrafikCalculation(Request $request): array
    {
        [$headers, $rows, $error] = $this->fetchData();

        // Cari index kolom Timestamp
        $timestampIndex = 0;
        foreach ($headers as $idx => $h) {
            if (mb_strtolower(trim($h)) === 'timestamp') {
                $timestampIndex = $idx;
                break;
            }
        }

        // Kumpulkan tahun yang tersedia dari data
        $availableYears = [];
        foreach ($rows as $row) {
            $ts = $row[$timestampIndex] ?? '';
            if ($ts) {
                $time = strtotime((string) $ts);
                if ($time) {
                    $availableYears[(int) date('Y', $time)] = true;
                }
            }
        }

        $availableYears = array_keys($availableYears);
        rsort($availableYears);

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $selectedYear = (int) $request->get('tahun', $availableYears[0]);
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $availableYears[0];
        }

        $selectedSemester = (int) $request->get('semester', 1);
        if ($selectedSemester !== 1 && $selectedSemester !== 2) {
            $selectedSemester = 1;
        }

        // Filter baris sesuai semester & tahun
        $filteredRows = [];
        foreach ($rows as $row) {
            $ts = $row[$timestampIndex] ?? '';
            if ($ts) {
                $time = strtotime((string) $ts);
                if ($time) {
                    $y = (int) date('Y', $time);
                    $m = (int) date('m', $time);
                    $sem = $m <= 6 ? 1 : 2;
                    if ($y === $selectedYear && $sem === $selectedSemester) {
                        $filteredRows[] = $row;
                    }
                }
            }
        }

        $jumlahResponden = count($filteredRows);

        // Kriteria Kualitas Layanan (9 item)
        $criteriaList = [
            1 => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya',
            2 => 'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan',
            3 => 'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan',
            4 => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan',
            5 => 'Kenyamanan, kebersihan & kerapian area layanan dan ruang pemeriksaan',
            6 => 'Bagaimana pendapat Saudara tentang kompetensi/ kemampuan petugas dalam pelayanan',
            7 => 'Bagaimana pendapat Saudara perilaku petugas dalam pelayanan terkait kesopanan dan keramahan',
            8 => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana',
            9 => 'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan',
        ];

        // Petakan kolom Kinerja & Kepentingan
        $criteriaCols = [];
        foreach ($criteriaList as $num => $label) {
            $kCol = null;
            $iCol = null;
            foreach ($headers as $idx => $h) {
                $hl = mb_strtolower($h);
                if (str_starts_with(trim($h), "$num.") || str_contains($h, "$num.")) {
                    if (str_contains($hl, 'kinerja')) {
                        $kCol = $idx;
                    } elseif (str_contains($hl, 'kepentingan')) {
                        $iCol = $idx;
                    }
                }
            }
            if ($kCol === null) $kCol = 8 + ($num - 1) * 2;
            if ($iCol === null) $iCol = 9 + ($num - 1) * 2;
            $criteriaCols[$num] = ['label' => $label, 'kCol' => $kCol, 'iCol' => $iCol];
        }

        $rawItems = [];
        foreach ($criteriaCols as $num => $c) {
            $kCounts = [4 => 0, 3 => 0, 2 => 0, 1 => 0];
            $iCounts = [4 => 0, 3 => 0, 2 => 0, 1 => 0];

            foreach ($filteredRows as $r) {
                $kv = (int) ($r[$c['kCol']] ?? 0);
                $iv = (int) ($r[$c['iCol']] ?? 0);
                if (isset($kCounts[$kv])) $kCounts[$kv]++;
                if (isset($iCounts[$iv])) $iCounts[$iv]++;
            }

            // Bobot: SP=4, P=3, TP=2, STP=1. Nilai = Total Skor / 4
            $kNilai = ($kCounts[4] * 4 + $kCounts[3] * 3 + $kCounts[2] * 2 + $kCounts[1] * 1) / 4;
            $iNilai = ($iCounts[4] * 4 + $iCounts[3] * 3 + $iCounts[2] * 2 + $iCounts[1] * 1) / 4;
            $kesesuaian = $iNilai > 0 ? ($kNilai / $iNilai) * 100 : 0;

            // Sumb X & Sumb Y (skala nilai / 10)
            $sumbX = round($kNilai / 10, 2);
            $sumbY = round($iNilai / 10, 2);

            $rawItems[] = [
                'no' => $num,
                'uraian' => $c['label'],
                'nilai_kinerja' => $kNilai,
                'nilai_kepentingan' => $iNilai,
                'kesesuaian' => $kesesuaian,
                'sumb_x' => $sumbX,
                'sumb_y' => $sumbY,
            ];
        }

        // Hitung Rata-rata Nilai X dan Y
        $count = count($rawItems);
        $avgX = $count > 0 ? round(array_sum(array_column($rawItems, 'sumb_x')) / $count, 2) : 0;
        $avgY = $count > 0 ? round(array_sum(array_column($rawItems, 'sumb_y')) / $count, 2) : 0;

        // Tentukan Kuadran IPA
        $items = [];
        $chartPoints = [];
        foreach ($rawItems as $item) {
            $x = $item['sumb_x'];
            $y = $item['sumb_y'];

            if ($x >= $avgX && $y >= $avgY) {
                $kuadran = 'B';
            } elseif ($x < $avgX && $y >= $avgY) {
                $kuadran = 'A';
            } elseif ($x < $avgX && $y < $avgY) {
                $kuadran = 'C';
            } else {
                $kuadran = 'D';
            }

            $item['kuadran'] = $kuadran;
            $items[] = $item;

            $chartPoints[] = [
                'x' => $x,
                'y' => $y,
                'no' => $item['no'],
                'label' => $item['uraian'],
                'kuadran' => $kuadran,
            ];
        }

        $semesterLabel = 'Semester ' . $selectedSemester . ' Tahun ' . $selectedYear;
        $periodeNama = $selectedSemester === 1 ? 'Periode I ( Januari - Juni ' . $selectedYear . ' )' : 'Periode II ( Juli - Desember ' . $selectedYear . ' )';
        $periodeBulan = $selectedSemester === 1 ? 'JANUARI - JUNI ' . $selectedYear : 'JULI - DESEMBER ' . $selectedYear;

        return compact(
            'items', 'avgX', 'avgY', 'chartPoints', 'availableYears', 'selectedYear',
            'selectedSemester', 'semesterLabel', 'periodeNama', 'periodeBulan', 'jumlahResponden', 'error'
        );
    }

    /**
     * Helper perhitungan demografi responden survei pelanggan
     */
    private function getDemografiCalculation(Request $request): array
    {
        [$headers, $rows, $error] = $this->fetchData();

        // Cari index kolom Timestamp
        $timestampIndex = 0;
        foreach ($headers as $idx => $h) {
            if (mb_strtolower(trim($h)) === 'timestamp') {
                $timestampIndex = $idx;
                break;
            }
        }

        // Kumpulkan tahun yang tersedia
        $availableYears = [];
        foreach ($rows as $row) {
            $ts = $row[$timestampIndex] ?? '';
            if ($ts) {
                $time = strtotime((string) $ts);
                if ($time) {
                    $availableYears[(int) date('Y', $time)] = true;
                }
            }
        }

        $availableYears = array_keys($availableYears);
        rsort($availableYears);

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $selectedYear = (int) $request->get('tahun', $availableYears[0]);
        if (!in_array($selectedYear, $availableYears)) {
            $selectedYear = $availableYears[0];
        }

        $selectedSemester = (int) $request->get('semester', 1);
        if ($selectedSemester !== 1 && $selectedSemester !== 2) {
            $selectedSemester = 1;
        }

        // Filter baris sesuai semester & tahun
        $filteredRows = [];
        foreach ($rows as $row) {
            $ts = $row[$timestampIndex] ?? '';
            if ($ts) {
                $time = strtotime((string) $ts);
                if ($time) {
                    $y = (int) date('Y', $time);
                    $m = (int) date('m', $time);
                    $sem = $m <= 6 ? 1 : 2;
                    if ($y === $selectedYear && $sem === $selectedSemester) {
                        $filteredRows[] = $row;
                    }
                }
            }
        }

        $jumlahResponden = count($filteredRows);

        // 1. Jenis Kelamin (Col 3)
        $genderCounts = ['Laki-laki' => 0, 'Perempuan' => 0];
        // 2. Usia (Col 5)
        $usiaCounts = ['0-19' => 0, '20-40' => 0, '41-60' => 0, '>60' => 0];
        // 3. Pendidikan (Col 4)
        $pendidikanCounts = ['SD' => 0, 'SMP' => 0, 'SMA' => 0, 'D3' => 0, 'D4/S1' => 0, 'S2' => 0, 'S3' => 0];
        // 4. Pekerjaan (Col 6)
        $pekerjaanCounts = ['PNS' => 0, 'TNI' => 0, 'POLRI' => 0, 'SWASTA' => 0, 'WIRAUSAHA' => 0, 'LAINNYA' => 0];

        foreach ($filteredRows as $r) {
            // Gender
            $g = trim((string) ($r[3] ?? ''));
            if (str_contains(mb_strtolower($g), 'laki')) {
                $genderCounts['Laki-laki']++;
            } elseif (str_contains(mb_strtolower($g), 'perempuan')) {
                $genderCounts['Perempuan']++;
            }

            // Usia
            $uVal = (int) trim((string) ($r[5] ?? ''));
            if ($uVal > 0) {
                if ($uVal <= 19) $usiaCounts['0-19']++;
                elseif ($uVal <= 40) $usiaCounts['20-40']++;
                elseif ($uVal <= 60) $usiaCounts['41-60']++;
                else $usiaCounts['>60']++;
            }

            // Pendidikan
            $pVal = strtoupper(trim((string) ($r[4] ?? '')));
            if ($pVal === 'SD') $pendidikanCounts['SD']++;
            elseif ($pVal === 'SMP') $pendidikanCounts['SMP']++;
            elseif ($pVal === 'SMA' || $pVal === 'SMK' || $pVal === 'SLTA') $pendidikanCounts['SMA']++;
            elseif ($pVal === 'D3' || $pVal === 'D-3' || $pVal === 'DIPLOMA') $pendidikanCounts['D3']++;
            elseif (str_contains($pVal, 'S1') || str_contains($pVal, 'D4')) $pendidikanCounts['D4/S1']++;
            elseif ($pVal === 'S2') $pendidikanCounts['S2']++;
            elseif ($pVal === 'S3') $pendidikanCounts['S3']++;

            // Pekerjaan
            $kVal = strtoupper(trim((string) ($r[6] ?? '')));
            if ($kVal === 'PNS' || $kVal === 'ASN') {
                $pekerjaanCounts['PNS']++;
            } elseif ($kVal === 'TNI') {
                $pekerjaanCounts['TNI']++;
            } elseif ($kVal === 'POLRI') {
                $pekerjaanCounts['POLRI']++;
            } elseif (str_contains($kVal, 'SWASTA')) {
                $pekerjaanCounts['SWASTA']++;
            } elseif (str_contains($kVal, 'WIRAUSAHA') || str_contains($kVal, 'WIRASWASTA')) {
                $pekerjaanCounts['WIRAUSAHA']++;
            } else {
                $pekerjaanCounts['LAINNYA']++;
            }
        }

        // Hitung persentase
        $demografi = [
            'gender' => array_map(fn($k, $v) => [
                'label' => $k,
                'count' => $v,
                'pct' => $jumlahResponden > 0 ? round(($v / $jumlahResponden) * 100) : 0,
            ], array_keys($genderCounts), $genderCounts),
            'usia' => array_map(fn($k, $v) => [
                'label' => $k,
                'count' => $v,
                'pct' => $jumlahResponden > 0 ? round(($v / $jumlahResponden) * 100) : 0,
            ], array_keys($usiaCounts), $usiaCounts),
            'pendidikan' => array_map(fn($k, $v) => [
                'label' => $k,
                'count' => $v,
                'pct' => $jumlahResponden > 0 ? round(($v / $jumlahResponden) * 100) : 0,
            ], array_keys($pendidikanCounts), $pendidikanCounts),
            'pekerjaan' => array_map(fn($k, $v) => [
                'label' => $k,
                'count' => $v,
                'pct' => $jumlahResponden > 0 ? round(($v / $jumlahResponden) * 100) : 0,
            ], array_keys($pekerjaanCounts), $pekerjaanCounts),
        ];

        $semesterLabel = 'Semester ' . $selectedSemester . ' Tahun ' . $selectedYear;

        return compact(
            'demografi', 'availableYears', 'selectedYear', 'selectedSemester',
            'semesterLabel', 'jumlahResponden', 'error'
        );
    }

    /**
     * Ambil data dari Google Apps Script dengan caching.
     * Apps Script mengembalikan: { "success": true, "data": [[row1], [row2], ...] }
     * Baris pertama data = header.
     *
     * @return array{0: string[], 1: array<int, string[]>, 2: string|null}
     */
    private function fetchData(): array
    {
        $url   = config('services.google_survey.url');
        $token = config('services.google_survey.token');

        if (!$url || !$token) {
            return [[], [], 'Konfigurasi GOOGLE_SURVEY_URL atau GOOGLE_SURVEY_TOKEN belum diatur di .env.'];
        }

        $cacheKey = 'survei_pelanggan_data';

        try {
            $cached = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($url, $token) {
                $response = Http::timeout(15)
                    ->withOptions(['verify' => false]) // Apps Script redirect HTTPS
                    ->get($url, ['token' => $token]);

                if (!$response->successful()) {
                    return null;
                }

                $json = $response->json();

                // Validasi struktur JSON
                if (!isset($json['success']) || $json['success'] !== true) {
                    return ['__error__' => $json['message'] ?? 'Apps Script mengembalikan success: false.'];
                }

                if (!isset($json['data']) || !is_array($json['data']) || count($json['data']) === 0) {
                    return ['__data__' => []];
                }

                return ['__data__' => $json['data']];
            });

            if ($cached === null) {
                return [[], [], 'Gagal terhubung ke Google Apps Script. Periksa URL dan token di .env.'];
            }

            if (isset($cached['__error__'])) {
                return [[], [], $cached['__error__']];
            }

            $allRows = $cached['__data__'] ?? [];

            if (empty($allRows)) {
                return [[], [], null];
            }

            $firstRow = reset($allRows);

            // Deteksi format: array of objects (associative) atau array of arrays (indexed)
            if (is_array($firstRow) && array_keys($firstRow) !== range(0, count($firstRow) - 1)) {
                // Format: array of objects → keys = header, values = data
                $headers = array_map('strval', array_keys($firstRow));
                $rows    = array_values(array_map(
                    fn($row) => array_map('strval', array_values($row)),
                    $allRows
                ));
            } else {
                // Format: array of arrays → baris pertama = header
                $headers = array_map('strval', array_shift($allRows));
                $colCount = count($headers);
                $rows = array_values(array_map(
                    fn(array $row) => array_pad(array_map('strval', $row), $colCount, ''),
                    $allRows
                ));
            }

            // Filter baris yang semua cell-nya kosong
            $rows = array_values(array_filter(
                $rows,
                fn(array $row) => count(array_filter($row, fn($c) => trim($c) !== '')) > 0
            ));

            return [$headers, $rows, null];

        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
            return [[], [], 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
