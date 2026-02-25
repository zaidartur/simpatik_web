<?php

namespace App\Http\Controllers;

use App\Models\DataUnit;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InstansiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:instansi', ['only' => ['index', 'save_instansi', 'update_instansi', 'delete_instansi']]);
    }

    public function index()
    {
        $data = [
            'instansi'  => DataUnit::all(),
        ];
        return view('main.instansi', $data);
    }

    public function save_instansi(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'akronim'   => 'required|string|max:20',
            'kode'      => 'required|string|max:10',
            'alamat'    => 'nullable|string|max:255',
            'telepon'   => 'nullable|string|max:15',
            'email'     => 'nullable|email|max:100',
        ]);

        $instansi = new DataUnit();
        $instansi->nama_unit = $request->nama;
        $instansi->akronim  = $request->akronim;
        $instansi->kode     = $request->kode;
        $instansi->alamat   = $request->alamat;
        $instansi->website  = $request->telepon;
        $instansi->email    = $request->email;
        
        if ($instansi->save()) {
            return redirect()->back()->with('success', 'Data instansi berhasil disimpan.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data instansi.');
        }
    }

    public function update_instansi(Request $request)
    {
        $request->validate([
            'uid'       => 'required|string',
            'nama'      => 'required|string|max:100',
            'akronim'   => 'required|string|max:20',
            'kode'      => 'required|string|max:10',
            'alamat'    => 'nullable|string|max:255',
            'telepon'   => 'nullable|string|max:15',
            'email'     => 'nullable|email|max:100',
        ]);

        $instansi = DataUnit::find($request->uid);
        $instansi->nama_unit = $request->nama;
        $instansi->akronim  = $request->akronim;
        $instansi->kode     = $request->kode;
        $instansi->alamat   = $request->alamat;
        $instansi->website  = $request->telepon;
        $instansi->email    = $request->email;
        
        if ($instansi->save()) {
            return redirect()->back()->with('success', 'Data instansi berhasil diupdate.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data instansi.');
        }
    }

    public function delete_instansi(Request $request)
    {
        $request->validate([
            'uid'   => 'required|string',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'Data instansi tidak ditemukan.']);
        }
        $instansi = DataUnit::find($id);
        
        if ($instansi->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Data instansi berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data instansi gagal dihapus.']);
        }
    }
}
