<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\Instansi;
use App\Models\Jra;
use App\Models\Sppd;
use App\Models\TempatBerkas;
use App\Models\UnitKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OutboxController extends Controller
{
    public function __construct() {
        $this->middleware('permission:surat keluar', ['only' => ['index', 'serverside', 'show']]);
        $this->middleware('permission:input surat keluar', ['only' => ['store', 'create', 'last_sppd', 'check_surat']]);
        $this->middleware('permission:edit surat keluar', ['only' => ['edit', 'update', 'duplikat']]);
        $this->middleware('permission:hapus surat keluar', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        // $outbox  = ArsipSurat::where('JENISSURAT', 'Keluar')->orderBy('TGLENTRY', 'desc')->limit(20)->get();
        // foreach ($outbox as $r => $ibx) {
        //     $outbox[$r]->uid = Crypt::encryptString($ibx->NO);
        // }
        $outbox = [];
        $data  = [
            'outbox' => $outbox
        ];

        return view('main.outbox.index', $data);
    }

    public function serverside()
    {
        $request = Request();
        $start = $request->start;
        $length = $request->length;

        $query = ArsipSurat::query();
        $query->where('JENISSURAT', 'Keluar');
        $totalData = $query->count();

        // search query
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('NOSURAT', 'like', "%$search%")
                    ->orWhere('drkpd', 'like', "%$search%")
                    ->orWhere('PERIHAL', 'like', "%$search%")
                    ->orWhere('ISI', 'like', "%$search%")
                    ->orWhere('KLAS3', 'like', "%$search%");
            });
        }

        // sorting
        // if ($request->has('order')) {
        //     $orderColumnIndex = $request->order[0]['column'];
        //     $orderDirection = $request->order[0]['dir'];
        //     $columns = $request->get('columns');
        //     $columnName = $columns[$orderColumnIndex]['data'];
        //     $query->orderBy($columnName, $orderDirection);
        // } else {
        //     $query->orderBy('TGLENTRY', 'desc');
        // }

        $totalFiltered = $query->count();
        $query->orderBy('NO', 'desc');
        // $query->offset($start)->limit($length);
        $query->skip($start)->take($length);
        
        $outbox = $query->get();

        // manipulate fields data
        $data = [];
        foreach ($outbox as $r => $ibx) {
            $data[] = [
                // 'NO' => $ibx->NO,
                'nomor'     => $ibx->NOSURAT,
                'no_agenda' => $ibx->NOAGENDA,
                'klasifikasi' => $ibx->SIFAT_SURAT,
                'berkas'    => $ibx->NAMABERKAS,
                'wilayah'   => $ibx->WILAYAH,
                'isi_surat' => $ibx->ISI,
                'tanggal'   => $ibx->TGLSURAT,
                'kepada'    => $ibx->drkpd,
                'perihal'   => $ibx->PERIHAL,
                'kode'      => $ibx->KLAS3,
                'tgl_buat'  => Carbon::parse($ibx->TGLENTRY)->isoFormat('DD-MMM-YYYY'),
                'tujuan'    => $ibx->NAMAUP,
                'uid'       => Crypt::encryptString($ibx->NO),
                'option'    => '<div class="btn-group-vertical" role="group" aria-label="Second group">
                                    <a href="'. route('outbox.edit', Crypt::encryptString($ibx->NO)) .'" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                    <button type="button" class="btn btn-outline-info bs-tooltip" title="Tindak Lanjut">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                    </button>
                                    <button type="button" class="btn btn-outline-success bs-tooltip" title="Cetak Surat">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    </button>
                                    <button type="button" class="btn btn-danger bs-tooltip" title="Hapus Surat" onclick="_delete(\''. Crypt::encryptString($ibx->NO) .'\')">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </div>',
            ];
        }

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $data = [
            'jra'       => Jra::all(),
            'berkas'    => TempatBerkas::all(),
            'instansi'  => Instansi::all(),
        ];
        return view('main.outbox.new', $data);
    }

    public function duplicate()
    {
        $data = [];

        return view('main.outbox.duplikat', $data);
    }

    public function edit($id)
    {
        $no    = (Crypt::decryptString($id));
        if (!$no) return abort(404);
        $outbox = ArsipSurat::where('NO', $no)->first();
        $sppd = empty($outbox->nosppd) ? [] : Sppd::where('nosppd', $outbox->nosppd)->first();
        $outbox->uid = $id;
        $data  = [
            'jra'       => Jra::all(),
            'berkas'    => TempatBerkas::all(),
            'instansi'  => Instansi::all(),
            'outbox'    => $outbox,
            'sppd'      => $sppd,
        ];

        return view('main.outbox.edit', $data);
    }

    public function show($id)
    {
        $no    = json_decode(Crypt::decryptString($id));
        $outbox = ArsipSurat::where('NO', $no)->first();
        $data  = [
            'outbox' => $outbox,
        ];

        return view('main.outbox.show', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'berkas'        => 'required|string|max:255',
            'tgl_naik'      => 'required|date',
            'tgl_surat'     => 'required|date',
            'darikepada'    => 'required|string|max:255',
            'wilayah'       => 'required|string|max:100',
            'perihal'       => 'nullable|string|max:255',
            'isi'           => 'required|string|max:255',
            'klasifikasi_kode' => 'required|string|max:10',
            'urut'          => 'required|numeric',
            'no_surat'      => 'required|string|max:15',
            'aktif'         => 'nullable|numeric',
            'inaktif'       => 'nullable|numeric',
            'thn_aktif'     => 'nullable|numeric',
            'thn_inaktif'   => 'nullable|numeric',
            'jra'           => 'nullable|string|max:255',
            'nilai_guna'    => 'nullable|string|max:100',
            'tempat_berkas' => 'required|string|max:100',
            'perkembangan'  => 'required|string|max:100',
            'tgl_diteruskan'=> 'nullable|date',
            'nama_up'       => 'nullable|string|max:100',
            'kode_up'       => 'nullable|string|max:10',
            'sifat_surat'   => 'nullable|string|max:100',
            'ttd'           => 'nullable|string|max:100',
            // 'tindakan'      => 'nullable|string|max:255',
            // 'tgl_balas'     => 'nullable|date',
            'gambar'        => 'nullable|array|max:5',
            'gambar.*'      => 'nullable|mimes:jpeg,jpg,png|max:3096',
            'lampiran'      => 'nullable|mimes:pdf|max:5126',

            'sppd'          => 'nullable|string|max:50',
            'nama'          => 'nullable|string|max:100',
            'jabatan'       => 'nullable|string|max:100',
            'tujuan'        => 'nullable|string|max:255',
            'kendaraan'     => 'nullable|string|max:100',
            'berangkat'     => 'nullable|date',
        ]);

        $last = ArsipSurat::where('JENISSURAT', 'Keluar')->orderBy('NO', 'desc')->first();
        // $kode_urut = intval($last->NOURUT) + 1;
        // $kode = explode('/', $last->poenx)[0];
        // $kode_urut = intval(substr(substr($kode, 1), 0, -2)) + 1;
        $kode_urut = $last->TAHUN == date('Y') ? intval($last->NOURUT) + 1 : 1;

        $outbox = new ArsipSurat();
        $outbox->NAMABERKAS      = $request->berkas;
        $outbox->TGLTERIMA       = Carbon::parse($request->tgl_naik)->format('Y-m-d');
        $outbox->TGLSURAT        = Carbon::parse($request->tgl_surat)->format('Y-m-d');
        $outbox->drkpd           = $request->darikepada;
        $outbox->NAMAKOTA        = $request->wilayah;
        $outbox->PERIHAL         = $request->perihal;
        $outbox->ISI             = $request->isi;
        $outbox->masalahjra      = ' ';
        $outbox->KLAS3           = $request->klasifikasi_kode;
        // $outbox->NOURUT          = $request->urut;
        // $outbox->NOAGENDA        = $request->urut;
        // $outbox->noagenda2       = $request->urut;
        $outbox->NOURUT          = $kode_urut;
        $outbox->NOAGENDA        = $kode_urut;
        $outbox->noagenda2       = $kode_urut;
        $outbox->NOSURAT         = $request->no_surat ?? ' ';
        $outbox->AKTIF           = $request->aktif;
        $outbox->INAKTIF         = $request->inaktif;
        $outbox->THAKTIF         = $request->thn_aktif;
        $outbox->THINAKTIF       = $request->thn_inaktif;
        $outbox->KETJRA          = $request->jra;
        $outbox->NILAIGUNA       = $request->nilai_guna;
        $outbox->TMPTBERKAS      = $request->tempat_berkas;
        $outbox->TK_PERKEMBANGAN = $request->perkembangan;
        $outbox->TGLTERUS        = Carbon::parse($request->tgl_diteruskan)->format('Y-m-d');
        $outbox->NAMAUP          = $request->nama_up;
        $outbox->KODEUP          = $request->kode_up;
        $outbox->nosppd          = $request->sppd;
        $outbox->SIFAT_SURAT     = $request->sifat_surat;
        $outbox->BALAS           = ' ';
        $outbox->TGLBALAS        = ' ';
        $outbox->CATATAN         = ' ';
        $outbox->ditandatanganioleh = $request->ttd;

        $outbox->SERIDPA         = '';
        $outbox->NO_SISIP        = 0;
        $outbox->nodef           = ' ';
        $outbox->TDT             = ' ';
        $outbox->pinjam          = ' ';
        $outbox->TTD             = 'Nama Perwakilan';
        $outbox->PIMPINAN        = 'Perwakilan';

        // Header
        $outbox->poenx           = 'K' . $kode_urut . date('d') . '/' . date('m') . '/' . date('Y') . ' ' . date('H:i:s');
        $outbox->KD_WILAYAH      = Auth::user()->kode ?? 'ID3331';
        $outbox->WILAYAH         = 'PEMERINTAH KABUPATEN KARANGANYAR';
        $outbox->NAMAINSTANSI    = Auth::user()->instansi->nama_instansi ?? '-';
        $outbox->BULAN           = date('m');
        $outbox->TAHUN           = date('Y');
        $outbox->MEDIA           = 'Teks';
        
        // Operator
        $outbox->Posisi          = Auth::user()->jurusan;
        $outbox->KODEOPR         = Auth::user()->nama_lengkap;
        $outbox->JENISSURAT      = 'Keluar';
        $outbox->TGLENTRY        = date('Y-m-d');
        $outbox->JAM             = date('H:i:s');

        $save = $outbox->save();

        if ($save) {
            if (!empty($request->sppd)) {
                $sppd = new Sppd();
                $sppd->nosppd       = $request->sppd;
                $sppd->nama         = $request->nama;
                $sppd->jabatan      = $request->jabatan;
                $sppd->tujuan       = $request->tujuan;
                $sppd->kendaraan    = $request->kendaraan;
                $sppd->tglsurat     = Carbon::parse($request->tgl_surat)->format('Y-m-d');
                $sppd->tglberangkat = Carbon::parse($request->berangkat)->format('Y-m-d');
                $sppd->save();
            }
            return redirect()->route('outbox')->with('success', 'Surat keluar berhasil disimpan.');
        } else {
            return redirect()->route('outbox')->with('error', 'Surat keluar gagal disimpan.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'uid'           => 'required|string',
            'berkas'        => 'required|string|max:255',
            'tgl_naik'      => 'required|date',
            'tgl_surat'     => 'required|date',
            'darikepada'    => 'required|string|max:255',
            'wilayah'       => 'required|string|max:100',
            'perihal'       => 'nullable|string|max:255',
            'isi'           => 'required|string|max:255',
            'klasifikasi_kode' => 'required|string|max:10',
            'urut'          => 'required|numeric',
            'no_surat'      => 'nullable|string|max:15',
            'aktif'         => 'nullable|numeric',
            'inaktif'       => 'nullable|numeric',
            'thn_aktif'     => 'nullable|numeric',
            'thn_inaktif'   => 'nullable|numeric',
            'jra'           => 'nullable|string|max:255',
            'nilai_guna'    => 'nullable|string|max:100',
            'tempat_berkas' => 'required|string|max:100',
            'perkembangan'  => 'required|string|max:100',
            'tgl_diteruskan'=> 'nullable|date',
            'nama_up'       => 'nullable|string|max:100',
            'kode_up'       => 'nullable|string|max:10',
            'sifat_surat'   => 'nullable|string|max:100',
            'ttd'           => 'nullable|string|max:100',
            // 'tindakan'      => 'nullable|string|max:255',
            // 'tgl_balas'     => 'nullable|date',
            'gambar'        => 'nullable|array|max:5',
            'gambar.*'      => 'nullable|mimes:jpeg,jpg,png|max:3096',
            'lampiran'      => 'nullable|mimes:pdf|max:5126',

            'sppd'          => 'nullable|string|max:50',
            'nama'          => 'nullable|string|max:100',
            'jabatan'       => 'nullable|string|max:100',
            'tujuan'        => 'nullable|string|max:255',
            'kendaraan'     => 'nullable|string|max:100',
            'berangkat'     => 'nullable|date',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) return redirect()->back()->with('error', 'No. Agenda tidak diketahui.');

        $outbox = ArsipSurat::where('NO', $id)->where('JENISSURAT', 'Keluar')->first();
        $outbox->NAMABERKAS      = $request->berkas;
        $outbox->TGLTERIMA       = Carbon::parse($request->tgl_naik)->format('Y-m-d');
        $outbox->TGLSURAT        = Carbon::parse($request->tgl_surat)->format('Y-m-d');
        $outbox->drkpd           = $request->darikepada;
        $outbox->NAMAKOTA        = $request->wilayah;
        $outbox->PERIHAL         = $request->perihal;
        $outbox->ISI             = $request->isi;
        // $outbox->masalahjra      = ' ';
        $outbox->KLAS3           = $request->klasifikasi_kode;
        // $outbox->NOURUT          = $request->urut;
        // $outbox->NOAGENDA        = $request->urut;
        // $outbox->noagenda2       = $request->urut;
        $outbox->NOSURAT         = $request->no_surat ?? ' ';
        $outbox->AKTIF           = $request->aktif;
        $outbox->INAKTIF         = $request->inaktif;
        $outbox->THAKTIF         = $request->thn_aktif;
        $outbox->THINAKTIF       = $request->thn_inaktif;
        $outbox->KETJRA          = $request->jra;
        $outbox->NILAIGUNA       = $request->nilai_guna;
        $outbox->TMPTBERKAS      = $request->tempat_berkas;
        $outbox->TK_PERKEMBANGAN = $request->perkembangan;
        $outbox->TGLTERUS        = Carbon::parse($request->tgl_diteruskan)->format('Y-m-d');
        $outbox->NAMAUP          = $request->nama_up;
        $outbox->KODEUP          = $request->kode_up;
        $outbox->nosppd          = $request->sppd;
        $outbox->SIFAT_SURAT     = $request->sifat_surat;
        // $outbox->BALAS           = ' ';
        // $outbox->TGLBALAS        = ' ';
        // $outbox->CATATAN         = ' ';
        $outbox->ditandatanganioleh = $request->ttd;

        // Header
        $outbox->KD_WILAYAH      = Auth::user()->kode ?? 'ID3331';
        // $outbox->WILAYAH         = 'PEMERINTAH KABUPATEN KARANGANYAR';
        $outbox->NAMAINSTANSI    = Auth::user()->instansi->nama_instansi ?? '-';
        // $outbox->BULAN           = date('m');
        // $outbox->TAHUN           = date('Y');
        $outbox->MEDIA           = 'Teks';
        
        // Operator
        $outbox->Posisi          = Auth::user()->jurusan;
        $outbox->KODEOPR         = Auth::user()->nama_lengkap;
        // $outbox->JENISSURAT      = 'Keluar';
        // $outbox->TGLENTRY        = date('Y/m/d');
        // $outbox->JAM             = date('H:i:s');

        $save = $outbox->save();

        if ($save) {
            if (!empty($request->sppd)) {
                $sppd = Sppd::where('nosppd', $request->sppd)->first();
                $sppd->nosppd       = $request->sppd;
                $sppd->nama         = $request->nama;
                $sppd->jabatan      = $request->jabatan;
                $sppd->tujuan       = $request->tujuan;
                $sppd->kendaraan    = $request->kendaraan;
                $sppd->tglsurat     = Carbon::parse($request->tgl_surat)->format('Y-m-d');
                $sppd->tglberangkat = Carbon::parse($request->berangkat)->format('Y-m-d');
                $sppd->save();
            }
            return redirect()->route('outbox')->with('success', 'Surat keluar berhasil diupdate.');
        } else {
            return redirect()->route('outbox')->with('error', 'Surat keluar gagal diupdate.');
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'uid'    => 'required|string'
        ]);

        $id = json_decode(Crypt::decryptString($request->uid));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $outbox = ArsipSurat::where('NO', $id)->delete();
        if ($outbox) {
            return response()->json(['status' => 'success', 'message' => 'Surat keluar berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat keluar gagal dihapus.']);
        }
    }

    public function check_surat(Request $request)
    {
        $request->validate([
            'nosurat'   => 'required|string|max:100',
        ]);

        $res = ArsipSurat::where('JENISSURAT', 'Keluar')
                ->where('NOSURAT', 'like', '%'.$request->nosurat.'%')
                ->where(function($q) {
                    $q->whereNull('nosppd')
                      ->orWhere('nosppd', '');
                })
                ->orderBy('noagenda2', 'asc')->get();

        if (!$res || count($res) < 1) return response()->json(['status' => 'failed', 'message' => 'Nomor surat tidak ditemukan.', 'data' => []]);

        return response()->json(['status' => 'success', 'message' => 'Nomor surat ditemukan.', 'data' => $res]);
    }

    public function last_sppd()
    {
        $last = Sppd::orderBy('id', 'desc')->first();
        Log::info('Last SPPD: ' . Carbon::parse($last->tglsurat)->format('Y'));
        if (!$last) {
            return response()->json(['nomor' => 1]);
        }
        if ($last && (Carbon::parse($last->tglsurat)->format('Y') < date('Y'))) {
            return response()->json(['nomor' => 1]);
        }

        $split = explode('/', $last->nosppd)[1];
        $lastNumber = intval(explode('.', $split)[0]) + 1;
        // $lastNumber = intval(substr($last->nosppd, 4)) + 1;
        // $newNosppd = 'SPPD' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['nomor' => $lastNumber]);
    }

    public function duplikat(Request $request)
    {
        $request->validate([
            'uid'    => 'required|string',
            'jumlah' => 'required|integer|min:1'
        ]);

        $id = base64_decode($request->uid);
        if (!$id || intval($id) < 1) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $surat = ArsipSurat::where('NO', intval($id))->first();
        if (!$surat) {
            return response()->json(['status' => 'failed', 'message' => 'Surat tidak ditemukan.']);
        }

        $last = ArsipSurat::where('JENISSURAT', 'Keluar')->orderBy('NO', 'desc')->first();
        $kode_urut = ($last->TAHUN == date('Y') ? intval($last->NOURUT) + 1 : 1);

        $data  = [
            'id_surat'   => $surat->NO,
            'nomor_surat' => $surat->NOSURAT,
            'nomor_awal' => $kode_urut,
            'jumlah'     => intval($request->jumlah),
            'tahun'      => date('Y'),
        ];
        $start = $kode_urut;
        $list  = [$surat];
        for ($i = 0; $i < intval($request->jumlah); $i++) {
            $newSurat = $surat->replicate();
            $newSurat->NOURUT = $start;
            $newSurat->NOAGENDA = $start;
            $newSurat->noagenda2 = $start;
            $newSurat->poenx = 'K' . $start . date('d') . '/' . date('m') . '/' . date('Y') . ' ' . date('H:i:s');
            $newSurat->TGLENTRY = date('Y-m-d');
            $newSurat->JAM = date('H:i:s');

            $pass1 = explode('/', $surat->NOSURAT);
            $pass2 = count($pass1) > 1 ? explode('.', $pass1[1]) : [];
            $newNumber = '';
            foreach ($pass1 as $key => $value) {
                if ($key == 0) {
                    $newNumber .= $value;
                } elseif ($key == 1) {
                    foreach ($pass2 as $k => $item) {
                        if ($k == 0) {
                            $newNumber .= '/'. $start;
                        } else {
                            $newNumber .= '.' . $item;
                        }
                    }
                } else {
                    $newNumber .= '/' . $value;
                }
            }

            $newSurat->NOSURAT = $newNumber;
            $newSurat->save();

            $list[] = $newSurat;
            if ($i < (intval($request->jumlah) - 1)) {
                $start++;
            }
        }
        Log::info('Duplikat Surat Keluar: ', $list);

        $pdf = Pdf::loadView('main.outbox.templates.duplikat_full', ['data' => $list]);
        $folder = public_path('datas/uploads/duplikat');
        $name  = 'duplikat_surat_keluar_' . date('Ymd_His') . '.pdf';
        $path  = $folder . '/' . $name;
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true, true);
        }
        $pdf->save($path);

        $data += [
            'nomor_akhir' => $start,
        ];

        return response()->json(['status' => 'success', 'message' => 'Surat berhasil diduplikat.', 'data' => $list]);
    }
}
