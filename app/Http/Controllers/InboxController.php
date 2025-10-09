<?php

namespace App\Http\Controllers;

use App\Models\ArsipSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

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
                                    <button type="button" class="btn btn-outline-success bs-tooltip" title="Cetak Surat">
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
        $data = [];
        return view('main.inbox.new', $data);
    }

    public function edit($id)
    {
        $no    = json_decode(Crypt::decryptString($id));
        $inbox = ArsipSurat::where('NO', $no)->first();
        $data  = [
            'inbox' => $inbox,
        ];

        return view('main.inbox.edit', $data);
    }
}
