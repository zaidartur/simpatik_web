<?php

namespace App\Http\Controllers;

use App\Http\Requests\PimpinanStoreRequest;
use App\Http\Requests\PimpinanUpdateRequest;
use App\Models\ArsipSurat;
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

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pimpinan', ['only' => ['list_pejabat', 'save_pimpinan', 'update_pimpinan', 'set_default', 'delete_pimpinan']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
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
            return response()->json(['status' => 'success', 'message' => 'Data pimpinan berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data pimpinan gagal dihapus.']);
        }
    }

}
