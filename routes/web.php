<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KetidaksesuaianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Master\CabangController;
use App\Http\Controllers\Master\DivisiController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\JenisKetidaksesuaianController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Master\KategoriNilaiController;
use App\Http\Controllers\Master\ParameterSopController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PeriodePenilaianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveiPelangganController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// =========================================
// PUBLIC: Redirect root ke login atau dashboard
// =========================================
Route::get('/', fn() => redirect()->route('dashboard'));

// =========================================
// AUTHENTICATION
// =========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// =========================================
// AUTHENTICATED ROUTES
// =========================================
Route::middleware('auth')->group(function () {

    // Dashboard (semua role, view berbeda-beda)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Master Data (Super Admin only) ----
    Route::middleware('role:super_admin')->prefix('master')->name('master.')->group(function () {
        Route::resource('divisi', DivisiController::class)->except(['show']);
        Route::resource('jabatan', JabatanController::class)->except(['show']);
        Route::resource('karyawan', KaryawanController::class);
        Route::resource('parameter-sop', ParameterSopController::class)->except(['show']);
        Route::resource('kategori-nilai', KategoriNilaiController::class)->except(['show']);
        Route::resource('cabang', CabangController::class)->except(['show']);
        Route::resource('jenis-ketidaksesuaian', JenisKetidaksesuaianController::class)->except(['show']);
    });

    // ---- User Management (Super Admin only) ----
    Route::middleware('role:super_admin')->prefix('user-management')->name('user-management.')->group(function () {
        Route::resource('/', UserController::class)->except(['show'])->parameters(['' => 'user']);
    });

    // ---- Audit Log (Super Admin only) ----
    Route::middleware('role:super_admin')
        ->get('/audit-log', [AuditLogController::class, 'index'])
        ->name('audit-log.index');

    // ---- Periode Penilaian (Super Admin + Kacab) ----
    Route::middleware('role:super_admin,kacab')->group(function () {
        Route::resource('periode', PeriodePenilaianController::class)->except(['show']);
        Route::patch('/periode/{periode}/status', [PeriodePenilaianController::class, 'updateStatus'])
            ->name('periode.update-status');
    });

    // ---- Penilaian CRUD (Super Admin, Manager, Kacab, Supervisor) ----
    Route::middleware('role:super_admin,manager,kacab,supervisor')->group(function () {
        Route::resource('penilaian', PenilaianController::class)->except(['show']);
    });

    // ---- Penilaian Show & Tanggapan (Bisa diakses semua role termasuk pelaksana untuk melihat & menanggapi penilaian sendiri) ----
    Route::get('/penilaian/{penilaian}', [PenilaianController::class, 'show'])
        ->whereNumber('penilaian')
        ->name('penilaian.show');
    Route::post('/penilaian/{penilaian}/tanggapan', [PenilaianController::class, 'tanggapan'])
        ->whereNumber('penilaian')
        ->name('penilaian.tanggapan');

    // ---- Monitoring ----
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        // Individu: semua role (pelaksana dibatasi ke diri sendiri oleh middleware)
        Route::get('/individu', [MonitoringController::class, 'individu'])
            ->middleware('pelaksana.access')
            ->name('individu');

        // Tim, Divisi, Ranking: khusus manager ke atas & mutu
        Route::middleware('role:super_admin,manager,kacab,supervisor,mutu')->group(function () {
            Route::get('/tim', [MonitoringController::class, 'tim'])->name('tim');
            Route::get('/divisi', [MonitoringController::class, 'divisi'])->name('divisi');
            Route::get('/ranking', [MonitoringController::class, 'ranking'])->name('ranking');
        });
    });

    // ---- Laporan (Super Admin, Manager, Kacab, Supervisor, Mutu) ----
    Route::middleware('role:super_admin,manager,kacab,supervisor,mutu')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/print', [LaporanController::class, 'print'])->name('print');
    });

    // ---- Ketidaksesuaian / FTKP (Semua role yang login) ----
    Route::prefix('ketidaksesuaian')->name('ketidaksesuaian.')->group(function () {
        Route::get('/', [KetidaksesuaianController::class, 'index'])->name('index');
        Route::get('/create', [KetidaksesuaianController::class, 'create'])->name('create');
        Route::post('/', [KetidaksesuaianController::class, 'store'])->name('store');
        Route::get('/{ketidaksesuaian}', [KetidaksesuaianController::class, 'show'])->name('show');
        Route::get('/{ketidaksesuaian}/export-pdf', [KetidaksesuaianController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{ketidaksesuaian}/export-excel', [KetidaksesuaianController::class, 'exportExcel'])->name('export-excel');
        Route::post('/{ketidaksesuaian}/tindaklanjut', [KetidaksesuaianController::class, 'tindaklanjut'])->name('tindaklanjut');
        Route::post('/{ketidaksesuaian}/verifikasi', [KetidaksesuaianController::class, 'verifikasi'])->name('verifikasi');
        Route::delete('/{ketidaksesuaian}', [KetidaksesuaianController::class, 'destroy'])
            ->middleware('role:super_admin')
            ->name('destroy');
    });

    // ---- Survei Pelanggan (Data Survei: semua role; Analisa data & export analisa: Superadmin, Kacab, Mutu) ----
    Route::prefix('survei-pelanggan')->name('survei-pelanggan.')->group(function () {
        Route::get('/', [SurveiPelangganController::class, 'index'])->name('index');
        Route::get('/export-data-excel', [SurveiPelangganController::class, 'exportDataExcel'])->name('export-data-excel');
        Route::get('/export-data-pdf', [SurveiPelangganController::class, 'exportDataPdf'])->name('export-data-pdf');

        Route::middleware('role:super_admin,kacab,mutu')->group(function () {
            Route::get('/analisa', [SurveiPelangganController::class, 'analisa'])->name('analisa');
            Route::get('/grafik', [SurveiPelangganController::class, 'grafik'])->name('grafik');
            Route::get('/pendukung', [SurveiPelangganController::class, 'pendukung'])->name('pendukung');
            Route::get('/export-excel', [SurveiPelangganController::class, 'exportExcel'])->name('export-excel');
            Route::get('/export-pdf', [SurveiPelangganController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/grafik/export-excel', [SurveiPelangganController::class, 'exportGrafikExcel'])->name('export-grafik-excel');
            Route::get('/grafik/export-pdf', [SurveiPelangganController::class, 'exportGrafikPdf'])->name('export-grafik-pdf');
            Route::get('/pendukung/export-excel', [SurveiPelangganController::class, 'exportPendukungExcel'])->name('export-pendukung-excel');
            Route::get('/pendukung/export-pdf', [SurveiPelangganController::class, 'exportPendukungPdf'])->name('export-pendukung-pdf');
        });
    });

    // ---- Update Data Diri & Akun (Semua role yang login) ----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
