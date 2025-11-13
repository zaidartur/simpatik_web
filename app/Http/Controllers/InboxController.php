<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\Jra;
use App\Models\TempatBerkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InboxController extends Controller
{
    public function index()
    {
        // $inbox  = ArsipSurat::where('JENISSURAT', 'Masuk')->orderBy('TGLENTRY', 'desc')->limit(20)->get();
        // foreach ($inbox as $r => $ibx) {
        //     $inbox[$r]->uid = Crypt::encryptString($ibx->NO);
        // }
        $inbox = [];
        $data  = [
            'inbox' => $inbox
        ];

        return view('main.inbox.index', $data);
    }

    public function serverside()
    {
        $request = Request();
        $start = $request->start;
        $length = $request->length;

        $query = ArsipSurat::query();
        $query->where('JENISSURAT', 'Masuk');
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
        $query->orderBy('TGLENTRY', 'desc');
        // $query->offset($start)->limit($length);
        $query->skip($start)->take($length);
        
        $inbox = $query->get();

        // manipulate fields data
        $data = [];
        foreach ($inbox as $r => $ibx) {
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
                'tgl_buat'  => $ibx->TGLENTRY,
                'uid'       => Crypt::encryptString($ibx->NO),
                'option'    => '<div class="btn-group-vertical" role="group" aria-label="Second group">
                                    <a href="'. route('inbox.edit', Crypt::encryptString($ibx->NO)) .'" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>
                                    <button type="button" class="btn btn-outline-primary bs-tooltip" title="Disposisi Surat">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                    </button>
                                    <button type="button" class="btn btn-outline-success bs-tooltip" title="Cetak Surat" onclick="printPdf(`'. Crypt::encryptString($ibx->NO) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    </button>
                                    <button type="button" class="btn btn-outline-info bs-tooltip" title="Tindak Lanjut">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                    </button>
                                    <button type="button" class="btn btn-danger bs-tooltip" title="Hapus Surat">
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
            'jra'    => Jra::all(),
            'berkas' => TempatBerkas::all(),
        ];
        return view('main.inbox.new', $data);
    }

    public function edit($id)
    {
        $no    = json_decode(Crypt::decryptString($id));
        if (!$no) return abort(404);
        $inbox = ArsipSurat::where('NO', $no)->first();
        $data  = [
            'inbox' => $inbox,
            'jra'    => Jra::all(),
            'berkas' => TempatBerkas::all(),
        ];

        return view('main.inbox.edit', $data);
    }

    public function show($id)
    {
        $no    = json_decode(Crypt::decryptString($id));
        $inbox = ArsipSurat::where('NO', $no)->first();
        $data  = [
            'inbox' => $inbox,
        ];

        return view('main.inbox.view', $data);
    }

    public function nomor_urut(Request $request)
    {
        $request->validate([
            'type'  => 'required|string|max:10',
        ]);

        $last = ArsipSurat::where('JENISSURAT', $request->type)->orderBy('NO', 'desc')->first();
        $kode_urut = intval($last->NOURUT) + 1;

        return response()->json(['status' => 'success', 'urut' => $kode_urut]);
    }

    public function get_jra(Request $request)
    {
        $request->validate([
            'kode'  => 'required|string|max:10',
            'name'  => 'required|string|max:100',
        ]);

        $jra = Jra::where('KLAS3', $request->kode)->where('MASALAH3', $request->name)->first();

        if ($jra) {
            $jra->thn_aktif = date('Y') + intval($jra->RAKTIF);
            $jra->thn_inaktif = date('Y') + intval($jra->RAKTIF) + intval($jra->RINAKTIF);
            return response()->json(['status' => 'success', 'jra' => $jra]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'JRA tidak ditemukan.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'berkas'        => 'required|string|max:255',
            'tgl_terima'    => 'required|date',
            'tgl_surat'     => 'required|date',
            'darikepada'    => 'required|string|max:255',
            'wilayah'       => 'required|string|max:100',
            'perihal'       => 'required|string|max:255',
            'isi'           => 'required|string|max:255',
            'klasifikasi_kode' => 'required|string|max:10',
            'urut'          => 'required|numeric',
            'no_surat'      => 'required|string|max:15',
            'aktif'         => 'required|numeric',
            'inaktif'       => 'required|numeric',
            'thn_aktif'     => 'required|numeric',
            'thn_inaktif'   => 'required|numeric',
            'jra'           => 'required|string|max:255',
            'nilai_guna'    => 'required|string|max:100',
            'tempat_berkas' => 'required|string|max:100',
            'perkembangan'  => 'required|string|max:100',
            'tgl_diteruskan'=> 'nullable|date',
            'diteruskan_kpd'=> 'nullable|string|max:100',
            'sifat_surat'   => 'nullable|string|max:100',
            'tindakan'      => 'nullable|string|max:255',
            'tgl_balas'     => 'nullable|date',
            'gambar'        => 'nullable|array|max:5',
            'gambar.*'      => 'nullable|mimes:jpeg,jpg,png|max:3096',
            'lampiran'      => 'nullable|mimes:pdf|max:5126',
        ]);

        $last = ArsipSurat::where('JENISSURAT', 'Masuk')->orderBy('NO', 'desc')->first();
        $kode_urut = intval($last->NOURUT) + 1;

        $inbox = new ArsipSurat();
        $inbox->NAMABERKAS      = $request->berkas;
        $inbox->TGLTERIMA       = Carbon::parse($request->tgl_terima)->format('Y/m/d');
        $inbox->TGLSURAT        = Carbon::parse($request->tgl_surat)->format('Y/m/d');
        $inbox->drkpd           = $request->darikepada;
        $inbox->NAMAKOTA        = $request->wilayah;
        $inbox->PERIHAL         = $request->perihal;
        $inbox->ISI             = $request->isi;
        $inbox->masalahjra      = $request->berkas;
        $inbox->KLAS3           = $request->klasifikasi_kode;
        $inbox->NOURUT          = $request->urut;
        $inbox->NOAGENDA        = $request->urut;
        $inbox->noagenda2       = $request->urut;
        $inbox->NOSURAT         = $request->no_surat;
        $inbox->AKTIF           = $request->aktif;
        $inbox->INAKTIF         = $request->inaktif;
        $inbox->THAKTIF         = $request->thn_aktif;
        $inbox->THINAKTIF       = $request->thn_inaktif;
        $inbox->KETJRA          = $request->jra;
        $inbox->NILAIGUNA       = $request->nilai_guna;
        $inbox->TMPTBERKAS      = $request->tempat_berkas;
        $inbox->TK_PERKEMBANGAN = $request->perkembangan;
        $inbox->TGLTERUS        = Carbon::parse($request->tgl_diteruskan)->format('Y/m/d');
        $inbox->NAMAUP          = $request->diteruskan_kpd;
        $inbox->KODEUP          = null;
        $inbox->SIFAT_SURAT     = $request->sifat_surat;
        $inbox->BALAS           = $request->tindakan;
        $inbox->TGLBALAS        = Carbon::parse($request->tgl_balas)->format('Y/m/d');

        $inbox->NO_SISIP        = null;
        $inbox->nodef           = ' ';
        $inbox->TDT             = ' ';

        // Header
        $inbox->poenx           = 'M' . $kode_urut . date('d') .'/' . date('m') . '/0001 ' . date('H:i:s');
        $inbox->KD_WILAYAH      = Auth::user()->kode ?? 'ID3331';
        $inbox->WILAYAH         = 'PEMERINTAH KABUPATEN KARANGANYAR';
        $inbox->NAMAINSTANSI    = Auth::user()->instansi->nama_instansi ?? '-';
        $inbox->BULAN           = date('m');
        $inbox->TAHUN           = date('Y');
        $inbox->MEDIA           = 'Teks';
        
        // Operator
        $inbox->Posisi          = Auth::user()->jurusan;
        $inbox->KODEOPR         = Auth::user()->nama_lengkap;
        $inbox->JENISSURAT      = 'Masuk';
        $inbox->TGLENTRY        = date('Y/m/d');
        $inbox->JAM             = date('H:i:s');

        $save = $inbox->save();

        if ($save) {
            return redirect()->route('inbox')->with('success', 'Surat masuk berhasil disimpan.');
        } else {
            return redirect()->route('inbox')->with('error', 'Surat masuk gagal disimpan.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|numeric',
            'berkas'        => 'required|string|max:255',
            'tgl_terima'    => 'required|date',
            'tgl_surat'     => 'required|date',
            'darikepada'    => 'required|string|max:255',
            'wilayah'       => 'required|string|max:100',
            'perihal'       => 'required|string|max:255',
            'isi'           => 'required|string|max:255',
            'klasifikasi_kode' => 'required|string|max:10',
            'urut'          => 'required|numeric',
            'no_surat'      => 'required|string|max:15',
            'aktif'         => 'required|numeric',
            'inaktif'       => 'required|numeric',
            'thn_aktif'     => 'required|numeric',
            'thn_inaktif'   => 'required|numeric',
            'jra'           => 'required|string|max:255',
            'nilai_guna'    => 'required|string|max:100',
            'tempat_berkas' => 'required|string|max:100',
            'perkembangan'  => 'required|string|max:100',
            'tgl_diteruskan'=> 'nullable|date',
            'diteruskan_kpd'=> 'nullable|string|max:100',
            'sifat_surat'   => 'nullable|string|max:100',
            'tindakan'      => 'nullable|string|max:255',
            'tgl_balas'     => 'nullable|date',
            'gambar'        => 'nullable|array|max:5',
            'gambar.*'      => 'nullable|mimes:jpeg,jpg,png|max:3096',
            'lampiran'      => 'nullable|mimes:pdf|max:5126',
        ]);

        $inbox = ArsipSurat::where('NO', $request->id)->first();
        $inbox->NAMABERKAS      = $request->berkas;
        $inbox->TGLTERIMA       = $request->tgl_terima;
        $inbox->TGLSURAT        = $request->tgl_surat;
        $inbox->drkpd           = $request->darikepada;
        $inbox->NAMAKOTA        = $request->wilayah;
        $inbox->PERIHAL         = $request->perihal;
        $inbox->ISI             = $request->isi;
        $inbox->masalahjra      = $request->berkas;
        $inbox->KLAS3           = $request->klasifikasi_kode;
        // $inbox->NOURUT          = $request->urut;
        // $inbox->NOAGENDA        = $request->urut;
        // $inbox->noagenda2       = $request->urut;
        $inbox->NOSURAT         = $request->no_surat;
        $inbox->AKTIF           = $request->aktif;
        $inbox->INAKTIF         = $request->inaktif;
        $inbox->THAKTIF         = $request->thn_aktif;
        $inbox->THINAKTIF       = $request->thn_inaktif;
        $inbox->KETJRA          = $request->jra;
        $inbox->NILAIGUNA       = $request->nilai_guna;
        $inbox->TMPTBERKAS      = $request->tempat_berkas;
        $inbox->TK_PERKEMBANGAN = $request->perkembangan;
        $inbox->TGLTERUS        = $request->tgl_diteruskan;
        $inbox->NAMAUP          = $request->diteruskan_kpd;
        $inbox->KODEUP          = null;
        $inbox->SIFAT_SURAT     = $request->sifat_surat;
        $inbox->BALAS           = $request->tindakan;
        $inbox->TGLBALAS        = $request->tgl_balas;

        // Header
        $inbox->KD_WILAYAH      = Auth::user()->kode ?? 'ID3331';
        // $inbox->WILAYAH         = 'PEMERINTAH KABUPATEN KARANGANYAR';
        $inbox->NAMAINSTANSI    = Auth::user()->instansi->nama_instansi ?? '-';
        // $inbox->BULAN           = date('m');
        // $inbox->TAHUN           = date('Y');
        $inbox->MEDIA           = 'Teks';
        
        // Operator
        $inbox->Posisi          = Auth::user()->jurusan;
        $inbox->KODEOPR         = Auth::user()->nama_lengkap;
        $inbox->JENISSURAT      = 'Masuk';
        // $inbox->TGLENTRY        = date('Y/m/d');
        // $inbox->JAM             = date('H:i:s');

        $save = $inbox->save();

        if ($save) {
            return redirect()->route('inbox')->with('success', 'Surat masuk berhasil diperbarui.');
        } else {
            return redirect()->route('inbox')->with('error', 'Surat masuk gagal diperbarui.');
        }
    }

    public function drop(Request $request)
    {
        $request->validate([
            'uid'    => 'required|string'
        ]);

        $id = json_decode(Crypt::decryptString($request->uid));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $inbox = ArsipSurat::where('NO', $id)->delete();
        if ($inbox) {
            return response()->json(['status' => 'success', 'message' => 'Surat masuk berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk gagal dihapus.']);
        }
    }

    public function view_pdf($uid)
    {
        // if (empty($uid)) return abort(404);
        $id  = Crypt::decryptString($uid);
        if (!$id) return abort(404);

        $surat = ArsipSurat::where('NO', $id)->first();
        if (!$surat) return abort(404);

        $pdf = $this->build_pdf($surat);
        return $pdf->stream('surat_masuk.pdf');
    }

    public function download_pdf(Request $request)
    {
        $pdf = $this->build_pdf($request->inbox);
        return $pdf->download('surat_masuk.pdf');
    }

    public function build_pdf($inbox)
    {
        $pdf = Pdf::setPaper('letter', 'portrait');
        // $pdf = Pdf::setPaper([0, 0, 792, 612], 'portrait');

        $pdf->loadView('main.inbox.template', ['data' => $inbox]);
        return $pdf;
    }
}
