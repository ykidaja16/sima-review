<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Master\DivisiController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Master\KategoriNilaiController;
use App\Http\Controllers\Master\ParameterSopController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PeriodePenilaianController;
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
    });

    // ---- User Management (Super Admin only) ----
    Route::middleware('role:super_admin')->prefix('user-management')->name('user-management.')->group(function () {
        Route::resource('/', UserController::class)->except(['show'])->parameters(['' => 'user']);
    });

    // ---- Audit Log (Super Admin only) ----
    Route::middleware('role:super_admin')
        ->get('/audit-log', [AuditLogController::class, 'index'])
        ->name('audit-log.index');

    // ---- Periode Penilaian (Super Admin + Manager) ----
    Route::middleware('role:super_admin,manager')->group(function () {
        Route::resource('periode', PeriodePenilaianController::class)->except(['show']);
        Route::patch('/periode/{periode}/status', [PeriodePenilaianController::class, 'updateStatus'])
            ->name('periode.update-status');
    });

    // ---- Penilaian Show (Bisa diakses semua role termasuk pelaksana untuk melihat penilaian sendiri) ----
    Route::get('/penilaian/{penilaian}', [PenilaianController::class, 'show'])->name('penilaian.show');

    // ---- Penilaian CRUD (Super Admin, Manager, Supervisor) ----
    Route::middleware('role:super_admin,manager,supervisor')->group(function () {
        Route::resource('penilaian', PenilaianController::class)->except(['show']);
    });

    // ---- Monitoring ----
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        // Individu: semua role (pelaksana dibatasi ke diri sendiri oleh middleware)
        Route::get('/individu', [MonitoringController::class, 'individu'])
            ->middleware('pelaksana.access')
            ->name('individu');

        // Tim, Divisi, Ranking: khusus manager ke atas
        Route::middleware('role:super_admin,manager,supervisor')->group(function () {
            Route::get('/tim', [MonitoringController::class, 'tim'])->name('tim');
            Route::get('/divisi', [MonitoringController::class, 'divisi'])->name('divisi');
            Route::get('/ranking', [MonitoringController::class, 'ranking'])->name('ranking');
        });
    });

    // ---- Laporan (Super Admin, Manager, Supervisor) ----
    Route::middleware('role:super_admin,manager,supervisor')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/print', [LaporanController::class, 'print'])->name('print');
    });
});
