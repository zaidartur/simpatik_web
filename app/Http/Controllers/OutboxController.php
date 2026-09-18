<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use App\Models\DataUnit;
use App\Models\Duplikat;
use App\Models\Instansi;
use App\Models\Jra;
use App\Models\Klasifikasi;
use App\Models\LevelUser;
use App\Models\Outbox;
use App\Models\Perkembangan;
use App\Models\Pimpinan;
use App\Models\SifatSurat;
use App\Models\Sppd;
use App\Models\TempatBerkas;
use App\Models\UnitKerja;
use App\Http\Requests\OutboxStoreRequest;
use App\Http\Requests\OutboxUpdateRequest;
use App\Services\FileUploadService;
use App\Services\NomorAgendaService;
use App\Services\PdfSanitizer;
use App\Services\SuratKeluarService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use setasign\Fpdi\Fpdi;

class OutboxController extends Controller
{
    protected SuratKeluarService $suratKeluarService;
    protected NomorAgendaService $agendaService;
    protected FileUploadService $fileService;
    protected \App\Services\ReferenceCacheService $cacheService;

    public function __construct(
        SuratKeluarService $suratKeluarService,
        NomorAgendaService $agendaService,
        FileUploadService $fileService,
        \App\Services\ReferenceCacheService $cacheService
    ) {
        $this->suratKeluarService = $suratKeluarService;
        $this->agendaService = $agendaService;
        $this->fileService = $fileService;
        $this->cacheService = $cacheService;

        $this->middleware('permission:surat keluar', ['only' => ['index', 'serverside', 'show', 'duplicate', 'view_file']]);
        $this->middleware('permission:input surat keluar', ['only' => ['store', 'create', 'last_sppd', 'check_surat']]);
        $this->middleware('permission:edit surat keluar', ['only' => ['edit', 'update', 'duplikat']]);
        $this->middleware('permission:hapus surat keluar', ['only' => ['destroy']]);
        $this->middleware('permission:cetak surat keluar', ['only' => ['view_pdf']]);
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
        $user   = Auth::user();
        $start = $request->start;
        $length = $request->length;
        $level  = LevelUser::where('id', Auth::user()->level)->first();
        $akses  = $level->akses;
        $query = Outbox::with(['klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap', 'spd', 'pengolah']);
        
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
        $query->where(function ($q) use ($level, $user) {
            $q->whereIn('level_surat', $level->akses ?? [])
              ->orWhere('created_by', $user->uuid);
        });
        $totalData = $query->count();

        // search query
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('no_surat', 'like', "%$search%")
                    ->orWhere('kepada', 'like', "%$search%")
                    ->orWhere('perihal', 'like', "%$search%")
                    ->orWhere('isi_surat', 'like', "%$search%")
                    ->orWhere('wilayah', 'like', "%$search%");
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
        $query->orderBy('created_at', 'desc');
        // $query->offset($start)->limit($length);
        $query->skip($start)->take($length);
        
        $outbox = $query->get();

        // manipulate fields data
        $data = [];
        foreach ($outbox as $r => $ibx) {
            $ibx->cryptfile = !empty($ibx->softcopy) ? Crypt::encryptString($ibx->softcopy) : null;
            $data[] = [
                // 'NO' => $ibx->NO,
                'nomor'     => e($ibx->no_surat),
                'no_agenda' => e($ibx->no_agenda),
                'klasifikasi' => e($ibx->sifat->nama_sifat ?? ''),
                'berkas'    => e($ibx->berkas->nama ?? ''),
                'wilayah'   => e($ibx->wilayah),
                'isi_surat' => e($ibx->isi_surat),
                'tanggal'   => e($ibx->tgl_surat),
                'kepada'    => e($ibx->kepada),
                'perihal'   => e($ibx->perihal),
                'kode'      => e($ibx->klasifikasi->klas3 ?? ''),
                'tgl_buat'  => Carbon::parse($ibx->created_at)->isoFormat('DD-MMM-YYYY'),
                // 'tujuan'    => $ibx->NAMAUP,
                'uid'       => Crypt::encryptString($ibx->id),
                'option'    => view('components.outbox-action-buttons', ['ibx' => $ibx])->render(),
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
        $list = json_decode(Auth::user()->leveluser->daftar_terusan ?? '[]');
        $data = [
            'jra'          => $this->cacheService->getKlasifikasi(),
            'berkas'       => $this->cacheService->getTempatBerkas(),
            'instansi'     => DataUnit::all(),
            'perkembangan' => $this->cacheService->getPerkembangan(),
            'sifat'        => $this->cacheService->getSifatSurat(),
            'level'        => (!empty($list) && is_array($list)) ? LevelUser::whereIn('id', $list)->get() : [],
        ];
        return view('main.outbox.new', $data);
    }

    public function duplicate()
    {
        $data = [
            'lists' => Duplikat::orderBy('created_at', 'desc')->get(),
        ];

        return view('main.outbox.duplikat', $data);
    }

    public function edit($id)
    {
        $no    = (Crypt::decryptString($id));
        if (!$no) return abort(404);
        $outbox = Outbox::with(['klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap', 'spd', 'pengolah'])
                    ->where('uuid', $no)->first();
        
        $jra = Klasifikasi::where('id', $outbox->id_klasifikasi)->first();

        if ($jra) {
            $outbox->r_aktif = $jra->r_aktif;
            $outbox->r_inaktif = $jra->r_inaktif;
            $outbox->thn_aktif = date('Y') + intval($jra->r_aktif);
            $outbox->thn_inaktif = date('Y') + intval($jra->r_aktif) + intval($jra->r_inaktif);
            $outbox->ket_jra = $jra->ket_jra;
            $outbox->nilai_guna = $jra->nilai_guna;
        }

        $list = json_decode(Auth::user()->leveluser->daftar_terusan ?? '[]');
        // $outbox->uid = $id;
        $data  = [
            'jra'          => $this->cacheService->getKlasifikasi(),
            'berkas'       => $this->cacheService->getTempatBerkas(),
            'instansi'     => DataUnit::all(),
            'perkembangan' => $this->cacheService->getPerkembangan(),
            'sifat'        => $this->cacheService->getSifatSurat(),
            'level'        => (!empty($list) && is_array($list)) ? LevelUser::whereIn('id', $list)->get() : [],
            'outbox'       => $outbox,
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

    public function nomor_urut(Request $request)
    {
        $request->validate([
            'type'  => 'required|string|max:10',
        ]);

        $isPrimary = (bool) (Auth::user()->leveluser->is_primary ?? false);
        $kode_urut = $this->agendaService->preview('outbox', $isPrimary, Auth::user()->level, intval(date('Y')));

        return response()->json(['status' => 'success', 'urut' => $kode_urut]);
    }

    public function store(OutboxStoreRequest $request)
    {
        try {
            $this->suratKeluarService->store($request->validated(), $request->file('is_scan'), Auth::user());
            return redirect()->route('outbox')->with('success', 'Surat keluar berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan surat keluar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('outbox')->with('error', 'Surat keluar gagal disimpan: ' . $e->getMessage());
        }
    }

    public function update(OutboxUpdateRequest $request)
    {
        $outbox = Outbox::where('uuid', $request->uid)->first();
        if (!$outbox) {
            return redirect()->route('outbox')->with('error', 'Surat keluar tidak ditemukan.');
        }

        try {
            $this->suratKeluarService->update($outbox, $request->validated(), $request->file('is_scan'));
            return redirect()->route('outbox')->with('success', 'Surat keluar berhasil diupdate.');
        } catch (\Throwable $e) {
            Log::error('Gagal mengupdate surat keluar: ' . $e->getMessage());
            return redirect()->route('outbox')->with('error', 'Surat keluar gagal diupdate.');
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

        $outbox = Outbox::where('uuid', $id)->first();
        if (!$outbox) {
            return response()->json(['status' => 'failed', 'message' => 'Surat keluar tidak ditemukan.']);
        }

        $drop = $this->suratKeluarService->destroy($outbox);
        if ($drop) {
            return response()->json(['status' => 'success', 'message' => 'Surat keluar berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat keluar gagal dihapus.']);
        }
    }

    public function upload_file($file, $id)
    {
        return $this->fileService->upload($file, $id, 'suratkeluar');
    }

    public function check_surat(Request $request)
    {
        $request->validate([
            'nosurat'   => 'required|numeric',
        ]);

        $res = Outbox::with(['klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap', 'spd', 'pengolah'])
                ->where('no_agenda', intval($request->nosurat))
                // ->where(function($q) {
                //     $q->whereNull('id_spd')
                //       ->orWhere('id_spd', '');
                // })
                ->whereNull('id_spd')
                ->orderBy('created_at', 'desc')->get();

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

        // $add = '_textonly';
        $add = '';
        // $surat = ArsipSurat::where('NO', intval($id))->first();
        $surat = Outbox::where('id', $id)->first();
        if (!$surat) {
            return response()->json(['status' => 'failed', 'message' => 'Surat tidak ditemukan.']);
        }

        // $last = ArsipSurat::where('JENISSURAT', 'Keluar')->orderBy('NO', 'desc')->first();
        $query = Outbox::with(['klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap', 'spd', 'pengolah']);
        if (Auth::user()->leveluser->is_primary == true) {
            $query->where('is_primary_agenda', true)->where('year', date('Y'))->orderBy('no_agenda', 'desc');
        } else {
            $query->where('is_primary_agenda', false)->where('year', date('Y'))->where('level_surat', Auth::user()->level)->orderBy('no_agenda', 'desc');
        }
        $last = $query->first();
        
        $kode_urut = (!empty($last->year) ? ($last->year == date('Y') ? intval($last->no_agenda) + 1 : 1) : 1);

        $data  = [
            'id_surat'   => $surat->id,
            'nomor_surat' => $surat->no_agenda,
            'nomor_awal' => $kode_urut,
            'jumlah'     => intval($request->jumlah),
            'tahun'      => date('Y'),
        ];

        $start = $kode_urut;
        $list  = [$surat];
        $raw_pdf = [];
        $nosurat = [];
        $folder = storage_path('app/private/duplikat');

        for ($i = 0; $i < intval($request->jumlah); $i++) {
            $newSurat = $surat->replicate();
            // $newSurat->no_agenda = $start;

            if ($i == 0) {
                $start = $newSurat->no_agenda;
            }
            if ($start < 9) {
                $newSurat->no_agenda = '000' . $start;
            } elseif ($start > 9 && $start < 99) {
                $newSurat->no_agenda = '00' . $start;
            } elseif ($start > 99 && $start < 999) {
                $newSurat->no_agenda = '0' . $start;
            }

            if ($i > 0) {
                $pass1 = explode('/', $surat->no_surat);
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
                                $newNumber .= '.' . $item ?? '';
                            }
                        }
                    } else {
                        $newNumber .= '/' . $value;
                    }
                }

                $newSurat->no_surat = $newNumber;
                $nosurat[] = $newNumber;
                $newSurat->save();
            }
            // $save = $newSurat->save();
            $save = true;
            if ($save) {
                $pdf = Pdf::loadView('main.outbox.template_duplikat' . $add, ['data' => $newSurat]);
                $name  = 'raw_' . $id . '_' . $start . '_' . date('Ymd') . '.pdf';
                $path  = $folder . '/' . $name;
                if (!File::exists($folder)) {
                    File::makeDirectory($folder, 0755, true, true);
                }
                $pdf->save($path);
                $raw_pdf[] = $name;
            }

            $list[] = $newSurat;
            if ($i < (intval($request->jumlah) - 1)) {
                $start++;
            }
        }
        // Log::info('Duplikat Surat Keluar: ', $list);

        $mergeName = 'duplikat_surat_keluar_' . $kode_urut . '-' . $start . '_' . date('Ymd_His') . '.pdf';
        $merge = $this->merge_pdf($raw_pdf, $mergeName);
        $data += [
            'nomor_akhir' => $start,
            'path_file'   => $merge ?? 'none',
            'list'        => json_encode($nosurat),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $dup = Duplikat::insert($data);

        if ($dup) {
            return response()->json(['status' => 'success', 'message' => 'Surat berhasil diduplikat.', 'data' => ($merge ?? 'none')]);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Surat gagal diduplikat.', 'data' => ($merge ?? 'none')]);
        }
    }

    public function merge_pdf($files, $name)
    {
        $folder = storage_path('app/private/duplikat');
        $pdf = new Fpdi();
        if (count($files) > 0) {
            foreach ($files as $key => $value) {
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
                $pdf->Output($folder . '/'. $name, 'F');
                Log::info('success merged');
                foreach ($files as $file) {
                    unlink($folder .'/'. $file);
                }
                return $name;
            } catch(\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    public function template_test($uid)
    {
        $data = ArsipSurat::where('NO', $uid)->first();
        $pdf = Pdf::loadView('main.outbox.template_duplikat', ['data' => $data]);

        return $pdf->stream('test.pdf');
    }

    public function view_pdf($uid)
    {
        $request = Request();
        $id  = Crypt::decryptString($uid);
        if (!$id) return abort(404);

        // $surat = ArsipSurat::where('NO', $id)->first();
        $surat = Outbox::with(['klasifikasi:id,klas3,masalah3,series,r_aktif,r_inaktif,ket_jra,nilai_guna', 'media', 'sifat', 'berkas', 'perkembangan', 'level:id,role,nama', 'creator:id,uuid,nama_lengkap', 'spd', 'pengolah'])
                ->where('uuid', $id)->first();
        
        if (!$surat) return abort(404);

        if (empty($request->type)) return abort(404);
        
        if ($request->type == 'kartu') {
            // $pdf = $this->build_kartu($surat, '_textonly');
            $pdf = $this->build_kartu($surat, null);
        } else {
            return abort(404);
        }

        return $pdf->stream($surat->no_agenda . '_' . $surat->year . '_' . ($request->type == 'kartu' ? 'kartu_surat_masuk' : $request->type) .'.pdf');
    }

    public function build_kartu($outbox, $add = null)
    {
        // $sign = Pimpinan::where('level', $outbox->Posisi)->where('is_default', true)->first();
        $sign = Pimpinan::where('level', $outbox->posisi_level)->where('is_default', true)->first();
        if ($outbox->no_agenda < 9) {
            $outbox->no_agenda = '000' . $outbox->no_agenda;
        } elseif ($outbox->no_agenda > 9 && $outbox->no_agenda < 99) {
            $outbox->no_agenda = '00' . $outbox->no_agenda;
        } elseif ($outbox->no_agenda > 99 && $outbox->no_agenda < 999) {
            $outbox->no_agenda = '0' . $outbox->no_agenda;
        }

        $pdf = Pdf::loadView('main.outbox.template_duplikat' . $add, ['data' => $outbox, 'sign' => $sign]);
        return $pdf;
    }

    public function view_file($uid)
    {
        try {
            $file = Crypt::decryptString($uid);
        } catch (\Throwable $e) {
            return abort(404);
        }

        if (!$file) return abort(404);

        $safeFile = basename($file);
        if ($safeFile !== $file) return abort(404);

        $path = $this->fileService->resolveFilePath($safeFile, 'suratkeluar');
        if (!$path || !file_exists($path)) {
            return abort(404);
        }

        return response()->file($path);
    }
}
