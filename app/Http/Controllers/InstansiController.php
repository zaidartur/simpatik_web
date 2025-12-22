<?php

namespace App\Http\Controllers;

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
            'instansi'  => Instansi::all(),
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

        $instansi = new Instansi();
        $instansi->INSTANSI = $request->nama;
        $instansi->Akronim  = $request->akronim;
        $instansi->KODE     = $request->kode;
        $instansi->ALAMAT   = $request->alamat;
        $instansi->TELEPON  = $request->telepon;
        $instansi->EMAIL    = $request->email;
        
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

        $instansi = Instansi::find($request->uid);
        $instansi->INSTANSI = $request->nama;
        $instansi->Akronim  = $request->akronim;
        $instansi->KODE     = $request->kode;
        $instansi->ALAMAT   = $request->alamat;
        $instansi->TELEPON  = $request->telepon;
        $instansi->EMAIL    = $request->email;
        
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
        $instansi = Instansi::find($id);
        
        if ($instansi->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Data instansi berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data instansi gagal dihapus.']);
        }
    }
}
