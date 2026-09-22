<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

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
