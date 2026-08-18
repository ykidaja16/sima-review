<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPelaksanaAccess
{
    /**
     * Pastikan pelaksana hanya bisa mengakses data miliknya sendiri.
     * Cek parameter karyawan_id di route atau query string.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isPelaksana()) {
            $karyawan = $user->karyawan;

            if (!$karyawan) {
                abort(403, 'Data karyawan tidak ditemukan untuk akun Anda.');
            }

            // Cek route parameter karyawan atau karyawan_id
            $karyawanId = $request->route('karyawan') ?? $request->route('karyawan_id') ?? $request->query('karyawan_id');

            if ($karyawanId && (int) $karyawanId !== $karyawan->id) {
                abort(403, 'Anda tidak diizinkan mengakses data karyawan lain.');
            }
        }

        return $next($request);
    }
}
