<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\DaftarTerusan;
use App\Models\Disposisi;
use App\Models\Inbox;
use App\Models\Jra;
use App\Models\Klasifikasi;
use App\Models\LevelUser;
use App\Models\Perkembangan;
use App\Models\Pimpinan;
use App\Models\SifatSurat;
use App\Models\TempatBerkas;
use App\Models\User;
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
        $this->middleware('permission:input surat masuk', ['only' => ['store', 'create', 'nomor_urut', 'upload_file']]);
        $this->middleware('permission:edit surat masuk', ['only' => ['edit', 'update', 'upload_file']]);
        $this->middleware('permission:hapus surat masuk', ['only' => ['destroy']]);
        $this->middleware('permission:cetak surat masuk', ['only' => ['view_pdf']]);
    }

    public function index()
    {
        $list = json_decode(Auth::user()->leveluser->daftar_terusan);
        $user  = Auth::user();
        $akses = LevelUser::find($user->level);
        $data  = [
            'posisi'    => $akses->related_level_user,
            'sifat'     => SifatSurat::all(),
            'years'     => Inbox::select('year')->distinct()->orderBy('year', 'desc')->get(),
            'terusan'   => (!empty($list) && is_array($list)) ? LevelUser::whereIn('id', $list)->get() : [],
        ];

        return view('main.inbox.index', $data);
    }

    public function serverside()
    {
        $request = Request();
        $user   = Auth::user();
        $start  = $request->start;
        $length = $request->length;
        $level  = LevelUser::where('id', Auth::user()->level)->first();
        $akses  = $level->akses;
        $query  = Inbox::with(['disposisi.penerima.leveluser', 'mydisposisi', 'getdisposisi', 'klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'posisi:id,uuid,nama_lengkap,level', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap']);

        if ($request->has('klasifikasi') && $request->klasifikasi != '') {
            $query->where('sifat_surat', $request->klasifikasi);
        }
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('year', $request->tahun);
        }
        if ($request->has('posisi') && $request->posisi != '' && in_array($request->posisi, $akses)) {
            if ($user->leveluser->role != 'admin' || $user->leveluser->role != 'administrator') {
                // $query->where('level_surat', $request->posisi);
                $query->whereHas('posisi', function ($q) use ($request) {
                    $q->where('level', $request->posisi);
                });
            }
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status_surat', $request->status);
        }

        $query->whereNull('on_delete');
        $query->where(function ($static) use ($level) {
            $static->whereIn('level_surat', $level->akses)
                    ->orWhereIn('posisi_level', $level->akses);
        });
        // $query->whereIn('level_surat', $level->akses);
        // $query->orWhereIn('posisi_level', $level->akses);
        $totalData = $query->count();

        // search query
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('no_surat', 'like', "%$search%")
                    ->orWhere('dari', 'like', "%$search%")
                    ->orWhere('perihal', 'like', "%$search%")
                    ->orWhere('isi_surat', 'like', "%$search%");
            });
        }

        $totalFiltered = $query->count();
        $query->orderBy('created_at', 'desc');
        $query->skip($start)->take($length);
        
        $inbox = $query->get();
        // return $inbox[0];
        // Log::info(json_encode($inbox));

        $data = [];
        foreach ($inbox as $r => $ibx) {
            $ibx->cryptfile = !empty($ibx->softcopy) ? Crypt::encryptString($ibx->softcopy) : null;
            $data[] = [
                'nomor'     => $ibx->no_surat,
                'no_agenda' => $ibx->no_agenda,
                'klasifikasi' => $ibx->sifat->nama_sifat ?? '',
                'berkas'    => $ibx->berkas->nama ?? '',
                'wilayah'   => $ibx->wilayah,
                'isi_surat' => $ibx->isi_surat,
                'tanggal'   => $ibx->tgl_surat,
                'kepada'    => $ibx->dari,
                'perihal'   => $ibx->perihal,
                'kode'      => $ibx->klasifikasi->klas3 ?? '',
                'tgl_buat'  => $ibx->tgl_surat,
                'posisi'    => $ibx->posisi->leveluser->nama ?? '',
                'class'     => $ibx->posisi->leveluser->warna ?? '',
                'uid'       => Crypt::encryptString($ibx->id),
                'option'    => '<div class="btn-group-vertical" role="group" aria-label="Second group">'.
                                    // Detail Surat
                                    ('<button type="button" class="btn btn-info bs-tooltip" title="Detail Surat" onclick="viewSurat(`'. base64_encode(json_encode($ibx)) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    </button>') .

                                    // Edit Surat
                                    ((Auth::user()->can('edit surat masuk')) ? '<a href="'. route('inbox.edit', Crypt::encryptString($ibx->uuid)) .'" type="button" class="btn btn-outline-warning bs-tooltip" title="Edit Surat">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </a>' : '') .

                                    // Tindak Lanjut
                                    ((Auth::user()->hasRole(['administrator'])) ? (($ibx->status_surat == 'diproses') ? '<button type="button" class="btn btn-outline-primary bs-tooltip" title="Tindak Lanjut (Teruskan)" onclick="followUp(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                    </button>' : '<button type="button" class="btn btn-outline-primary bs-tooltip" title="Batalkan Tindak Lanjut" onclick="cancelFollowUp(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                    </button>') : '') .
                                    (((Auth::user()->leveluser->tindak_lanjut) && ($ibx->status_surat == 'diproses') && ($ibx->posisi_level == Auth::user()->level) && (!Auth::user()->hasRole(['administrator']))) ? '<button type="button" class="btn btn-outline-primary bs-tooltip" title="Tindak Lanjut (Teruskan)" onclick="followUp(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                    </button>' : '') .

                                    // Selesai Tindak Lanjut
                                    (($ibx->status_surat == 'selesai') ? '<button type="button" class="btn btn-outline-success bs-tooltip" title="Surat sudah ditindak lanjuti">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    </button>' : '') .

                                    // Menunggu Tindakan
                                    // ((Auth::user()->hasRole(['umum']) && $ibx->statussurat == 'diproses') ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                    //     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    // </button>' : '') .
                                    // ((Auth::user()->hasRole(['setda']) && $ibx->Posisi == 'Bupati' && empty($ibx->tglbupati1) && !empty($ibx->pengirimsekda) && $ibx->statussurat == 'disposisi') ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                    //     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    // </button>' : '') .
                                    // ((Auth::user()->hasRole(['bupati']) && $ibx->Posisi == 'Bupati' && !empty($ibx->tglbupati1) && !empty($ibx->pengirimsekda) && empty($ibx->tglsekda1) && $ibx->statussurat == 'disposisi') ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                    //     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    // </button>' : '') .
                                    ((Auth::user()->leveluser->tindak_lanjut && $ibx->status_surat == 'diproses' && !empty($ibx->mydisposisi) && $ibx->mydisposisi->is_completed == false) ? '<button type="button" class="btn btn-outline-secondary bs-tooltip" title="Menunggu Tindakan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
                                    </button>' : '') .

                                    // Cetak Surat
                                    ((!Auth::user()->hasRole(['administrator', 'admin']) && $ibx->status_surat == 'selesai') ? '<button type="button" class="btn btn-outline-info bs-tooltip" title="Cetak Disposisi" onclick="printPdf(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    </button>' : '') .
                                    ((Auth::user()->hasRole(['administrator', 'admin']) && $ibx->status_surat == 'selesai') ? '<div class="btn-group" role="group">
                                        <button id="btndefault" type="button" class="btn btn-outline-info bs-tooltip dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Cetak">
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btndefault">
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="printPdf(`'. Crypt::encryptString($ibx->uuid) .'`)"><i class="flaticon-home-fill-1 mr-1"></i>Disposisi</a>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="printKartu(`'. Crypt::encryptString($ibx->uuid) .'`)"><i class="flaticon-gear-fill mr-1"></i>Kartu Surat Masuk</a>
                                        </div>
                                    </div>' : '') .

                                    // Hapus Surat
                                    ((Auth::user()->can('hapus surat masuk')) ? '<button type="button" class="btn btn-danger bs-tooltip" title="Hapus Surat" onclick="_delete(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>' : '') .

                                    // Tanggapi Surat
                                    ((($ibx->status_surat == 'diproses' && isset($ibx->mydisposisi->is_completed) && $ibx->mydisposisi->is_completed)) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Disposisi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    </button>' : '') .
                                    (($ibx->status_surat == 'diproses' && !isset($ibx->mydisposisi) && !empty($ibx->getdisposisi->id) && !$ibx->getdisposisi->is_completed) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Disposisi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->uuid) .'`)">
                                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    </button>' : '') .

                                    // ((Auth::user()->hasRole(['administrator']) && $ibx->status_surat == 'diproses') ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                    //     <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    // </button>' : '') .
                                    // ((Auth::user()->hasRole(['setda']) && $ibx->status_surat == 'diproses' && (($ibx->Posisi == 'Bupati' && !empty($ibx->tglbupati1) && !empty($ibx->pengirimsekda)) || $ibx->Posisi == 'Sekretaris Daerah')) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                    //     <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    // </button>' : '') .
                                    // ((Auth::user()->hasRole(['wabup']) && $ibx->status_surat == 'diproses' && empty($ibx->wakil)) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                    // <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    // </button>' : '') .
                                    // ((Auth::user()->hasRole(['bupati']) && $ibx->status_surat == 'diproses' && empty($ibx->tglbupati1)) ? '<button type="button" class="btn btn-secondary bs-tooltip" title="Tanggapi Surat" onclick="_reply(`'. Crypt::encryptString($ibx->NO) .'`)">
                                    // <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                    // </button>' : '') .

                                '</div>',
            ];
        }

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function create()
    {
        $list = json_decode(Auth::user()->leveluser->daftar_terusan);
        $data = [
            'jra'       => Klasifikasi::all(),
            'berkas'    => TempatBerkas::all(),
            'sifat'     => SifatSurat::all(),
            'perkembangan' => Perkembangan::all(),
            // 'level'     => LevelUser::whereNotIn('role', ['administrator'])->get(),
            'level'     => (!empty($list) && is_array($list)) ? LevelUser::whereIn('id', $list)->get() : [],
        ];
        return view('main.inbox.new', $data);
    }

    public function edit($id)
    {
        $no    = Crypt::decryptString($id);
        if (!$no) return abort(404);
        $inbox = Inbox::with(['disposisi', 'disposisi.penerima.leveluser', 'mydisposisi', 'klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'posisi:id,uuid,nama_lengkap,level', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap'])->where('uuid', $no)->first();
        if ($inbox) {
            $inbox->cryptfile = Crypt::encryptString($inbox->softcopy);
        }
        $data  = [
            'inbox'     => $inbox,
            'jra'       => Klasifikasi::all(),
            'berkas'    => TempatBerkas::all(),
            'sifat'     => SifatSurat::all(),
            'perkembangan' => Perkembangan::all(),
        ];

        return view('main.inbox.edit', $data);
    }

    public function show($id)
    {
        $no    = json_decode(Crypt::decryptString($id));
        $inbox = ArsipSurat::where('id', $no)->first();
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

        if (Auth::user()->leveluser->is_primary) {
            $last = Inbox::where('is_primary_agenda', true)->orderBy('id', 'desc')->first();
        } else {
            $last = Inbox::where('level_surat', Auth::user()->level)->orderBy('id', 'desc')->first();
        }
        $kode_urut = ($last->year == date('Y') ? intval($last->no_agenda) + 1 : 1);

        return response()->json(['status' => 'success', 'urut' => $kode_urut]);
    }

    public function get_jra(Request $request)
    {
        $request->validate([
            'kode'  => 'required|string|max:10',
            'name'  => 'required|string|max:100',
        ]);

        $jra = Klasifikasi::where('klas3', $request->kode)->where('masalah3', $request->name)->first();

        if ($jra) {
            $jra->thn_aktif = date('Y') + intval($jra->r_aktif);
            $jra->thn_inaktif = date('Y') + intval($jra->r_aktif) + intval($jra->r_inaktif);
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
            'lampiran'      => 'nullable|string|max:10',
            'is_scan'       => 'nullable|mimes:pdf,jpeg,jpg,png|max:10240',
            'keterangan'    => 'nullable|string|max:500'
        ]);

        // nomor agenda tahunan
        if (Auth::user()->leveluser->is_primary) {
            $last = Inbox::where('is_primary_agenda', true)->orderBy('id', 'desc')->first();
            $pr   = true;
        } else {
            $last = Inbox::where('level_surat', Auth::user()->level)->orderBy('id', 'desc')->first();
            $pr   = false;
        }
        $kode_urut = ($last->year == date('Y') ? intval($last->no_agenda) + 1 : 1);

        $uuid = Str::uuid7();
        $klas = Klasifikasi::where('klas3', $request->klasifikasi_kode)->first();
        if (!empty($request->file('is_scan'))) {
            Log::info('file exists');
            $file = $this->upload_file($request->file('is_scan'), $uuid);
        } else {
            $file = null;
        }

        $inbox = new Inbox();
        $inbox->uuid            = $uuid;
        $inbox->nama_berkas     = $request->berkas;
        $inbox->tgl_diterima    = Carbon::parse($request->tgl_terima)->format('Y-m-d');
        $inbox->tgl_surat       = Carbon::parse($request->tgl_surat)->format('Y-m-d');
        $inbox->dari            = $request->darikepada;
        $inbox->wilayah         = $request->wilayah;
        $inbox->perihal         = $request->perihal;
        $inbox->isi_surat       = $request->isi;
        $inbox->year            = date('Y');
        $inbox->id_klasifikasi  = $klas->id ?? null;
        $inbox->no_agenda       = $kode_urut;
        $inbox->no_surat        = $request->no_surat;
        $inbox->tempat_berkas   = $request->tempat_berkas;
        $inbox->id_perkembangan = $request->perkembangan;
        $inbox->sifat_surat     = $request->sifat_surat;
        $inbox->tindakan        = $request->tindakan;
        $inbox->id_media        = 1;
        $inbox->tgl_balas       = $request->tindakan == 'non balas' ? null : Carbon::parse($request->tgl_balas)->format('Y-m-d');
        $inbox->status_surat    = isset($request->is_diteruskan) ? 'diproses' : 'selesai';
        $inbox->softcopy        = $file;
        
        if (Auth::user()->leveluser->tindak_lanjut == false) {
            $inbox->keterangan  = $request->keterangan ?? null;
        }
        
        // Operator
        $inbox->is_primary_agenda = $pr;
        $inbox->created_by      = Auth::user()->uuid;
        $inbox->level_surat     = Auth::user()->leveluser->id;
        $inbox->posisi_surat    = Auth::user()->uuid;
        $inbox->posisi_level    = Auth::user()->leveluser->id;

        $save = $inbox->save();

        if ($save) {
            if (isset($request->is_diteruskan) && $request->is_diteruskan == 'yes' && isset($request->diteruskan_kpd) && !empty($request->diteruskan_kpd)) {
                $rcv   = User::where('level', $request->diteruskan_kpd)->first();
                $surat = Inbox::where('uuid', $uuid)->first();
                if ($rcv) {
                    $dispo = new Disposisi();
                    $dispo->uid_disposisi   = Str::uuid7();
                    $dispo->uid_surat       = $uuid;
                    $dispo->pengirim_uuid   = Auth::user()->uuid;
                    $dispo->penerima_uuid   = $rcv->uuid;
                    $dispo->is_completed    = false;
                    $dispo->save();

                    //update surat
                    $surat->posisi_surat    = $rcv->uuid;
                    $surat->posisi_level    = $request->diteruskan_kpd;
                    $surat->save();
                }
            }
            return redirect()->route('inbox')->with('success', 'Surat masuk berhasil disimpan.');
        } else {
            return redirect()->route('inbox')->with('error', 'Surat masuk gagal disimpan.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([            
            'uid'           => 'required|string|max:40',
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
            'lampiran'      => 'nullable|string|max:10',
            'is_scan'       => 'nullable|mimes:pdf,jpeg,jpg,png|max:10240',
        ]);

        $inbox = Inbox::where('uuid', $request->uid)->first();

        if (!empty($request->file('is_scan'))) {
            $folder = public_path('datas/uploads/suratmasuk/');
            @unlink($folder . $inbox->softcopy);
            $file = $this->upload_file($request->file('is_scan'), $request->uuid);
        } else {
            $file = $inbox->softcopy;
        }

        $inbox->nama_berkas     = $request->berkas;
        $inbox->tgl_diterima    = Carbon::parse($request->tgl_terima)->format('Y-m-d');
        $inbox->tgl_surat       = Carbon::parse($request->tgl_surat)->format('Y-m-d');
        $inbox->dari            = $request->darikepada;
        $inbox->wilayah         = $request->wilayah;
        $inbox->perihal         = $request->perihal;
        $inbox->isi_surat       = $request->isi;
        $inbox->id_klasifikasi  = $request->klasifikasi_kode;
        $inbox->no_surat        = $request->no_surat;
        $inbox->tempat_berkas   = $request->tempat_berkas;
        $inbox->id_perkembangan = $request->perkembangan;
        $inbox->sifat_surat     = $request->sifat_surat;
        $inbox->tindakan        = $request->tindakan;
        $inbox->id_media        = 1;
        $inbox->tgl_balas       = $inbox->tindakan == 'Non Balas' ? '' : Carbon::parse($request->tgl_balas)->format('Y-m-d');
        $inbox->softcopy        = $file;

        // Operator
        // $inbox->created_by      = Auth::user()->uuid;
        $inbox->level_surat     = Auth::user()->leveluser->id;

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

        $id = (Crypt::decryptString($request->uid));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $inbox = Inbox::where('uuid', $id)->delete();
        if ($inbox) {
            return response()->json(['status' => 'success', 'message' => 'Surat masuk berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk gagal dihapus.']);
        }
    }

    public function upload_file($file, $id)
    {
        $image = ['jpg', 'jpeg', 'png'];
        $extension = $file->extension();
        
        if (in_array(strtolower($extension), $image)) {
            Log::info('file is image');
            return $this->sanitize_image($file, $id);
        } else if (strtolower($extension) == 'pdf') {
            Log::info('file pdf');
            return $this->sanitize_pdf($file, $id);
        } else {
            return null;
        }
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
            return null;
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
            return null;
        }
    }

    public function forward(Request $request)
    {
        $request->validate([
            'uid'       => 'required|string',
            'tujuan'    => 'required|numeric',
            // 'catatan'   => 'nullable|string|max:255',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $dest = LevelUser::find($request->tujuan);
        if (!$dest) {
            return response()->json(['status' => 'failed', 'message' => 'ID tujuan tidak diketahui.']);
        }

        $inbox = Inbox::where('uuid', $id)->first();
        if (!$inbox) {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk tidak diketahui.']);
        }

        $penerima = User::where('level', $request->tujuan)->first();
        $check = Disposisi::where('uid_surat', $inbox->uuid)->where('pengirim_uuid', Auth::user()->uuid)->get();
        if (count($check) > 0) return response()->json(['status' => 'failed', 'message' => 'Duplikasi tindak lanjut.']);

        $dispo = new Disposisi();
        $dispo->uid_disposisi   = Str::uuid7();
        $dispo->uid_surat       = $inbox->uuid;
        $dispo->pengirim_uuid   = Auth::user()->uuid;
        $dispo->penerima_uuid   = $penerima->uuid;
        $dispo->is_completed    = false;
        $save = $dispo->save();

        if ($save) {
            $inbox->posisi_level = $request->tujuan;
            $inbox->posisi_surat = $penerima->uuid;
            $inbox->save();

            return response()->json(['status' => 'success', 'message' => 'Surat berhasil diteruskan.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat gagal diteruskan.']);
        }
        
        // if ($inbox->save()) {
        //     return response()->json(['status' => 'success', 'message' => 'Surat berhasil diteruskan.']);
        // } else {
        //     return response()->json(['status' => 'failed', 'message' => 'Surat gagal diteruskan.']);
        // }
    }

    public function reply(Request $request)
    {
        $request->validate([
            'uid'       => 'required|string',
            'notes'     => 'required|string|max:255',
        ]);

        $id = (Crypt::decryptString($request->uid));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $user  = Auth::user();
        $dispo = Disposisi::where('uid_surat', $id)->where('penerima_uuid', $user->uuid)->where('is_completed', false)->whereNull('on_delete')->first();
        if (!$dispo) return response()->json(['status' => 'failed', 'message' => 'ID disposisi tidak diketahui.']);

        $check = Disposisi::where('uid_surat', $id)->where('pengirim_uuid', $user->uuid)->where('is_completed', false)->exists();
        if ($check) return response()->json(['status' => 'failed', 'message' => 'Anda belum bisa memberikan disposisi karena pihak yang Anda teruskan belum memberikan tanggapan.']);

        $pimpinan = Pimpinan::where('level', $user->level)->where('is_default', true)->first();
        if (!$pimpinan) return response()->json(['status' => 'failed', 'message' => 'Anda belum membuat/memilih data pimpinan Anda.']);
        
        $dispo->catatan_disposisi   = $request->notes;
        $dispo->id_pimpinan         = $pimpinan->id;
        $dispo->is_completed        = true;

        if ($dispo->save()) {
            $this->check_surat_selesai($id);
            return response()->json(['status' => 'success', 'message' => 'Surat berhasil ditanggapi.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat gagal ditanggapi.']);
        }
    }

    public function check_surat_selesai($uid)
    {
        $inbox = Inbox::where('uuid', $uid)->first();
        $dispo = Disposisi::where('uid_surat', $uid)->where('is_completed', false)->exists();

        if (!$dispo) {
            $inbox->status_surat = 'selesai';
            $inbox->save();
        }
    }

    public function view_pdf($uid)
    {
        $folder = public_path('datas/uploads/suratmasuk');
        $request = Request();
        $id  = Crypt::decryptString($uid);
        if (!$id) return abort(404);

        $surat = Inbox::with(['disposisi.penerima.leveluser', 'mydisposisi', 'getdisposisi', 'klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'posisi:id,uuid,nama_lengkap,level', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap'])
                ->where('uuid', $id)->first();
        
        if (!$surat) return abort(404);

        if (empty($request->type)) return abort(404);

        // $add = (isset($request->type) && !empty($request->type) && $request->type == 'textonly') ? ('_' . $request->type) : null;
        // if (empty($surat->pdf)) {
        //     $pdf = $this->save_pdf($surat, $add);
        //     if (!$pdf) return abort(404);
        // } elseif (!file_exists($folder . '/' . $surat->pdf)) {
        //     $pdf = $this->save_pdf($surat, $add);
        //     if (!$pdf) return abort(404);
        // } else {
        //     $pdf = $surat->pdf;
        // }

        // return response()->file($folder. '/' . $pdf, [
        //     'Content-Type' => 'application/pdf',
        // ]);

        if ($request->type == 'disposisi') {
            $pdf = $this->build_pdf($surat, null);
        } elseif ($request->type == 'kartu') {
            if (!Auth::user()->hasRole(['administrator', 'admin'])) return abort(404);
            // $pdf = $this->build_kartu($surat, '_textonly');
            $pdf = $this->build_kartu($surat, null);
        } else {
            return abort(404);
        }

        return $pdf->stream($surat->no_agenda . '_' . $surat->year . '_' . ($request->type == 'kartu' ? 'kartu_surat_masuk' : $request->type) .'.pdf');
    }

    public function download_pdf(Request $request)
    {
        $pdf = $this->build_pdf($request->inbox);
        return $pdf->download('surat_masuk.pdf');
    }

    public function save_pdf($inbox, $add = null)
    {
        $folder = public_path('datas/uploads/suratmasuk');
        $uid = Str::uuid();
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true, true);
        }

        $surat = $this->build_pdf($inbox, $add);
        if (!$surat) return false;
        $suratName = $uid . $add . '_surat.pdf';
        $saveSurat = $surat->save($folder . '/' . $suratName);
        if (!$saveSurat) return false;

        $kartu = $this->build_kartu($inbox, $add);
        if (!$kartu) return false;
        $kartuName = $uid. $add . '_kartu.pdf';
        $saveKartu = $kartu->save($folder . '/' . $kartuName);
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
            $fileName = $uid . $add . '.pdf';
            $pdf->Output($folder . '/'. $fileName, 'F');
            Log::info('success merged');
            foreach ($datas as $file) {
                unlink($folder .'/'. $file);
            }
            if (empty($add)) ArsipSurat::where('NO', $inbox->NO)->update(['pdf' => $fileName]);
            return $fileName;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function build_pdf($inbox, $add = null)
    {
        // return dd($inbox);
        if ($inbox->posisi->level == 8) { 
            // bupati
            $template = 'main.inbox.templates.bupati_full';
            // $template = 'main.inbox.templates.wabup_full';
            // $template = 'main.inbox.templates.setda_full';
            // $template = 'main.inbox.templates.umum_full';
            // $template = 'main.inbox.template';
        } elseif ($inbox->posisi->level == 7) {
            // wabup
            $template = 'main.inbox.templates.wabup_full';
        } elseif ($inbox->posisi->level == 3) {
            // setda
            $template = 'main.inbox.templates.setda_full';
        } else {
            $template = 'main.inbox.templates.umum_full';
        }
        // $pdf = Pdf::setPaper('letter', 'portrait');
        // $pdf = Pdf::setPaper([0, 0, 792, 612], 'portrait');
        $sign = Pimpinan::where('level', $inbox->posisi_level)->where('is_default', true)->first();
        $terusan = DaftarTerusan::all();

        $pdf = Pdf::loadView($template . $add, ['data' => $inbox, 'sign' => $sign, 'terusan' => $terusan]);
        return $pdf;
    }

    public function build_kartu($inbox, $add = null)
    {
        $sign = Pimpinan::where('level', $inbox->posisi_level)->where('is_default', true)->first();
        if ($inbox->no_agenda < 9) {
            $inbox->no_agenda = '000' . $inbox->no_agenda;
        } elseif ($inbox->no_agenda > 9 && $inbox->no_agenda < 99) {
            $inbox->no_agenda = '00' . $inbox->no_agenda;
        } elseif ($inbox->no_agenda > 99 && $inbox->no_agenda < 999) {
            $inbox->no_agenda = '0' . $inbox->no_agenda;
        }

        $pdf = Pdf::loadView('main.inbox.templates.kartu' . $add, ['data' => $inbox, 'sign' => $sign]);
        return $pdf;
    }

    public function view_file($uid)
    {
        $file = Crypt::decryptString($uid);
        if (!$file) return abort(404);

        $folder = public_path('datas/uploads/suratmasuk');
        if (file_exists($folder . '/' . $file)) {
            return response()->file($folder . '/' . $file);
        } else {
            return abort(404);
        }
    }
}
