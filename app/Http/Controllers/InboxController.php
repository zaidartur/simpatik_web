<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\Jra;
use App\Models\Pimpinan;
use App\Models\TempatBerkas;
use App\Services\PdfSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Modifiers\AutoRotate;
use setasign\Fpdi\Fpdi;

class InboxController extends Controller
{

    public function __construct() {
        $this->middleware('permission:surat masuk', ['only' => ['index', 'serverside', 'show']]);
        $this->middleware('permission:input surat masuk', ['only' => ['store', 'create', 'view_pdf', 'nomor_urut', 'upload_file']]);
        $this->middleware('permission:edit surat masuk', ['only' => ['edit', 'update', 'upload_file']]);
        $this->middleware('permission:hapus surat masuk', ['only' => ['destroy']]);
    }

    public function index()
    {
        $user  = Auth::user();
        $posisi = DB::table('instansi')->where('instansi', Auth::user()->jurusan)->first();
        $data  = [
            'posisi'=> json_decode($posisi->akses, true),
            'years' => ArsipSurat::select('TAHUN')->distinct()->orderBy('TAHUN', 'desc')->get(),
        ];

        return view('main.inbox.index', $data);
    }

    public function serverside()
    {
        $request = Request();
        $start = $request->start;
        $length = $request->length;
        $posisi = DB::table('instansi')->where('instansi', Auth::user()->jurusan)->first();
        $akses  = json_decode($posisi->akses, true);

        $query = ArsipSurat::query();
        $query->where('JENISSURAT', 'Masuk');
        if ($request->has('klasifikasi') && $request->klasifikasi != '') {
            $query->where('SIFAT_SURAT', $request->klasifikasi);
        }
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('TAHUN', $request->tahun);
        }
        if ($request->has('posisi') && $request->posisi != '' && in_array($request->posisi, $akses)) {
            $query->where('Posisi', $request->posisi);
        } else {
            if (Auth::user()->level == 'operator' && Auth::user()->jurusan == 'Sekretaris Daerah') {
                $query->whereIn('Posisi', ['Sekretaris Daerah', 'Bupati']);
            }
            if (Auth::user()->level == 'operator' && Auth::user()->jurusan == 'Wakil Bupati') {
                $query->whereIn('Posisi', ['Wakil Bupati', 'Bupati']);
            }
            if (Auth::user()->level == 'operator' && Auth::user()->jurusan == 'Bupati') {
                $query->whereIn('Posisi', ['Bupati']);
            }
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('statussurat', $request->status);
        }
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

        $totalFiltered = $query->count();
        $query->orderBy('NO', 'desc');
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
                'posisi'    => $ibx->Posisi,
                'class'     => ($ibx->Posisi == 'Sekretaris Daerah' ? 'badge-info' : ($ibx->Posisi == 'Wakil Bupati' ? 'badge-secondary' : ($ibx->Posisi == 'Bupati' ? 'badge-primary' : 'badge-dark'))),
                'uid'       => Crypt::encryptString($ibx->NO),
                'option'    => '<div class="btn-group-vertical" role="group" aria-label="Second group">'.
                                    // Detail Surat
                                    (Auth::user()->hasRole(['administrator', 'setda', 'wabup', 'bupati']) ? '<button type="button" class="btn btn-info bs-tooltip" title="Detail Surat" onclick="viewSurat(`'. base64_encode(json_encode($ibx)) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    </button>' : '') .

                                    // Edit Surat
                                    (Auth::user()->hasRole(['administrator','umum']) ? '<a href="'. route('inbox.edit', Crypt::encryptString($ibx->NO)) .'" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>' : '') .

                                    // Tindak Lanjut
                                    ((Auth::user()->hasRole(['administrator','setda', 'wabup']) && ($ibx->statussurat == 'disposisi') && ($ibx->Posisi == Auth::user()->jurusan || Auth::user()->level == 'administrator')) ? '<button type="button" class="btn btn-outline-primary bs-tooltip" title="Tindak Lanjut (Teruskan)" onclick="followUp(`'. Crypt::encryptString($ibx->NO) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                    </button>' : '') .

                                    // Selesai Tindak Lanjut
                                    (($ibx->statussurat == 'selesai') ? '<button type="button" class="btn btn-outline-success bs-tooltip" title="Surat sudah ditindak lanjuti">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </button>' : '') .

                                    // Menunggu Tindakan
                                    ((Auth::user()->hasRole(['umum']) && $ibx->statussurat == 'disposisi') ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    </button>' : '') .
                                    ((Auth::user()->hasRole(['setda']) && $ibx->Posisi == 'Bupati' && empty($ibx->tglbupati1) && !empty($ibx->pengirimsekda) && $ibx->statussurat == 'disposisi') ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    </button>' : '') .
                                    ((Auth::user()->hasRole(['bupati']) && $ibx->Posisi == 'Bupati' && !empty($ibx->tglbupati1) && !empty($ibx->pengirimsekda) && empty($ibx->tglsekda1) && $ibx->statussurat == 'disposisi') ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    </button>' : '') .

                                    // Cetak Surat
                                    ((Auth::user()->hasRole(['administrator', 'umum']) && $ibx->statussurat == 'selesai') ? '<button type="button" class="btn btn-outline-info bs-tooltip" title="Cetak Surat" onclick="printPdf(`'. Crypt::encryptString($ibx->NO) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    </button>' : '') .

                                    // Hapus Surat
                                    ((Auth::user()->hasRole(['administrator', 'umum'])) ? '<button type="button" class="btn btn-danger bs-tooltip" title="Hapus Surat" onclick="_delete(`'. Crypt::encryptString($ibx->NO) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>' : '') .

                                    // Tanggapi Surat
                                    ((Auth::user()->hasRole(['administrator']) && $ibx->statussurat == 'disposisi') ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    </button>' : '') . 
                                    ((Auth::user()->hasRole(['setda']) && $ibx->statussurat == 'disposisi' && (($ibx->Posisi == 'Bupati' && !empty($ibx->tglbupati1) && !empty($ibx->pengirimsekda)) || $ibx->Posisi == 'Sekretaris Daerah')) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    </button>' : '') .
                                    ((Auth::user()->hasRole(['wabup']) && $ibx->statussurat == 'disposisi' && empty($ibx->wakil)) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    </button>' : '') .
                                    ((Auth::user()->hasRole(['bupati']) && $ibx->statussurat == 'disposisi' && empty($ibx->tglbupati1)) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    </button>' : '') .

                                '</div>',
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
        $kode_urut = $last->TAHUN == date('Y') ? intval($last->NOURUT) + 1 : 1;

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
            'lampiran'      => 'nullable|string|max:100',
            'is_scan'       => 'nullable|mimes:pdf,jpeg,jpg,png|max:10240',
            // 'gambar'        => 'nullable|array|max:5',
            // 'gambar.*'      => 'nullable|mimes:jpeg,jpg,png|max:3096',
            // 'lampiran'      => 'nullable|mimes:pdf|max:5126',
        ]);

        $last = ArsipSurat::where('JENISSURAT', 'Masuk')->orderBy('NO', 'desc')->first();
        $kode_urut = $last->TAHUN == date('Y') ? intval($last->NOURUT) + 1 : 1;

        $inbox = new ArsipSurat();
        $inbox->NAMABERKAS      = $request->berkas;
        $inbox->TGLTERIMA       = Carbon::parse($request->tgl_terima)->format('Y-m-d');
        $inbox->TGLSURAT        = Carbon::parse($request->tgl_surat)->format('Y-m-d');
        $inbox->drkpd           = $request->darikepada;
        $inbox->NAMAKOTA        = $request->wilayah;
        $inbox->PERIHAL         = $request->perihal;
        $inbox->ISI             = $request->isi;
        $inbox->masalahjra      = $request->berkas;
        $inbox->KLAS3           = $request->klasifikasi_kode;
        // $inbox->NOURUT          = $request->urut;
        // $inbox->NOAGENDA        = $request->urut;
        // $inbox->noagenda2       = $request->urut;
        $inbox->NOURUT          = $kode_urut;
        $inbox->NOAGENDA        = $kode_urut;
        $inbox->noagenda2       = $kode_urut;
        $inbox->NOSURAT         = $request->no_surat;
        $inbox->AKTIF           = $request->aktif;
        $inbox->INAKTIF         = $request->inaktif;
        $inbox->THAKTIF         = $request->thn_aktif;
        $inbox->THINAKTIF       = $request->thn_inaktif;
        $inbox->KETJRA          = $request->jra;
        $inbox->NILAIGUNA       = $request->nilai_guna;
        $inbox->TMPTBERKAS      = $request->tempat_berkas;
        $inbox->TK_PERKEMBANGAN = $request->perkembangan;
        $inbox->TGLTERUS        = isset($request->is_diteruskan) ? Carbon::parse($request->tgl_diteruskan)->format('Y-m-d') : null;
        $inbox->NAMAUP          = isset($request->is_diteruskan) ? $request->diteruskan_kpd : null;
        $inbox->KODEUP          = null;
        $inbox->SIFAT_SURAT     = $request->sifat_surat;
        $inbox->BALAS           = $request->tindakan;
        $inbox->TGLBALAS        = $inbox->tindakan == 'Non Balas' ? '' : Carbon::parse($request->tgl_balas)->format('Y-m-d');
        $inbox->statussurat     = isset($request->is_diteruskan) ? 'disposisi' : 'selesai';

        $inbox->NO_SISIP        = 0;
        $inbox->nodef           = ' ';
        $inbox->TDT             = ' ';

        // Header
        $inbox->poenx           = 'M' . $kode_urut . date('d') .'/' . date('m') . '/' . date('Y') . ' ' . date('H:i:s');
        $inbox->KD_WILAYAH      = Auth::user()->kode ?? 'ID3331';
        $inbox->WILAYAH         = 'PEMERINTAH KABUPATEN KARANGANYAR';
        $inbox->NAMAINSTANSI    = Auth::user()->instansi->nama_instansi ?? '-';
        $inbox->BULAN           = date('m');
        $inbox->TAHUN           = date('Y');
        $inbox->MEDIA           = 'Teks';
        
        // Operator
        $inbox->Posisi          = isset($request->is_diteruskan) ? $request->diteruskan_kpd : Auth::user()->jurusan;
        $inbox->KODEOPR         = Auth::user()->nama_lengkap;
        $inbox->JENISSURAT      = 'Masuk';
        $inbox->TGLENTRY        = date('Y-m-d');
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
            'lampiran'      => 'nullable|string|max:100',
            'is_scan'       => 'nullable|mimes:pdf,jpeg,jpg,png|max:10240',
            // 'gambar'        => 'nullable|array|max:5',
            // 'gambar.*'      => 'nullable|mimes:jpeg,jpg,png|max:3096',
            // 'lampiran'      => 'nullable|mimes:pdf|max:5126',
        ]);

        $inbox = ArsipSurat::where('NO', $request->id)->first();
        $inbox->NAMABERKAS      = $request->berkas;
        $inbox->TGLTERIMA       = Carbon::parse($request->tgl_terima)->format('Y-m-d');
        $inbox->TGLSURAT        = Carbon::parse($request->tgl_surat)->format('Y-m-d');
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
        $inbox->TGLTERUS        = isset($request->is_diteruskan) ? Carbon::parse($request->tgl_diteruskan)->format('Y-m-d') : null;
        $inbox->NAMAUP          = isset($request->is_diteruskan) ? $request->diteruskan_kpd : null;
        $inbox->KODEUP          = null;
        $inbox->SIFAT_SURAT     = $request->sifat_surat;
        $inbox->BALAS           = $request->tindakan;
        $inbox->TGLBALAS        = Carbon::parse($request->tgl_balas)->format('Y-m-d');
        $inbox->statussurat     = isset($request->is_diteruskan) ? 'disposisi' : 'selesai';

        // Header
        $inbox->KD_WILAYAH      = Auth::user()->kode ?? 'ID3331';
        // $inbox->WILAYAH         = 'PEMERINTAH KABUPATEN KARANGANYAR';
        $inbox->NAMAINSTANSI    = Auth::user()->instansi->nama_instansi ?? '-';
        // $inbox->BULAN           = date('m');
        // $inbox->TAHUN           = date('Y');
        $inbox->MEDIA           = 'Teks';
        
        // Operator
        $inbox->Posisi          = isset($request->is_diteruskan) ? $request->diteruskan_kpd : Auth::user()->jurusan;
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

    public function destroy(Request $request)
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

    public function upload_file($file, $id)
    {
        //
        return;
    }

    public function sanitize_image($file, $id)
    {
        $manager = new ImageManager(new Driver());
        // $manager = ImageManager::gd(autoOrientation: false);
        $image = $manager->read($file)
                ->orient()
                ->toJpeg(quality: 90);

        // 3. Save to temporary path
        $tempPath = storage_path('app/tmp/sanitized_' .$id. '.jpg');
        File::ensureDirectoryExists(dirname($tempPath));
        file_put_contents($tempPath, $image->toString());

        // 4. Move to public folder
        $fileName = $id . '_sanitized_' .date('YmdHis'). '.jpg';
        $folder = public_path('datas/uploads/suratmasuk');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        $path = $folder . '/' . $fileName;
        $move = File::move($tempPath, $path);

        if (!$move) {
            return false;
        }
        return $fileName;
    }

    public function sanitize_pdf($file, $id)
    {
        $sanitizer = new PdfSanitizer();
        // Store original temporarily
        $tempInput = storage_path('app/tmp/original_' .date('YmdHis'). '.pdf');
        $tempOutput = storage_path('app/tmp/sanitized_' .date('YmdHis'). '.pdf');
        $file->move(dirname($tempInput), basename($tempInput));

        // Sanitize
        $sanitizer->sanitize($tempInput, $tempOutput);

        // Store sanitized PDF (never store original)
        $folder = public_path('datas/uploads/suratmasuk');
        if (! File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }
        
        $fileName = $id . '_sanitized_' .date('YmdHis'). '.pdf';
        $path = $folder . '/' . $fileName;
        $move = File::move($tempOutput, $path);

        // Cleanup
        @unlink($tempInput);
        @unlink($tempOutput);

        if ($move) {
            return $fileName;
        } else {
            return false;
        }
    }

    public function forward(Request $request)
    {
        $request->validate([
            'uid'       => 'required|string',
            'tujuan'    => 'required|string|max:100',
            // 'catatan'   => 'nullable|string|max:255',
        ]);

        $id = json_decode(Crypt::decryptString($request->uid));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $inbox = ArsipSurat::where('NO', $id)->first();
        $inbox->Posisi      = $request->tujuan;
        if (Auth::user()->hasRole(['setda'])) {
            $inbox->pengirimsekda = Auth::user()->nama_lengkap;
        }
        if (Auth::user()->hasRole(['wabup'])) {
            $inbox->pengirimwakil = Auth::user()->nama_lengkap;
        }
        
        if ($inbox->save()) {
            return response()->json(['status' => 'success', 'message' => 'Surat berhasil diteruskan.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat gagal diteruskan.']);
        }
    }

    public function reply(Request $request)
    {
        $request->validate([
            'uid'       => 'required|string',
            'notes'     => 'required|string|max:255',
        ]);

        $id = json_decode(Crypt::decryptString($request->uid));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $user  = Auth::user();
        $inbox = ArsipSurat::where('NO', $id)->first();
        if (($user->hasRole(['setda']) && $inbox->Posisi == 'Sekretaris Daerah') || ($user->hasRole(['setda']) && !empty($inbox->DisposisiBupati)) || ($user->level == 'administrator' && $inbox->Posisi == 'Sekretaris Daerah') || ($user->level == 'administrator' && !empty($inbox->DisposisiBupati))) {
            $inbox->statussurat = 'selesai';
            $inbox->DisposisiSekda = $request->notes;
            $inbox->tglsekda1      = date('Y-m-d H:i:s');
        }
        if ($user->hasRole(['bupati']) && $inbox->Posisi == 'Bupati') {
            if (empty($inbox->pengirimsekda)) {
                $inbox->statussurat = 'selesai';
            }
            $inbox->DisposisiBupati = $request->notes;
            $inbox->tglbupati1      = date('Y-m-d H:i:s');
        }
        if ($user->hasRole(['wakil_bupati']) && $inbox->Posisi == 'Wakil Bupati') {
            $inbox->statussurat = 'selesai';
            $inbox->DisposisiWakil = $request->notes;
            $inbox->tglwakil     = date('Y-m-d H:i:s');
        }

        if ($inbox->save()) {
            return response()->json(['status' => 'success', 'message' => 'Surat berhasil ditanggapi.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat gagal ditanggapi.']);
        }
    }

    public function view_pdf($uid)
    {
        $folder = public_path('datas/uploads/suratmasuk');
        $id  = Crypt::decryptString($uid);
        if (!$id) return abort(404);

        $surat = ArsipSurat::where('NO', $id)->first();
        if (!$surat) return abort(404);

        if (empty($surat->pdf)) {
            $pdf = $this->save_pdf($surat);
            if (!$pdf) return abort(404);
        } else {
            $pdf = $surat->pdf;
        }
        // return $pdf->stream('surat_masuk.pdf');
        return response()->file($folder. '/' . $pdf, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function download_pdf(Request $request)
    {
        $pdf = $this->build_pdf($request->inbox);
        return $pdf->download('surat_masuk.pdf');
    }

    public function save_pdf($inbox)
    {
        $folder = public_path('datas/uploads/suratmasuk');
        $uid = Str::uuid();
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true, true);
        }

        $surat = $this->build_pdf($inbox);
        if (!$surat) return false;
        $suratName = $uid . '_surat.pdf';
        $saveSurat = $surat->save($folder . '/' . $suratName);
        if (!$saveSurat) return false;

        $kartu = $this->build_kartu($inbox);
        if (!$kartu) return false;
        $kartuName = $uid . '_kartu.pdf';
        $saveKartu = $surat->save($folder . '/' . $kartuName);
        if (!$saveKartu) return false;

        // merge pdf
        $datas = [$suratName, $kartuName];
        $pdf = new Fpdi();
        foreach ($datas as $key => $value) {
            $pageCount = $pdf->setSourceFile($folder .'/'. $value);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $pdf->useTemplate($templateId);
            }
        }

        try {
            $fileName = $uid . '.pdf';
            $pdf->Output($folder . '/'. $fileName, 'F');
            Log::info('success merged');
            foreach ($datas as $file) {
                unlink($folder .'/'. $file);
            }
            ArsipSurat::where('NO', $inbox->NO)->update(['pdf' => $fileName]);
            return $fileName;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function build_pdf($inbox)
    {
        if ($inbox->Posisi == 'Bupati') {
            $template = 'main.inbox.templates.bupati_full';
            // $template = 'main.inbox.templates.wabup_full';
            // $template = 'main.inbox.templates.setda_full';
            // $template = 'main.inbox.templates.umum_full';
            // $template = 'main.inbox.template';
        } elseif ($inbox->Posisi == 'Wakil Bupati') {
            $template = 'main.inbox.templates.wabup_full';
        } elseif ($inbox->Posisi == 'Sekretaris Daerah') {
            $template = 'main.inbox.templates.setda_full';
        } else {
            $template = 'main.inbox.templates.umum_full';
        }
        // $pdf = Pdf::setPaper('letter', 'portrait');
        // $pdf = Pdf::setPaper([0, 0, 792, 612], 'portrait');
        $sign = Pimpinan::where('level', $inbox->Posisi)->where('is_default', true)->first();

        $pdf = Pdf::loadView($template, ['data' => $inbox, 'sign' => $sign]);
        return $pdf;
    }

    public function build_kartu($inbox)
    {
        $sign = Pimpinan::where('level', $inbox->Posisi)->where('is_default', true)->first();

        $pdf = Pdf::loadView('main.inbox.templates.kartu', ['data' => $inbox, 'sign' => $sign]);
        return $pdf;
    }
}
