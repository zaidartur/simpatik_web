<?php

namespace App\Http\Controllers;

use App\Http\Requests\PimpinanStoreRequest;
use App\Http\Requests\PimpinanUpdateRequest;
use App\Models\ArsipSurat;
use App\Models\Disposisi;
use App\Models\Inbox;
use App\Models\Instansi;
use App\Models\Klasifikasi;
use App\Models\LevelUser;
use App\Models\Outbox;
use App\Models\Perkembangan;
use App\Models\Pimpinan;
use App\Models\SifatSurat;
use App\Models\Spd;
use App\Models\TempatBerkas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Services\ActivityLogService;
use App\Services\ReferenceCacheService;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    protected ReferenceCacheService $cacheService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ReferenceCacheService $cacheService)
    {
        $this->middleware('auth');
        $this->middleware('permission:pimpinan', ['only' => ['list_pejabat', 'save_pimpinan', 'update_pimpinan', 'set_default', 'delete_pimpinan']]);
        $this->cacheService = $cacheService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['administrator', 'admin']);
        $level = LevelUser::where('id', $user->level)->first();
        $akses = $level ? $level->akses : [$user->level];

        $currentYear = intval(date('Y'));
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;

        // Base query scoped by role & level access
        $inboxBase = Inbox::whereNull('on_delete');
        $outboxBase = Outbox::whereNull('on_delete');

        if (!$isAdmin) {
            $inboxBase->where(function ($q) use ($akses, $user) {
                $q->whereIn('posisi_level', $akses)
                  ->orWhere('posisi_surat', $user->uuid)
                  ->orWhere('created_by', $user->uuid);
            });

            $outboxBase->where(function ($q) use ($akses, $user) {
                $q->whereIn('level_surat', $akses)
                  ->orWhere('created_by', $user->uuid);
            });
        }

        // Metrics
        $masukHariIni = (clone $inboxBase)->whereDate('created_at', $today)->count();
        $masukBulanIni = (clone $inboxBase)->whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->count();

        $keluarHariIni = (clone $outboxBase)->whereDate('created_at', $today)->count();
        $keluarBulanIni = (clone $outboxBase)->whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)->count();

        $suratSelesai = (clone $inboxBase)->where('status_surat', 'selesai')->count();

        // Disposisi menunggu tindakan
        $disposisiMenungguUser = Disposisi::where('penerima_uuid', $user->uuid)
            ->where('is_completed', false)
            ->whereNull('on_delete')
            ->count();

        $totalDisposisiGlobal = $isAdmin 
            ? Disposisi::where('is_completed', false)->whereNull('on_delete')->count()
            : $disposisiMenungguUser;

        // Pending disposisi items for current user
        $pendingDisposisis = Disposisi::with(['pengirim.leveluser', 'inbox'])
            ->where('penerima_uuid', $user->uuid)
            ->where('is_completed', false)
            ->whereNull('on_delete')
            ->latest()
            ->limit(5)
            ->get();

        // Fallback for admin: if no direct personal pending disposisi, show latest ongoing inboxes
        $recentInboxes = ($isAdmin && $pendingDisposisis->isEmpty())
            ? (clone $inboxBase)->where('status_surat', 'diproses')->with(['posisi.leveluser'])->latest()->limit(5)->get()
            : collect();

        // Monthly Trend for Current Year
        $inboxMonthly = (clone $inboxBase)
            ->selectRaw('EXTRACT(MONTH FROM created_at) as m, count(*) as c')
            ->whereYear('created_at', $currentYear)
            ->groupBy('m')
            ->pluck('c', 'm')
            ->toArray();

        $outboxMonthly = (clone $outboxBase)
            ->selectRaw('EXTRACT(MONTH FROM created_at) as m, count(*) as c')
            ->whereYear('created_at', $currentYear)
            ->groupBy('m')
            ->pluck('c', 'm')
            ->toArray();

        $trendMasuk = [];
        $trendKeluar = [];
        for ($m = 1; $m <= 12; $m++) {
            $trendMasuk[] = intval($inboxMonthly[$m] ?? 0);
            $trendKeluar[] = intval($outboxMonthly[$m] ?? 0);
        }

        return view('home', compact(
            'isAdmin',
            'masukHariIni',
            'masukBulanIni',
            'keluarHariIni',
            'keluarBulanIni',
            'suratSelesai',
            'disposisiMenungguUser',
            'totalDisposisiGlobal',
            'pendingDisposisis',
            'recentInboxes',
            'trendMasuk',
            'trendKeluar',
            'currentYear'
        ));
    }

    public function list_surat()
    {
        $request = Request();
        if (!isset($request->start) || !isset($request->end)) return null;
        if (empty($request->start) || empty($request->end)) return null;
        $start = Carbon::parse($request->start)->format('Y-m-d');
        $end = Carbon::parse($request->end)->format('Y-m-d');
        $level  = LevelUser::where('id', Auth::user()->level)->first();

        $query = Inbox::selectRaw('uuid as id, dari as title, created_at as start, created_at as end, isi_surat, wilayah as kota, no_surat as surat, tgl_surat, perihal')
                ->whereBetween('created_at', [$start, $end])
                ->whereNull('on_delete')
                ->whereIn('level_surat', $level->akses)
                ->orWhereIn('posisi_level', $level->akses);
        $inbox = $query->get();
        $inbox->transform(function($inb) {
            $inb->jenis = 'Masuk';
            return $inb;
        });

        $queri = Outbox::selectRaw('uuid as id, kepada as title, created_at as start, created_at as end, isi_surat, wilayah as kota, no_surat as surat, tgl_surat, perihal')
                ->whereBetween('created_at', [$start, $end])
                ->whereNull('on_delete')
                ->whereIn('level_surat', $level->akses);
        $outbox = $queri->get();
        $outbox->transform(function($out) {
            $out->jenis = 'Keluar';
            return $out;
        });

        $lists = [...$inbox, ...$outbox];
        foreach ($lists as $key => $value) {
            // $value->extendedProps = ['calendar' => ($value->jenis == 'Masuk' ? 'Work' : 'Important')];
            $value->surat = empty($value->surat) ? '-' : $value->surat;
            $value->extendedProps = ['calendar' => $value->jenis];
            $value->tgl_surat = empty($value->tgl_surat) ? '' : str_replace('/', '-', $value->tgl_surat);
        }

        return response()->json($lists);
    }

    public function view_duplikat($name)
    {
        $safeName = basename($name);
        if ($safeName !== $name || !preg_match('/^[a-zA-Z0-9._-]+$/', $safeName)) {
            return abort(404);
        }

        $folder = public_path('datas/uploads/duplikat');
        if (!is_dir($folder)) {
            return abort(404);
        }

        $fullPath = realpath($folder . DIRECTORY_SEPARATOR . $safeName);
        $realFolder = realpath($folder);
        if (!$fullPath || !$realFolder || !str_starts_with($fullPath, $realFolder)) {
            return abort(404);
        }

        if (!file_exists($fullPath)) {
            return abort(404);
        }

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function download_duplikat($name)
    {
        $safeName = basename($name);
        if ($safeName !== $name || !preg_match('/^[a-zA-Z0-9._-]+$/', $safeName)) {
            return abort(404);
        }

        $folder = public_path('datas/uploads/duplikat');
        if (!is_dir($folder)) {
            return abort(404);
        }

        $fullPath = realpath($folder . DIRECTORY_SEPARATOR . $safeName);
        $realFolder = realpath($folder);
        if (!$fullPath || !$realFolder || !str_starts_with($fullPath, $realFolder)) {
            return abort(404);
        }

        if (!file_exists($fullPath)) {
            return abort(404);
        }

        return response()->download($fullPath, $safeName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function list_pejabat()
    {
        $user = Auth::user();
        $lead = Pimpinan::select('*');
        $query = LevelUser::select('*');
        if ($user->hasAnyRole(['admin'])) {
            $lead->whereIn('level', $user->leveluser->akses);
            $query->whereIn('id', $user->leveluser->akses);
        } elseif (!$user->hasAnyRole(['administrator', 'admin'])) {
            $lead->where('level', $user->level);
            $query->where('id', $user->level);
        }

        $pimpinan = $lead->get();
        $level = $query->get();
        $data = [
            'lists'     => $pimpinan,
            // 'instansi'  => Instansi::orderBy('kode')->get(),
            'instansi'  => $level,
        ];

        return view('main.pimpinan', $data);
    }

    public function save_pimpinan(PimpinanStoreRequest $request)
    {
        $targetLevel = $request->role;
        if ($request->is_default == 'yes') {
            Pimpinan::where('level', $targetLevel)->update(['is_default' => 0]);
        }

        $pimpinan = new Pimpinan();
        $pimpinan->nama = $request->nama;
        $pimpinan->jabatan = $request->jabatan;
        $pimpinan->nip = $request->nip;
        $pimpinan->pangkat_golongan = $request->pangkat;
        $pimpinan->level = $request->role;
        $pimpinan->is_default = $request->is_default == 'yes' ? 1 : 0;
        $save = $pimpinan->save();

        if ($save) {
            $this->cacheService->forgetPimpinan();
            return response()->json(['status' => 'success', 'message' => 'Data pimpinan berhasil disimpan.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data pimpinan gagal disimpan.']);
        }
    }

    public function update_pimpinan(PimpinanUpdateRequest $request)
    {
        $pimpinan = Pimpinan::where('id', $request->uid)->first();
        if (!$pimpinan) {
            return response()->json(['status' => 'failed', 'message' => 'Data pimpinan tidak ditemukan.']);
        }

        $targetLevel = $request->role;
        if ($request->is_default == 'yes') {
            Pimpinan::where('level', $targetLevel)->update(['is_default' => 0]);
        }

        $pimpinan->nama = $request->nama;
        $pimpinan->jabatan = $request->jabatan;
        $pimpinan->nip = $request->nip;
        $pimpinan->pangkat_golongan = $request->pangkat;
        $pimpinan->level = $request->role;
        $pimpinan->is_default = $request->is_default == 'yes' ? 1 : 0;
        $save = $pimpinan->save();

        if ($save) {
            $this->cacheService->forgetPimpinan();
            return response()->json(['status' => 'success', 'message' => 'Data pimpinan berhasil diubah.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data pimpinan gagal diubah.']);
        }
    }

    public function set_default(Request $request)
    {
        $request->validate([
            'uid'       => 'required|string|max:100',
            'role'      => 'required|string|max:50',
        ]);

        $id = base64_decode($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'Data pimpinan tidak ditemukan.']);
        Pimpinan::where('level', $request->role)->update(['is_default' => 0]);
        $pimpinan = Pimpinan::where('id', $id)->first();
        $pimpinan->is_default = 1;
        $save = $pimpinan->save();

        if ($save) {
            $this->cacheService->forgetPimpinan();
            return response()->json(['status' => 'success', 'message' => 'Data pimpinan berhasil dijadikan default.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data pimpinan gagal dijadikan default.']);
        }
    }

    public function delete_pimpinan(Request $request)
    {
        $request->validate([
            'uid'   => 'required|string'
        ]);

        $id = base64_decode($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'Data pimpinan tidak ditemukan.']);
        $drop = Pimpinan::where('id', $id)->delete();
        if ($drop) {
            $this->cacheService->forgetPimpinan();
            return response()->json(['status' => 'success', 'message' => 'Data pimpinan berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data pimpinan gagal dihapus.']);
        }
    }

    /**
     * Change authenticated user password with session revocation on other devices.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'password.min'              => 'Password baru minimal harus 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Password saat ini tidak sesuai.',
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Security hardening: Revoke sessions on all other devices
        Auth::logoutOtherDevices($request->password);

        ActivityLogService::log(
            'change_password',
            'auth',
            'Pengguna berhasil mengubah password akun dan mencabut sesi di perangkat lain.',
            $user
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diubah. Sesi di perangkat lain telah dinonaktifkan.',
        ]);
    }
}
