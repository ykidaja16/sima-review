<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Ketidaksesuaian;
use App\Models\KetidaksesuaianTindaklanjut;
use App\Models\KetidaksesuaianVerifikasi;
use App\Models\JenisKetidaksesuaian;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KetidaksesuaianController extends Controller
{
    /**
     * Cek apakah user bisa melihat semua tiket.
     * Kacab, Super Admin, dan Role Mutu bisa lihat semua.
     */
    private function canViewAll(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->hasRole(['super_admin', 'kacab', 'mutu']) || $user->isMutu();
    }

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        $query = Ketidaksesuaian::with(['pelapor', 'divisiTujuan', 'jenis', 'tindaklanjut', 'verifikasiManager', 'verifikasiKacab'])
            ->orderBy('created_at', 'desc');

        if (!$this->canViewAll()) {
            $karyawanId = $karyawan?->id;
            $divisiId   = $karyawan?->divisi_id;
            $roleSlug   = $user->role?->slug;

            $query->where(function ($q) use ($karyawanId, $divisiId, $roleSlug) {
                // Tiket yang dilaporkan sendiri
                if ($karyawanId) {
                    $q->orWhere('pelapor_id', $karyawanId);
                }
                // Tiket yang ditujukan ke divisi user
                if ($divisiId) {
                    $q->orWhere('divisi_tujuan_id', $divisiId);
                }
                // Manager & Supervisor: lihat semua tiket dari divisi yang sama (monitoring bawahan)
                if (in_array($roleSlug, ['manager', 'supervisor']) && $divisiId) {
                    $q->orWhere('divisi_pelapor_id', $divisiId);
                }
            });
        }

        $ketidaksesuaians = $query->paginate(15);
        return view('ketidaksesuaian.index', compact('ketidaksesuaians', 'karyawan'));
    }

    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $karyawan = Karyawan::with('divisi')->where('user_id', $user->id)->first();
        $jenisList = JenisKetidaksesuaian::active()->orderBy('nama')->get();
        $divisis   = Divisi::orderBy('nama')->get();

        return view('ketidaksesuaian.create', compact('karyawan', 'jenisList', 'divisis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_id'          => ['nullable', 'exists:jenis_ketidaksesuaians,id'],
            'jenis_lainnya'     => ['required_without:jenis_id', 'nullable', 'string', 'max:200'],
            'penjelasan_temuan' => ['required', 'string'],
            'kategori_temuan'   => ['required', 'in:ok,observasi,nc'],
            'divisi_tujuan_id'  => ['required', 'exists:divisis,id'],
        ]);

        /** @var User $user */
        $user     = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $validated['nomor_ftkp']        = Ketidaksesuaian::generateNomor();
        $validated['tanggal_laporan']   = now()->toDateString();
        $validated['pelapor_id']        = $karyawan->id;
        $validated['divisi_pelapor_id'] = $karyawan->divisi_id;
        $validated['status']            = 'open';

        $tiket = Ketidaksesuaian::create($validated);
        AuditLogService::log('CREATE_FTKP', 'Ketidaksesuaian', $tiket->id, null, ['nomor' => $tiket->nomor_ftkp]);

        return redirect()->route('ketidaksesuaian.show', $tiket)
            ->with('success', "FTKP {$tiket->nomor_ftkp} berhasil dibuat.");
    }

    public function show(Ketidaksesuaian $ketidaksesuaian): View
    {
        $ketidaksesuaian->load([
            'pelapor.divisi', 'divisiPelapor', 'jenis',
            'divisiTujuan', 'tindaklanjut.user',
            'verifikasiManager.verifikator', 'verifikasiKacab.verifikator',
        ]);

        /** @var User $user */
        $user     = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        // Tentukan apakah user bisa isi tindak lanjut
        $bisaTindaklanjut = $ketidaksesuaian->status === 'open'
            && !$ketidaksesuaian->tindaklanjut
            && $karyawan
            && $karyawan->divisi_id == $ketidaksesuaian->divisi_tujuan_id;

        // Tentukan apakah user bisa verifikasi manager
        $bisaVerifManager = $ketidaksesuaian->status === 'verifikasi_manager'
            && !$ketidaksesuaian->verifikasiManager
            && $user->hasRole(['manager'])
            && $karyawan
            && $karyawan->divisi_id == $ketidaksesuaian->divisi_tujuan_id;

        // Tentukan apakah user bisa verifikasi kacab
        $bisaVerifKacab = $ketidaksesuaian->status === 'verifikasi_kacab'
            && !$ketidaksesuaian->verifikasiKacab
            && $user->hasRole(['super_admin', 'kacab']);

        return view('ketidaksesuaian.show', compact(
            'ketidaksesuaian', 'bisaTindaklanjut', 'bisaVerifManager', 'bisaVerifKacab'
        ));
    }

    public function tindaklanjut(Request $request, Ketidaksesuaian $ketidaksesuaian): RedirectResponse
    {
        /** @var User $user */
        $user     = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        // Validasi akses
        if (!$karyawan || $karyawan->divisi_id != $ketidaksesuaian->divisi_tujuan_id) {
            return back()->with('error', 'Anda tidak berhak mengisi tindak lanjut ini.');
        }
        if ($ketidaksesuaian->status !== 'open') {
            return back()->with('error', 'Status tiket tidak valid untuk tindak lanjut.');
        }
        if ($ketidaksesuaian->tindaklanjut) {
            return back()->with('error', 'Tindak lanjut sudah diisi sebelumnya.');
        }

        $validated = $request->validate([
            'akar_masalah'              => ['required', 'string'],
            'tindakan_korektif'         => ['required', 'string'],
            'tindakan_pencegahan'       => ['required', 'string'],
            'perkiraan_tanggal_selesai' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $validated['ketidaksesuaian_id'] = $ketidaksesuaian->id;
        $validated['user_id']            = $user->id;

        DB::transaction(function () use ($ketidaksesuaian, $validated) {
            KetidaksesuaianTindaklanjut::create($validated);
            $ketidaksesuaian->update(['status' => 'verifikasi_manager']);
            AuditLogService::log('TINDAKLANJUT_FTKP', 'Ketidaksesuaian', $ketidaksesuaian->id, null, ['status' => 'verifikasi_manager']);
        });

        return redirect()->route('ketidaksesuaian.show', $ketidaksesuaian)
            ->with('success', 'Tindak lanjut berhasil disimpan. Menunggu verifikasi Manager.');
    }

    public function verifikasi(Request $request, Ketidaksesuaian $ketidaksesuaian): RedirectResponse
    {
        /** @var User $user */
        $user  = Auth::user();
        $level = null;

        if ($user->hasRole(['manager']) && $ketidaksesuaian->status === 'verifikasi_manager') {
            $level = 'manager';
        } elseif ($user->hasRole(['super_admin', 'kacab']) && $ketidaksesuaian->status === 'verifikasi_kacab') {
            $level = 'kacab';
        } else {
            return back()->with('error', 'Anda tidak berhak melakukan verifikasi pada tahap ini.');
        }

        $validated = $request->validate([
            'tindakan_efektif' => ['required', 'boolean'],
            'alasan'           => ['nullable', 'string'],
            'ftkp_baru_no'     => ['nullable', 'string', 'max:50'],
        ]);

        $validated['ketidaksesuaian_id'] = $ketidaksesuaian->id;
        $validated['verifikator_id']     = $user->id;
        $validated['level']              = $level;

        DB::transaction(function () use ($ketidaksesuaian, $validated, $level) {
            KetidaksesuaianVerifikasi::create($validated);

            if ((bool) $validated['tindakan_efektif']) {
                // Efektif ? lanjut ke tahap berikutnya atau closed
                $nextStatus = ($level === 'manager') ? 'verifikasi_kacab' : 'closed';
                $ketidaksesuaian->update(['status' => $nextStatus]);
            } else {
                // Tidak efektif ? kembali ke open untuk tindak lanjut ulang
                $ketidaksesuaian->tindaklanjut?->delete();
                // Hapus verifikasi sebelumnya jika kacab tolak (balik ke manager)
                if ($level === 'kacab') {
                    $ketidaksesuaian->verifikasiManager?->delete();
                }
                $ketidaksesuaian->update(['status' => 'open']);
            }

            AuditLogService::log('VERIFIKASI_FTKP', 'Ketidaksesuaian', $ketidaksesuaian->id, null, ['level' => $level, 'efektif' => $validated['tindakan_efektif']]);
        });

        $pesan = (bool) $validated['tindakan_efektif']
            ? 'Verifikasi disetujui.'
            : 'Verifikasi ditolak. Tindak lanjut perlu diulang.';

        return redirect()->route('ketidaksesuaian.show', $ketidaksesuaian)->with('success', $pesan);
    }
}
