<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Catat aktivitas ke audit log.
     *
     * @param  string      $aktivitas   Nama aktivitas (LOGIN, CREATE_PENILAIAN, dsb.)
     * @param  string|null $model       Nama model yang terdampak
     * @param  int|null    $modelId     ID record yang terdampak
     * @param  array|null  $dataLama    Data sebelum perubahan
     * @param  array|null  $dataBaru    Data sesudah perubahan
     */
    public static function log(
        string $aktivitas,
        ?string $model = null,
        ?int $modelId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'aktivitas'  => strtoupper($aktivitas),
            'model'      => $model,
            'model_id'   => $modelId,
            'data_lama'  => $dataLama,
            'data_baru'  => $dataBaru,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
