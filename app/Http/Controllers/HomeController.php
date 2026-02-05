<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\Inbox;
use App\Models\Klasifikasi;
use App\Models\LevelUser;
use App\Models\Perkembangan;
use App\Models\Pimpinan;
use App\Models\SifatSurat;
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
        $this->middleware('permission:aplikasi', ['only' => ['list_pejabat', 'save_pimpinan', 'update_pimpinan', 'set_default', 'delete_pimpinan']]);
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

        $query = ArsipSurat::selectRaw('NO as id, drkpd as title, TGLENTRY as start, TGLENTRY as end, JENISSURAT as jenis, ISI as isi_surat, NAMAKOTA as kota, NOSURAT as surat, TGLSURAT as tgl_surat, PERIHAL as perihal')
                ->whereBetween('TGLENTRY', [$start, $end]);
        
        if (Auth::user()->hasRole(['setda'])) {
            $query->whereIn('Posisi', ['Sekretaris Daerah', 'Bupati', 'Wakil Bupati']);
        }
        if (Auth::user()->hasRole(['wabup'])) {
            $query->whereIn('Posisi', ['Bupati', 'Wakil Bupati']);
        }
        if (Auth::user()->hasRole(['bupati'])) {
            $query->whereIn('Posisi', ['Bupati']);
        }
        
        $lists = $query->get();
        
        // $lists = ArsipSurat::whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->get();
        
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
        $folder = public_path('datas/uploads/duplikat');
        if (!file_exists($folder . '/' . $name)) return abort(404);

        return response()->file($folder. '/' . $name, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function download_duplikat($name)
    {
        $folder = public_path('datas/uploads/duplikat');
        if (!file_exists($folder . '/' . $name)) return abort(404);

        return response()->download($folder .'/'. $name, $name, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function list_pejabat()
    {
        $data = [
            'lists'     => Pimpinan::orderBy('level')->get(),
            'instansi'  => DB::table('instansi')->get(),
        ];

        return view('main.pimpinan', $data);
    }

    public function save_pimpinan(Request $request)
    {
        $request->validate([
            'jabatan'   => 'required|string|max:100',
            'nama'      => 'required|string|max:100',
            'nip'       => 'nullable|string|max:255',
            'pangkat'   => 'nullable|string|max:100',
            'role'      => 'required|string|max:50',
            'is_default'=> 'required|string|in:yes,no',
        ]);

        if ($request->is_default == 'yes') {
            Pimpinan::where('level', $request->level)->update(['is_default' => 0]);
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

    public function update_pimpinan(Request $request)
    {
        $request->validate([
            'uid'       => 'required|numeric',
            'jabatan'   => 'required|string|max:100',
            'nama'      => 'required|string|max:100',
            'nip'       => 'nullable|string|max:255',
            'pangkat'   => 'nullable|string|max:100',
            'role'      => 'required|string|max:50',
            'is_default'=> 'required|string|in:yes,no',
        ]);

        if ($request->is_default == 'yes') {
            Pimpinan::where('level', $request->level)->update(['is_default' => 0]);
        }

        $pimpinan = Pimpinan::where('id', $request->uid)->first();
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


    // ============================================= MIGRATION ============================================= //

    public function migrate_table()
    {
        ini_set('max_execution_time', 3600);
        $all = DB::table('aktif')->where('JENISSURAT', 'Masuk')->orderBy('NO')->chunk(100, function ($datas, $int) {
            $res = 0;
            foreach ($datas as $key => $value) {
                $klas  = Klasifikasi::where('klas3', $value->KLAS3)->first();
                $sifat = SifatSurat::where('nama_sifat', $value->SIFAT_SURAT)->first();
                $tempat= TempatBerkas::where('nama', $value->TMPTBERKAS)->first();
                $ip    = Perkembangan::where('nama', $value->TK_PERKEMBANGAN)->first();
                $level = LevelUser::where('nama', $value->NAMAUP)->first();

                if ($level && $level->id) {
                    $user  = User::where('level', $level->id)->first();
                    $save = new Inbox();
                    $save->uuid         = Str::uuid7();
                    $save->no_agenda    = intval($value->NOAGENDA);
                    $save->nama_berkas  = $value->NAMABERKAS;
                    $save->no_surat     = $value->NOSURAT;
                    $save->dari         = $value->drkpd;
                    $save->wilayah      = $value->WILAYAH;
                    $save->perihal      = $value->PERIHAL;
                    $save->isi_surat    = $value->ISI;
                    $save->tgl_surat    = date_format(date_create($value->TGLSURAT), 'Y-m-d');
                    $save->tgl_diterima = Carbon::now();
                    $save->year         = intval($value->TAHUN);
                    $save->id_media     = 1;
                    if ($klas && $klas->id) {
                        $save->id_klasifikasi   = $klas->id;
                    }
                    if ($sifat && $sifat->id) {
                        $save->sifat_surat  = $sifat->id;
                    }
                    if ($tempat && $tempat->id) {
                        $save->tempat_berkas= $tempat->id;
                    }
                    if ($ip && $ip->id) {
                        $save->id_perkembangan  = $ip->id;
                    }
                    $save->posisi_surat = $user->uuid;
                    $save->tindakan     = "non balas";
                    $save->tgl_balas    = null;
                    $save->level_surat  = 8;
                    $save->status_surat = "selesai";
                    $save->is_primary_agenda = true;
                    $save->created_by   = 1;

                    if ($save->save()) {
                        $res++;
                    }
                }
            }
        });

        // foreach ($all as $key => $value) {
        //     $klas  = Klasifikasi::where('klas3', $value->KLAS3)->first();
        //     $sifat = SifatSurat::where('nama_sifat', $value->SIFAT_SURAT)->first();
        //     $tempat= TempatBerkas::where('nama', $value->TMPTBERKAS)->first();
        //     $ip    = Perkembangan::where('nama', $value->TK_PERKEMBANGAN)->first();
        //     $level = LevelUser::where('nama', $value->NAMAUP)->first();
        //     // if (!empty($value->RAKTIF) && !empty($value->RINAKTIF) && !empty($value->KETJRA)) {
        //         $save = new Inbox();
        //         $save->uuid         = Str::uuid7();
        //         $save->no_agenda    = intval($value->NOAGENDA);
        //         $save->nama_berkas  = $value->NAMABERKAS;
        //         $save->no_surat     = $value->NOSURAT;
        //         $save->dari         = $value->drkpd;
        //         $save->wilayah      = $value->WILAYAH;
        //         $save->perihal      = $value->PERIHAL;
        //         $save->isi_surat    = $value->ISI;
        //         $save->tgl_surat    = date_format(date_create($value->TGLSURAT), 'Y-m-d');
        //         $save->tgl_diterima = date_format(date_create($value->TGLTERIMA), 'Y-m-d');
        //         $save->year         = intval($value->TAHUN);
        //         $save->id_media     = 1;
        //         $save->id_klasifikasi   = $klas->id;
        //         $save->sifat_surat  = $sifat->id;
        //         $save->tempat_berkas= $tempat->id;
        //         $save->id_perkembangan  = $ip->id;
        //         $save->posisi_surat = $value->Posisi;
        //         $save->tindakan     = "non balas";
        //         $save->tgl_balas    = null;
        //         $save->level_surat  = intval($level->id) ?? 1;
        //         $save->status_surat = "selesai";
        //         $save->is_primary_agenda = true;
        //         $save->created_by   = 1;

        //         if ($save->save()) {
        //             $res++;
        //         }
        //     // }
        // }

        // return ['total' => count($all), 'success' => $res];
    }
}
