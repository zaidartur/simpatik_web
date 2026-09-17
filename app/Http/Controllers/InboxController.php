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
use App\Http\Requests\InboxStoreRequest;
use App\Http\Requests\InboxUpdateRequest;
use App\Services\FileUploadService;
use App\Services\NomorAgendaService;
use App\Services\PdfSanitizer;
use App\Services\SuratMasukService;
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
    protected SuratMasukService $suratMasukService;
    protected NomorAgendaService $agendaService;
    protected FileUploadService $fileService;

    public function __construct(
        SuratMasukService $suratMasukService,
        NomorAgendaService $agendaService,
        FileUploadService $fileService
    ) {
        $this->suratMasukService = $suratMasukService;
        $this->agendaService = $agendaService;
        $this->fileService = $fileService;

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

    /**
     * Display the specified surat masuk details and disposisi timeline.
     */
    public function show($id)
    {
        try {
            $uuid = Crypt::decryptString($id);
        } catch (\Exception $e) {
            $uuid = $id;
        }

        $inbox = Inbox::with([
            'disposisi.pengirim.leveluser',
            'disposisi.penerima.leveluser',
            'disposisi.pimpinan',
            'klasifikasi',
            'media',
            'sifat',
            'berkas',
            'perkembangan',
            'posisi.leveluser',
            'level',
            'creator'
        ])->where('uuid', $uuid)->first();

        if (!$inbox) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['status' => 'failed', 'message' => 'Surat tidak ditemukan.'], 404);
            }
            return abort(404);
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status'   => 'success',
                'inbox'    => $inbox,
                'timeline' => view('components.disposisi-timeline', ['inbox' => $inbox])->render(),
            ]);
        }

        return view('main.inbox.show', compact('inbox'));
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
                'nomor'     => e($ibx->no_surat),
                'no_agenda' => e($ibx->no_agenda),
                'klasifikasi' => e($ibx->sifat->nama_sifat ?? ''),
                'berkas'    => e($ibx->berkas->nama ?? ''),
                'wilayah'   => e($ibx->wilayah),
                'isi_surat' => e($ibx->isi_surat),
                'tanggal'   => e($ibx->tgl_surat),
                'kepada'    => e($ibx->dari),
                'perihal'   => e($ibx->perihal),
                'kode'      => e($ibx->klasifikasi->klas3 ?? ''),
                'tgl_buat'  => e($ibx->tgl_surat),
                'posisi'    => e($ibx->posisi->leveluser->nama ?? ''),
                'class'     => e($ibx->posisi->leveluser->warna ?? ''),
                'uid'       => Crypt::encryptString($ibx->id),
                'option'    => view('components.inbox-action-buttons', ['ibx' => $ibx])->render(),
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

    public function nomor_urut(Request $request)
    {
        $request->validate([
            'type'  => 'required|string|max:10',
        ]);

        $isPrimary = (bool) (Auth::user()->leveluser->is_primary ?? false);
        $kode_urut = $this->agendaService->preview('inbox', $isPrimary, Auth::user()->level, intval(date('Y')));

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

    public function store(InboxStoreRequest $request)
    {
        try {
            $this->suratMasukService->store($request->validated(), $request->file('is_scan'), Auth::user());
            return redirect()->route('inbox')->with('success', 'Surat masuk berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan surat masuk: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('inbox')->with('error', 'Surat masuk gagal disimpan: ' . $e->getMessage());
        }
    }

    public function update(InboxUpdateRequest $request)
    {
        $inbox = Inbox::where('uuid', $request->uid)->first();
        if (!$inbox) {
            return redirect()->route('inbox')->with('error', 'Surat masuk tidak ditemukan.');
        }

        try {
            $this->suratMasukService->update($inbox, $request->validated(), $request->file('is_scan'), Auth::user());
            return redirect()->route('inbox')->with('success', 'Surat masuk berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui surat masuk: ' . $e->getMessage());
            return redirect()->route('inbox')->with('error', 'Surat masuk gagal diperbarui.');
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'uid' => 'required|string'
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $inbox = Inbox::where('uuid', $id)->first();
        if (!$inbox) {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk tidak ditemukan.']);
        }

        $drop = $this->suratMasukService->destroy($inbox);
        if ($drop) {
            return response()->json(['status' => 'success', 'message' => 'Surat masuk berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk gagal dihapus.']);
        }
    }

    public function upload_file($file, $id)
    {
        return $this->fileService->upload($file, $id, 'suratmasuk');
    }

    public function forward(Request $request)
    {
        $request->validate([
            'uid'    => 'required|string',
            'tujuan' => 'required|numeric',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $inbox = Inbox::where('uuid', $id)->first();
        if (!$inbox) {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk tidak diketahui.']);
        }

        $res = $this->suratMasukService->forward($inbox, intval($request->tujuan), Auth::user());
        return response()->json($res);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'uid'   => 'required|string',
            'notes' => 'required|string|max:255',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID Surat tidak diketahui.']);
        }

        $inbox = Inbox::where('uuid', $id)->first();
        if (!$inbox) {
            return response()->json(['status' => 'failed', 'message' => 'Surat masuk tidak ditemukan.']);
        }

        $res = $this->suratMasukService->reply($inbox, $request->notes, Auth::user());
        return response()->json($res);
    }

    public function check_surat_selesai($uid)
    {
        $this->suratMasukService->checkSuratSelesai($uid);
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

        $safeFile = basename($file);
        if ($safeFile !== $file) return abort(404);

        $path = $this->fileService->resolveFilePath($safeFile, 'suratmasuk');
        if (!$path || !file_exists($path)) {
            return abort(404);
        }

        return response()->file($path);
    }
}
