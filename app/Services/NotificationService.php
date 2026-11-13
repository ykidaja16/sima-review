<?php

namespace App\Services;

use App\Models\Ketidaksesuaian;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Mengambil daftar notifikasi untuk pengguna yang sedang login
     *
     * @return array<int, array{
     *     id: string,
     *     type: string,
     *     title: string,
     *     message: string,
     *     time: Carbon|string|null,
     *     time_diff: string,
     *     url: string,
     *     read_url: string,
     *     icon: string,
     *     color: string,
     *     badge_label: string,
     *     is_read: bool
     * }>
     */
    public static function getNotificationsForUser(?User $user): array
    {
        if (!$user) {
            return [];
        }

        // Ambil daftar key notifikasi yang sudah dibaca oleh user ini
        $readKeys = DB::table('user_notification_reads')
            ->where('user_id', $user->id)
            ->pluck('notification_key')
            ->flip()
            ->all();

        $notifications = [];
        $karyawan = $user->karyawan;

        // ---------------------------------------------------------------------
        // 1. Notifikasi FTKP yang masuk ditujukan ke Divisinya yang perlu direspon
        // (Khusus FTKP: tidak hilang jika hanya diklik, harus sampai direspon/ditindaklanjuti)
        // ---------------------------------------------------------------------
        if ($karyawan && $karyawan->divisi_id) {
            $ftkpList = Ketidaksesuaian::with(['divisiPelapor'])
                ->where('divisi_tujuan_id', $karyawan->divisi_id)
                ->where('status', 'open')
                ->latest()
                ->take(5)
                ->get();

            foreach ($ftkpList as $ftkp) {
                $divisiAsal = $ftkp->divisiPelapor?->nama ?? 'Divisi Terkait';
                $notifId = 'ftkp_' . $ftkp->id;
                $targetUrl = route('ketidaksesuaian.show', $ftkp->id);

                $notifications[] = [
                    'id'          => $notifId,
                    'type'        => 'ftkp',
                    'title'       => 'FTKP Baru Perlu Respon',
                    'message'     => "Tiket #{$ftkp->nomor_tiket} dari {$divisiAsal} menunggu respon/tindak lanjut divisi Anda.",
                    'time'        => $ftkp->created_at,
                    'time_diff'   => $ftkp->created_at ? $ftkp->created_at->diffForHumans() : '',
                    'url'         => $targetUrl,
                    'read_url'    => route('notifications.read', ['key' => $notifId, 'redirect' => $targetUrl]),
                    'icon'        => 'bi-exclamation-triangle-fill',
                    'color'       => '#ef4444',
                    'badge_label' => 'FTKP',
                    'is_read'     => false, // FTKP selalu berstatus unread selama tiket belum direspon
                ];
            }
        }

        // ---------------------------------------------------------------------
        // 2 & 3. Untuk user yang memiliki flag "is_dinilai" (Wajib Dinilai)
        // ---------------------------------------------------------------------
        $isUserDinilai = $user->is_dinilai || ($karyawan && $karyawan->is_dinilai);

        if ($karyawan && $isUserDinilai) {
            // A. Notifikasi penilaian baru atas dirinya
            $penilaianList = Penilaian::with(['periode', 'evaluator'])
                ->where('karyawan_id', $karyawan->id)
                ->latest('tanggal_penilaian')
                ->take(3)
                ->get();

            foreach ($penilaianList as $penilaian) {
                $periodeNama = $penilaian->periode?->nama ?? 'Periode Ini';
                $evaluatorName = $penilaian->evaluator?->name ?? 'Atasan/Penilai';
                $tgl = $penilaian->tanggal_penilaian ?? $penilaian->created_at;
                $notifId = 'penilaian_' . $penilaian->id;
                $targetUrl = route('penilaian.show', $penilaian->id);

                $notifications[] = [
                    'id'          => $notifId,
                    'type'        => 'penilaian',
                    'title'       => 'Penilaian Kinerja Baru',
                    'message'     => "Penilaian periode {$periodeNama} telah diisi oleh {$evaluatorName} (Nilai: " . number_format($penilaian->nilai_akhir, 2) . ").",
                    'time'        => $tgl,
                    'time_diff'   => $tgl ? Carbon::parse($tgl)->diffForHumans() : '',
                    'url'         => $targetUrl,
                    'read_url'    => route('notifications.read', ['key' => $notifId, 'redirect' => $targetUrl]),
                    'icon'        => 'bi-clipboard2-check-fill',
                    'color'       => '#3b82f6',
                    'badge_label' => 'Penilaian',
                    'is_read'     => isset($readKeys[$notifId]),
                ];
            }

            // B. Peringatan H-7 sebelum penutupan periode aktif jika belum dinilai
            $periodeAktif = PeriodePenilaian::aktif()->first();
            if ($periodeAktif && $periodeAktif->tanggal_selesai) {
                $today = Carbon::today();
                $tglSelesai = Carbon::parse($periodeAktif->tanggal_selesai)->startOfDay();
                $diffDays = (int) $today->diffInDays($tglSelesai, false);

                // Jika berada dalam rentang H-7 sampai hari H penutupan
                if ($diffDays >= 0 && $diffDays <= 7) {
                    $sudahDinilai = Penilaian::where('karyawan_id', $karyawan->id)
                        ->where('periode_id', $periodeAktif->id)
                        ->exists();

                    if (!$sudahDinilai) {
                        $sisaText = $diffDays === 0 ? 'hari ini' : "{$diffDays} hari lagi";
                        $hText = $diffDays === 0 ? 'H-0' : "H-{$diffDays}";
                        $notifId = 'h7_periode_' . $periodeAktif->id;
                        $targetUrl = route('monitoring.individu', ['saya' => 1]);

                        $notifications[] = [
                            'id'          => $notifId,
                            'type'        => 'warning_h7',
                            'title'       => "Peringatan: Belum Dinilai ({$hText})",
                            'message'     => "Periode {$periodeAktif->nama} berakhir {$sisaText} (" . $tglSelesai->format('d M Y') . "). Anda belum dinilai.",
                            'time'        => now(),
                            'time_diff'   => 'Tenggat Waktu',
                            'url'         => $targetUrl,
                            'read_url'    => route('notifications.read', ['key' => $notifId, 'redirect' => $targetUrl]),
                            'icon'        => 'bi-alarm-fill',
                            'color'       => '#f59e0b',
                            'badge_label' => 'Peringatan H-7',
                            'is_read'     => isset($readKeys[$notifId]),
                        ];
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // 4. Notifikasi balasan/tanggapan penilaian dari user yang dinilai
        // ---------------------------------------------------------------------
        $tanggapanList = Penilaian::with(['karyawan', 'periode'])
            ->where('evaluator_id', $user->id)
            ->whereNotNull('tanggal_tanggapan')
            ->whereNotNull('tanggapan')
            ->where('tanggapan', '!=', '')
            ->latest('tanggal_tanggapan')
            ->take(5)
            ->get();

        foreach ($tanggapanList as $penilaianTanggapan) {
            $namaDinilai = $penilaianTanggapan->karyawan?->nama ?? 'Karyawan';
            $periodeNama = $penilaianTanggapan->periode?->nama ?? 'Periode Terkait';
            $tgl = $penilaianTanggapan->tanggal_tanggapan;
            $notifId = 'tanggapan_' . $penilaianTanggapan->id;
            $targetUrl = route('penilaian.show', $penilaianTanggapan->id);

            $notifications[] = [
                'id'          => $notifId,
                'type'        => 'tanggapan',
                'title'       => 'Tanggapan Penilaian Diterima',
                'message'     => "{$namaDinilai} telah memberikan respon tanggapan atas penilaian {$periodeNama}.",
                'time'        => $tgl,
                'time_diff'   => $tgl ? Carbon::parse($tgl)->diffForHumans() : '',
                'url'         => $targetUrl,
                'read_url'    => route('notifications.read', ['key' => $notifId, 'redirect' => $targetUrl]),
                'icon'        => 'bi-chat-left-text-fill',
                'color'       => '#10b981',
                'badge_label' => 'Respon Balik',
                'is_read'     => isset($readKeys[$notifId]),
            ];
        }

        // Urutkan: unread terlebih dahulu, lalu berdasarkan waktu terbaru
        usort($notifications, function ($a, $b) {
            if ($a['is_read'] !== $b['is_read']) {
                return $a['is_read'] ? 1 : -1;
            }
            $tA = $a['time'] ? Carbon::parse($a['time'])->timestamp : 0;
            $tB = $b['time'] ? Carbon::parse($b['time'])->timestamp : 0;
            return $tB <=> $tA;
        });

        return $notifications;
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     */
    public static function markAsRead(int $userId, string $key): void
    {
        // Khusus FTKP: tidak bisa ditandai terbaca lewat klik (harus sampai direspon/ditindaklanjuti)
        if (str_starts_with($key, 'ftkp_')) {
            return;
        }

        DB::table('user_notification_reads')->updateOrInsert(
            ['user_id' => $userId, 'notification_key' => $key],
            ['read_at' => now(), 'updated_at' => now(), 'created_at' => now()]
        );
    }

    /**
     * Tandai kumpulan notifikasi sebagai sudah dibaca (kecuali FTKP)
     *
     * @param array<int, string> $keys
     */
    public static function markAllAsRead(int $userId, array $keys): void
    {
        $now = now();
        $records = [];
        foreach ($keys as $key) {
            if (!str_starts_with($key, 'ftkp_')) {
                $records[] = [
                    'user_id'          => $userId,
                    'notification_key' => $key,
                    'read_at'          => $now,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        }

        if (!empty($records)) {
            DB::table('user_notification_reads')->upsert(
                $records,
                ['user_id', 'notification_key'],
                ['read_at', 'updated_at']
            );
        }
    }
}
