<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SppdController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:spd', ['only' => ['index', 'serverside', 'create', 'list', 'save', 'store', 'edit', 'update', 'destroy']]);
    }

    public function index()
    {
        $data = [
            // 'lists' => Sppd::orderBy('id', 'desc')->get(),
        ];

        return view('main.sppd.index');
    }

    public function serverside()
    {
        $request = Request();
        $start = $request->start;
        $length = $request->length;
        $user = $request->user();

        $query = Sppd::query();
        // $query->where('JENISSURAT', 'Keluar');
        $totalData = $query->count();

        // search query
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nosppd', 'like', "%$search%")
                    ->orWhere('nama', 'like', "%$search%")
                    ->orWhere('jabatan', 'like', "%$search%")
                    ->orWhere('tujuan', 'like', "%$search%")
                    ->orWhere('tglsurat', 'like', "%$search%")
                    ->orWhere('tglberangkat', 'like', "%$search%");
            });
        }

        $totalFiltered = $query->count();
        $query->orderBy('id', 'desc');
        $query->skip($start)->take($length);
        
        $sppd = $query->get();

        // manipulate fields data
        $data = [];
        foreach ($sppd as $r => $sp) {
            $data[] = [
                'nomor'     => $sp->nosppd,
                'nama'      => $sp->nama,
                'jabatan'   => $sp->jabatan ?? '',
                'tujuan'    => $sp->tujuan ?? '',
                'kendaraan'  => $sp->kendaraan ?? '',
                'tgl_surat' => Carbon::parse($sp->tglsurat)->isoFormat('DD-MMM-YYYY'),
                'tgl_berangkat' => Carbon::parse($sp->tglberangkat)->isoFormat('DD-MMM-YYYY'),
                'created_at'    => $sp->created_at,
                'uid'       => Crypt::encryptString($sp->id),
                'option'    => '<div class="btn-group-vertical" role="group" aria-label="Second group">
                                    <a href="javascript:void(0)" onclick="_edit(`'. base64_encode(json_encode($sp)) .'`)" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit SPPD">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                    <button type="button" class="btn btn-danger bs-tooltip" onclick="_delete(`'. Crypt::encryptString($sp->id) .'`, `'. base64_encode(json_encode($sp)) .'`, `'. Crypt::encryptString($sp->id) .'`)" title="Hapus SPPD">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </div>',
            ];
        }

        return response()->json([
            // 'draw' => intval($request->draw) ?? 0,
            'draw' => $request->has('draw') ? intval($request->draw) : 0,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('main.sppd.new');
    }

    public function list()
    {
        $lists = Sppd::orderBy('id', 'desc')->limit(50)->get();

        return response()->json(['status' => 'success', 'data' => $lists]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nosppd'    => 'required|string|max:100',
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'tujuan'    => 'required|string|max:255',
            'kendaraan' => 'required|string|max:255',
            'tgl_surat' => 'required|date',
            'tgl_berangkat' => 'required|date',
        ]);

        $sppd = new Sppd();
        $sppd->nosppd       = $request->nosppd;
        $sppd->nama         = strtoupper($request->nama);
        $sppd->jabatan      = strtoupper($request->jabatan);
        $sppd->tujuan       = strtoupper($request->tujuan);
        $sppd->kendaraan    = strtoupper($request->kendaraan);
        $sppd->tglsurat    = $request->tgl_surat;
        $sppd->tglberangkat = $request->tgl_berangkat;
        $sppd->created_at   = Carbon::now();

        if ($sppd->save()) {
            return response()->json(['status' => 'success', 'message' => 'SPPD berhasil disimpan.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'SPPD gagal disimpan.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nosppd'    => 'required|string|max:100',
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'tujuan'    => 'required|string|max:255',
            'kendaraan' => 'required|string|max:255',
            'tgl_surat' => 'required|date',
            'tgl_berangkat' => 'required|date',
        ]);

        $sppd = new Sppd();
        $sppd->nosppd       = $request->nosppd;
        $sppd->nama         = strtoupper($request->nama);
        $sppd->jabatan      = strtoupper($request->jabatan);
        $sppd->tujuan       = strtoupper($request->tujuan);
        $sppd->kendaraan    = strtoupper($request->kendaraan);
        $sppd->tglsurat    = $request->tgl_surat;
        $sppd->tglberangkat = $request->tgl_berangkat;
        $sppd->created_at   = Carbon::now();

        if ($sppd->save()) {
            return redirect()->route('sppd')->with('success', 'SPPD berhasil disimpan.');
        } else {
            return redirect()->route('sppd')->with('error', 'SPPD gagal disimpan.');
        }
    }

    public function edit($uid)
    {
        $id = Crypt::decryptString($uid);
        if (!$id) return abort(404);
        return abort(404);
    }

    public function update(Request $request)
    {
        $request->validate([
            'uid'       => 'required',
            'nosppd'    => 'required|string|max:100',
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'tujuan'    => 'required|string|max:255',
            'kendaraan' => 'required|string|max:255',
            'tgl_surat' => 'required|date',
            'tgl_berangkat' => 'required|date',
        ]);

        $sppd = Sppd::find($request->uid);
        $sppd->nosppd       = $request->nosppd;
        $sppd->nama         = strtoupper($request->nama);
        $sppd->jabatan      = strtoupper($request->jabatan);
        $sppd->tujuan       = strtoupper($request->tujuan);
        $sppd->kendaraan    = strtoupper($request->kendaraan);
        $sppd->tglsurat    = $request->tgl_surat;
        $sppd->tglberangkat = $request->tgl_berangkat;
        // $sppd->created_at   = Carbon::now();

        if ($sppd->save()) {
            return redirect()->route('sppd')->with('success', 'SPPD berhasil diperbarui.');
        } else {
            return redirect()->route('sppd')->with('error', 'SPPD gagal diperbarui.');
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'uid'   => 'required|string'
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'ID surat tidak diketahui.']);

        $drop = Sppd::where('id', $id)->delete();
        if ($drop) {
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Data gagal dihapus.']);
        }
    }
}
