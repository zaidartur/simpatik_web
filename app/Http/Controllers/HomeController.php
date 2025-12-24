<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\Pimpinan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $lists = ArsipSurat::selectRaw('NO as id, drkpd as title, created_at as start, created_at as end, JENISSURAT as jenis, ISI as isi_surat, NAMAKOTA as kota, NOSURAT as surat, TGLSURAT as tgl_surat, PERIHAL as perihal')
                ->whereBetween('created_at', [$start, $end])->get();
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
}
