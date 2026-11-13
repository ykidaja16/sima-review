<?php

namespace App\Services;

use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * Definisi seluruh menu aplikasi yang dikelola hak aksesnya
     */
    public static function getMenuCatalog(): array
    {
        return [
            [
                'group' => 'Dashboard',
                'menus' => [
                    ['key' => 'dashboard', 'title' => 'Dashboard', 'desc' => 'Ringkasan performa & statistik'],
                ],
            ],
            [
                'group' => 'Penilaian Kinerja',
                'menus' => [
                    ['key' => 'penilaian_input',  'title' => 'Input Penilaian',        'desc' => 'Form penilaian karyawan'],
                    ['key' => 'penilaian_data',   'title' => 'Data Penilaian',         'desc' => 'Riwayat & daftar penilaian'],
                    ['key' => 'periode_kelola',   'title' => 'Buat / Kelola Periode',  'desc' => 'Manajemen periode penilaian aktif'],
                ],
            ],
            [
                'group' => 'Monitoring',
                'menus' => [
                    ['key' => 'monitoring_individu', 'title' => 'Track Record Individu', 'desc' => 'Grafik & tren nilai individu karyawan'],
                    ['key' => 'monitoring_tim',      'title' => 'Monitoring Tim',        'desc' => 'Status penilaian bawahan / tim'],
                    ['key' => 'monitoring_divisi',   'title' => 'Perbandingan Divisi',   'desc' => 'Statistik & perbandingan antar divisi'],
                    ['key' => 'monitoring_ranking',  'title' => 'Ranking Karyawan',      'desc' => 'Peringkat nilai karyawan tertinggi'],
                ],
            ],
            [
                'group' => 'Laporan',
                'menus' => [
                    ['key' => 'laporan', 'title' => 'Laporan & Export', 'desc' => 'Rekapitulasi nilai dan export laporan'],
                ],
            ],
            [
                'group' => 'Review (FTKP)',
                'menus' => [
                    ['key' => 'ketidaksesuaian', 'title' => 'FTKP / Ketidaksesuaian', 'desc' => 'Pelaporan & verifikasi ketidaksesuaian'],
                ],
            ],
            [
                'group' => 'Survei Pelanggan',
                'menus' => [
                    ['key' => 'survei_data',      'title' => 'Data Survei',     'desc' => 'Data mentah respon survei'],
                    ['key' => 'survei_analisa',   'title' => 'Analisa Data',    'desc' => 'Rekapitulasi 9 kriteria kualitas layanan'],
                    ['key' => 'survei_grafik',    'title' => 'Internal Grafik', 'desc' => 'Diagram kartesius kuadran IPA'],
                    ['key' => 'survei_pendukung', 'title' => 'Data Pendukung',  'desc' => 'Profil demografi responden survei'],
                ],
            ],
            [
                'group' => 'Master Data',
                'menus' => [
                    ['key' => 'master_cabang',    'title' => 'Master Cabang',        'desc' => 'Data kantor cabang'],
                    ['key' => 'master_divisi',    'title' => 'Master Divisi',        'desc' => 'Data divisi perusahaan'],
                    ['key' => 'master_jabatan',   'title' => 'Master Jabatan',       'desc' => 'Data jabatan & level'],
                    ['key' => 'master_karyawan',  'title' => 'Master Karyawan',      'desc' => 'Data karyawan & atasan langsung'],
                    ['key' => 'master_parameter', 'title' => 'Master Parameter SOP', 'desc' => 'Parameter penilaian SOP'],
                ],
            ],
            [
                'group' => 'User Management',
                'menus' => [
                    ['key' => 'user_management', 'title' => 'Daftar Pengguna',       'desc' => 'Akun login & status aktif user'],
                    ['key' => 'hak_akses',       'title' => 'Hak Akses Management',  'desc' => 'Pengaturan hak akses menu per role'],
                ],
            ],
        ];
    }

    /**
     * Flat array seluruh menu key
     */
    public static function getAllMenuKeys(): array
    {
        $keys = [];
        foreach (static::getMenuCatalog() as $group) {
            foreach ($group['menus'] as $m) {
                $keys[] = $m['key'];
            }
        }
        return $keys;
    }

    /**
     * Default permissions awal per role jika tabel permissions masih kosong
     */
    public static function getDefaultPermissionsByRole(string $roleSlug): array
    {
        return match ($roleSlug) {
            'super_admin' => [
                'dashboard', 'penilaian_input', 'penilaian_data', 'periode_kelola',
                // Sesuai Request #2: superadmin TIDAK perlu monitoring_individu, monitoring_tim, monitoring_divisi, monitoring_ranking
                'laporan', 'ketidaksesuaian',
                'survei_data', 'survei_analisa', 'survei_grafik', 'survei_pendukung',
                'master_cabang', 'master_divisi', 'master_jabatan', 'master_karyawan', 'master_parameter',
                'user_management', 'hak_akses',
            ],
            'direktur' => [
                // Sesuai Request #3: mirip kacab tapi TANPA periode_kelola, dan bisa akses semua cabang
                'dashboard', 'penilaian_input', 'penilaian_data',
                'monitoring_individu', 'monitoring_tim', 'monitoring_divisi', 'monitoring_ranking',
                'laporan', 'ketidaksesuaian',
                'survei_data', 'survei_analisa', 'survei_grafik', 'survei_pendukung',
            ],
            'kacab' => [
                'dashboard', 'penilaian_input', 'penilaian_data', 'periode_kelola',
                'monitoring_individu', 'monitoring_tim', 'monitoring_divisi', 'monitoring_ranking',
                'laporan', 'ketidaksesuaian',
                'survei_data', 'survei_analisa', 'survei_grafik', 'survei_pendukung',
            ],
            'manager' => [
                'dashboard', 'penilaian_input', 'penilaian_data',
                'monitoring_individu', 'monitoring_tim', 'monitoring_divisi',
                'laporan', 'ketidaksesuaian', 'survei_data',
            ],
            'supervisor' => [
                'dashboard', 'penilaian_input', 'penilaian_data',
                'monitoring_individu', 'monitoring_tim',
                'laporan', 'ketidaksesuaian', 'survei_data',
            ],
            'pelaksana' => [
                'dashboard', 'penilaian_data',
                'monitoring_individu',
                'ketidaksesuaian', 'survei_data',
            ],
            'mutu' => [
                'dashboard',
                'monitoring_divisi', 'monitoring_ranking',
                'laporan', 'ketidaksesuaian',
                'survei_data', 'survei_analisa', 'survei_grafik', 'survei_pendukung',
            ],
            default => ['dashboard'],
        };
    }

    /**
     * Inisialisasi default permissions ke DB jika role tertentu belum memiliki entri sama sekali
     */
    public static function seedDefaultsIfEmpty(): void
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $hasEntries = RoleMenuPermission::where('role_id', $role->id)->exists();
            if (!$hasEntries) {
                $defaults = static::getDefaultPermissionsByRole($role->slug);
                $rows = [];
                $now = now();
                foreach ($defaults as $menuKey) {
                    $rows[] = [
                        'role_id'    => $role->id,
                        'menu_key'   => $menuKey,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if (!empty($rows)) {
                    RoleMenuPermission::insert($rows);
                }
            }
        }
        Cache::forget('role_menu_permissions_matrix');
    }

    /**
     * Dapatkan permissions matriks [role_id => [menu_key => true]]
     */
    public static function getPermissionsMatrix(): array
    {
        return Cache::remember('role_menu_permissions_matrix', 3600, function () {
            self::seedDefaultsIfEmpty();

            $permissions = RoleMenuPermission::all();
            $matrix = [];
            foreach ($permissions as $p) {
                $matrix[$p->role_id][$p->menu_key] = true;
            }
            return $matrix;
        });
    }

    /**
     * Cek apakah role_id memiliki izin akses ke menu tertentu
     */
    public static function roleHasAccess(int $roleId, string $menuKey): bool
    {
        $matrix = static::getPermissionsMatrix();
        return isset($matrix[$roleId][$menuKey]);
    }
}

