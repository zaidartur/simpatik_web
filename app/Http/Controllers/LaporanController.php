<?php

namespace App\Http\Controllers;

use App\Exports\AgendaAllExport;
use App\Exports\AgendaKeluarExport;
use App\Exports\AgendaMasukExport;
use App\Exports\StatistikExport;
use App\Exports\TindakLanjutExport;
use App\Models\ArsipSurat;
use App\Models\Disposisi;
use App\Models\Inbox;
use App\Models\Klasifikasi;
use App\Models\Outbox;
use App\Models\User;
use App\Services\AgendaFpdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;

class LaporanController extends Controller
{
    public function __construct() {
        $this->middleware('permission:statistik', ['only' => ['statistik', 'statistik_ssr']]);
        $this->middleware('permission:tindak lanjut', ['only' => ['tindak_lanjut', 'tindak_lanjut_ssr', 'tindak_lanjut_print', 'tindak_lanjut_excel']]);
        $this->middleware('permission:agenda', ['only' => ['agenda', 'agenda_ssr', 'agenda_print', 'agenda_print_fpdf']]);
    }

    public function statistik()
    {
        $yearsInbox = Inbox::whereNull('on_delete')->select('year')->distinct();
        $yearsOutbox = Outbox::whereNull('on_delete')->select('year')->distinct();
        $years = $yearsInbox->union($yearsOutbox)->orderBy('year', 'DESC')->pluck('year');

        $yearsCollection = $years->map(function ($y) {
            return (object)['tahun' => $y];
        });

        $lastYear = $yearsCollection->first() ?? (object)['tahun' => date('Y')];

        $data = [
            'tahun' => $yearsCollection,
            'last'  => $lastYear,
        ];
        return view('main.laporan.statistik', $data);
    }

    public function tindak_lanjut()
    {
        return view('main.laporan.tindak_lanjut');
    }

    public function agenda()
    {
        $data = [
            'years' => Inbox::select('year')->distinct()->orderBy('year', 'desc')->get(),
        ];
        return view('main.laporan.agenda', $data);
    }

    public function statistik_ssr()
    {
        $request = Request();
        $month = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $year = request()->has('tahun') ? request()->get('tahun') : date('Y');

        $inboxCounts = Inbox::whereNull('on_delete')
                ->where('year', $year)
                ->select(DB::raw('count(id) as count'), DB::raw("EXTRACT(MONTH FROM created_at)::integer as month"))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

        $outboxCounts = Outbox::whereNull('on_delete')
                ->where('year', $year)
                ->select(DB::raw('count(id) as count'), DB::raw("EXTRACT(MONTH FROM created_at)::integer as month"))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

        $data = [];
        $masuk = 0;
        $keluar = 0;
        $jml = 0;

        for ($i = 1; $i <= 12; $i++) { 
            $surat_masuk  = intval($inboxCounts[$i] ?? 0);
            $surat_keluar = intval($outboxCounts[$i] ?? 0);
            $total        = $surat_masuk + $surat_keluar;

            $data[] = [
                'bulan'         => $month[$i] ?? $i,
                'surat_masuk'   => number_format($surat_masuk, 0, ',', '.'),
                'surat_keluar'  => number_format($surat_keluar, 0, ',', '.'),
                'total'         => number_format($total, 0, ',', '.'),
            ];

            $masuk += $surat_masuk;
            $keluar += $surat_keluar;
            $jml += $total;
        }

        $data[] = [
            'bulan'         => '<strong class="text-success">Tahun '. $year .'</strong>',
            'surat_masuk'   => '<strong class="text-success">' . number_format($masuk, 0, ',', '.') . '</strong>',
            'surat_keluar'  => '<strong class="text-success">' . number_format($keluar, 0, ',', '.') . '</strong>',
            'total'         => '<strong class="text-success">' . number_format($jml, 0, ',', '.') . '</strong>',
        ];

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    public function tindak_lanjut_ssr()
    {
        $request = Request();
        $user = $request->user();
        if ($user->hasAnyRole(['administrator', 'umum', 'setda', 'wabup', 'bupati', 'admin'])) {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $query = ArsipSurat::where(function($q) {
                        $q->where(function($sub) {
                            $sub->whereNotNull('DisposisiSekda')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiSekda\", ''))) > 0");
                        })
                        ->orWhere(function($sub) {
                            $sub->whereNotNull('DisposisiSekda2')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiSekda2\", ''))) > 0");
                        })
                        ->orWhere(function($sub) {
                            $sub->whereNotNull('DisposisiBupati')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiBupati\", ''))) > 0");
                        })
                        ->orWhere(function($sub) {
                            $sub->whereNotNull('DisposisiWakil')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiWakil\", ''))) > 0");
                        });
                    })
                    ->where('JENISSURAT', 'Masuk')
                    ->orderBy('NO', 'DESC');

            if ($user->hasRole('setda')) {
                $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Bupati']);
            }
            if ($user->hasRole('wabup')) {
                $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Wakil Bupati']);
            }
            if ($user->hasRole('bupati')) {
                $query->whereIn('Posisi', ['Bupati']);
            }

            // date filtering
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startSlash = str_replace('-', '/', $request->start_date);
                $endSlash   = str_replace('-', '/', $request->end_date);
                $startDash  = str_replace('/', '-', $request->start_date);
                $endDash    = str_replace('/', '-', $request->end_date);

                $query->where(function($sub) use ($startSlash, $endSlash, $startDash, $endDash) {
                    $sub->whereBetween('TGLSURAT', [$startSlash, $endSlash])
                        ->orWhereBetween('TGLSURAT', [$startDash, $endDash])
                        ->orWhereBetween('TGLENTRY', [$startSlash, $endSlash])
                        ->orWhereBetween('TGLENTRY', [$startDash, $endDash]);
                });
            }

            $totalData = $query->count();

            // search query
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('NOSURAT', 'ilike', "%$search%")
                        ->orWhere('drkpd', 'ilike', "%$search%")
                        ->orWhere('PERIHAL', 'ilike', "%$search%")
                        ->orWhere('ISI', 'ilike', "%$search%")
                        ->orWhere('KLAS3', 'ilike', "%$search%");
                });
            }
            $totalFiltered = $query->count();
            $list = $query->skip($start)->take($length)->get();

            $parseLegacyDate = function($dateStr, $format = 'DD-MM-YYYY') {
                if (empty($dateStr)) return null;
                try {
                    $str = trim((string)$dateStr);
                    if (str_contains($str, '/')) {
                        $parts = explode(' ', $str);
                        $dmy = explode('/', $parts[0]);
                        if (count($dmy) === 3) {
                            $normDate = sprintf('%04d-%02d-%02d', $dmy[2], $dmy[1], $dmy[0]);
                            if (isset($parts[1])) {
                                $normDate .= ' ' . $parts[1];
                            }
                            return Carbon::parse($normDate)->isoFormat($format);
                        }
                    }
                    return Carbon::parse($str)->isoFormat($format);
                } catch (\Throwable $e) {
                    return (string) $dateStr;
                }
            };

            $data = [];
            foreach ($list as $row) {
                $data[] = [
                    'nomor'     => $row->NOSURAT,
                    'no_agenda' => $row->NOAGENDA,
                    'klasifikasi' => $row->SIFAT_SURAT,
                    'berkas'    => $row->NAMABERKAS,
                    'wilayah'   => $row->WILAYAH,
                    'isi_surat' => $row->ISI,
                    'tanggal'   => $parseLegacyDate($row->TGLSURAT) ?? '-',
                    'kepada'    => $row->drkpd,
                    'perihal'   => $row->PERIHAL,
                    'kode'      => $row->KLAS3,
                    'tgl_buat'  => $parseLegacyDate($row->TGLENTRY) ?? '-',
                    'posisi'    => $row->Posisi,
                    'class'     => ($row->Posisi == 'Sekretaris Daerah' ? 'badge-info' : ($row->Posisi == 'Wakil Bupati' ? 'badge-secondary' : ($row->Posisi == 'Bupati' ? 'badge-primary' : 'badge-dark'))),
                    'uid'       => Crypt::encryptString($row->NO),
                    'sekda'     => $row->DisposisiSekda,
                    'sekda2'    => $row->DisposisiSekda2,
                    'bupati'    => $row->DisposisiBupati,
                    'wakil'     => $row->DisposisiWakil,
                    'tsekda'    => $parseLegacyDate($row->tglsekda1),
                    'tsekda2'   => $parseLegacyDate($row->tglsekda2),
                    'tbupati'   => $parseLegacyDate($row->tglbupati1),
                    'twakil'    => $parseLegacyDate($row->tglwakil),
                    'option'    => '',
                    'status'    => $row->statussurat == 'selesai' ? '<span class="badge badge-success mb-2 me-4">Selesai</span>' : '<span class="badge badge-secondary mb-2 me-4">Menunggu</span>',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ]);
    }

    public function tindak_lanjut_print(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $user = $request->user();
        if (!$user->hasAnyRole(['administrator', 'umum', 'setda', 'wabup', 'bupati', 'admin'])) {
            return abort(403);
        }

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if ($startDate && $endDate) {
            $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
            if ($diffInDays > 31) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Rentang waktu cetak PDF maksimal 31 hari. Untuk rentang waktu lebih panjang, silakan gunakan fitur Ekspor Excel.',
                ], 422);
            }
        }

        $query = ArsipSurat::where(function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('DisposisiSekda')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiSekda\", ''))) > 0");
                })
                ->orWhere(function($sub) {
                    $sub->whereNotNull('DisposisiSekda2')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiSekda2\", ''))) > 0");
                })
                ->orWhere(function($sub) {
                    $sub->whereNotNull('DisposisiBupati')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiBupati\", ''))) > 0");
                })
                ->orWhere(function($sub) {
                    $sub->whereNotNull('DisposisiWakil')->whereRaw("LENGTH(TRIM(COALESCE(\"DisposisiWakil\", ''))) > 0");
                });
            })
            ->where('JENISSURAT', 'Masuk');

        if ($user->hasRole('setda')) {
            $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Bupati']);
        } elseif ($user->hasRole('wabup')) {
            $query->whereIn('Posisi', ['Sekeretaris Daerah', 'Sekretaris Daerah', 'Wakil Bupati']);
        } elseif ($user->hasRole('bupati')) {
            $query->whereIn('Posisi', ['Bupati']);
        }

        if ($startDate && $endDate) {
            $startSlash = str_replace('-', '/', $startDate);
            $endSlash   = str_replace('-', '/', $endDate);
            $startDash  = str_replace('/', '-', $startDate);
            $endDash    = str_replace('/', '-', $endDate);

            $query->where(function($sub) use ($startSlash, $endSlash, $startDash, $endDash) {
                $sub->whereBetween('TGLSURAT', [$startSlash, $endSlash])
                    ->orWhereBetween('TGLSURAT', [$startDash, $endDash])
                    ->orWhereBetween('TGLENTRY', [$startSlash, $endSlash])
                    ->orWhereBetween('TGLENTRY', [$startDash, $endDash]);
            });
        }

        $items = $query->orderBy('NO', 'desc')->limit(1000)->get();

        $pdf = Pdf::loadView('main.laporan.template_tindak_lanjut', [
            'items'     => $items,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'user'      => $user,
        ]);

        $pdf->setPaper('legal', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isFontSubsettingEnabled', true);

        return $pdf->stream('Laporan_Tindak_Lanjut_' . ($startDate ? $startDate . '_' . $endDate : 'Semua') . '.pdf');
    }

    public function tindak_lanjut_excel(Request $request)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['administrator', 'umum', 'setda', 'wabup', 'bupati', 'admin'])) {
            return abort(403);
        }

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $role      = $user->roles->pluck('name')->first() ?? 'administrator';

        $fileName = 'Laporan_Tindak_Lanjut_' . ($startDate ? $startDate . '_' . $endDate : date('Y')) . '.xlsx';

        return Excel::download(new TindakLanjutExport($startDate, $endDate, $role), $fileName);
    }

    public function agenda_ssr()
    {
        $request = Request();
        $user = $request->user();
        if ($user->hasAnyRole(['administrator', 'admin', 'setda', 'wabup', 'bupati', 'umum'])) {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $jenis = $request->input('jenis');

            $filterDate = function($q) use ($request) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                    $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                    $sDateStr = $request->start_date;
                    $eDateStr = $request->end_date;

                    $q->where(function($sub) use ($startDate, $endDate, $sDateStr, $eDateStr) {
                        $sub->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereBetween('tgl_surat', [$sDateStr, $eDateStr]);
                    });
                } else {
                    $q->where('year', date('Y'));
                }
            };

            $filterSearch = function($q, $search, $isOutbox = false) {
                if (!empty($search)) {
                    $q->where(function ($sub) use ($search, $isOutbox) {
                        $sub->where('no_surat', 'ilike', "%$search%")
                            ->orWhere('no_agenda', 'ilike', "%$search%")
                            ->orWhere('perihal', 'ilike', "%$search%")
                            ->orWhere('isi_surat', 'ilike', "%$search%")
                            ->orWhere($isOutbox ? 'kepada' : 'dari', 'ilike', "%$search%")
                            ->orWhere('wilayah', 'ilike', "%$search%");
                    });
                }
            };

            $searchValue = $request->input('search.value');

            if ($jenis === 'Keluar') {
                $q = Outbox::whereNull('on_delete')->with(['klasifikasi', 'creator.leveluser']);
                $filterDate($q);
                $filterSearch($q, $searchValue, true);
                $totalData = Outbox::whereNull('on_delete')->where('year', date('Y'))->count();
                $totalFiltered = $q->count();
                $list = $q->orderBy('created_at', 'desc')->skip($start)->take($length)->get();
            } elseif ($jenis === 'Masuk') {
                $q = Inbox::whereNull('on_delete')->with([
                    'klasifikasi', 'creator.leveluser', 'disposisi.pengirim.leveluser', 'disposisi.pimpinan'
                ]);
                $filterDate($q);
                $filterSearch($q, $searchValue, false);
                $totalData = Inbox::whereNull('on_delete')->where('year', date('Y'))->count();
                $totalFiltered = $q->count();
                $list = $q->orderBy('created_at', 'desc')->skip($start)->take($length)->get();
            } else {
                // Semua (Masuk dan Keluar digabungkan)
                $qInbox = DB::table('inboxes')
                    ->select([
                        DB::raw("'Masuk' as jenis_surat"),
                        'id', 'uuid', 'no_agenda', 'no_surat', 'tgl_surat', 'created_at',
                        'isi_surat', 'perihal', 'dari',
                        DB::raw("'Sekretariat Daerah' as kepada"),
                        'id_klasifikasi', 'sifat_surat', 'created_by', 'nama_berkas', 'wilayah', 'posisi_level'
                    ])
                    ->whereNull('on_delete');
                $filterDate($qInbox);
                $filterSearch($qInbox, $searchValue, false);

                $qOutbox = DB::table('outboxes')
                    ->select([
                        DB::raw("'Keluar' as jenis_surat"),
                        'id', 'uuid', 'no_agenda', 'no_surat', 'tgl_surat', 'created_at',
                        'isi_surat', 'perihal',
                        DB::raw("unit as dari"),
                        'kepada',
                        'id_klasifikasi', 'sifat_surat', 'created_by', 'nama_berkas', 'wilayah',
                        DB::raw("null::bigint as posisi_level")
                    ])
                    ->whereNull('on_delete');
                $filterDate($qOutbox);
                $filterSearch($qOutbox, $searchValue, true);

                $unionQuery = $qInbox->unionAll($qOutbox);
                $totalFiltered = DB::table(DB::raw("({$unionQuery->toSql()}) as combined"))
                    ->mergeBindings($unionQuery)
                    ->count();
                $totalData = $totalFiltered;

                $rows = DB::table(DB::raw("({$unionQuery->toSql()}) as combined"))
                    ->mergeBindings($unionQuery)
                    ->orderBy('created_at', 'desc')
                    ->skip($start)
                    ->take($length)
                    ->get();

                // Batch eager load relations for the paginated rows
                $klasIds = $rows->pluck('id_klasifikasi')->filter()->unique();
                $creatorUuids = $rows->pluck('created_by')->filter()->unique();
                $inboxUuids = $rows->where('jenis_surat', 'Masuk')->pluck('uuid')->filter()->unique();

                $klasifikasis = \App\Models\Klasifikasi::whereIn('id', $klasIds)->get()->keyBy('id');
                $creators = \App\Models\User::with('leveluser')->whereIn('uuid', $creatorUuids)->get()->keyBy('uuid');
                $disposisisByUuid = \App\Models\Disposisi::with(['pengirim.leveluser', 'pimpinan'])
                    ->whereIn('uid_surat', $inboxUuids)
                    ->get()
                    ->groupBy('uid_surat');

                $list = $rows->map(function($r) use ($klasifikasis, $creators, $disposisisByUuid) {
                    $r->klasifikasi = $klasifikasis[$r->id_klasifikasi] ?? null;
                    $r->creator = $creators[$r->created_by] ?? null;
                    $r->disposisi = $r->jenis_surat === 'Masuk' ? ($disposisisByUuid[$r->uuid] ?? collect([])) : collect([]);
                    return $r;
                });
            }

            $getDisposisiInfo = function($disposisis, $target) {
                if (!$disposisis || $disposisis->isEmpty()) return '-';
                $found = $disposisis->first(function($d) use ($target) {
                    $senderLevel = strtolower($d->pengirim?->leveluser?->nama ?? '');
                    if ($target === 'bupati') {
                        return $d->id_pimpinan == 1 || str_contains($senderLevel, 'bupati') || ($d->pengirim && $d->pengirim->hasRole('bupati'));
                    }
                    if ($target === 'wakil') {
                        return $d->id_pimpinan == 3 || str_contains($senderLevel, 'wakil') || ($d->pengirim && $d->pengirim->hasRole('wabup'));
                    }
                    if ($target === 'sekda') {
                        return $d->id_pimpinan == 4 || str_contains($senderLevel, 'sekda') || str_contains($senderLevel, 'sekretaris daerah') || ($d->pengirim && $d->pengirim->hasRole('setda'));
                    }
                    return false;
                });

                if ($found && !empty($found->catatan_disposisi)) {
                    $tgl = $found->created_at ? Carbon::parse($found->created_at)->isoFormat('DD-MM-YYYY HH:mm') : '';
                    return '<b><p style="width: 100%; text-align: right;">' . $tgl . '</p></b><br>' . e($found->catatan_disposisi);
                }

                return '-';
            };

            $data = [];
            foreach ($list as $l => $row) {
                $isKeluar = ($row instanceof Outbox) || (($row->jenis_surat ?? '') === 'Keluar');
                $tglKirim = $row->created_at ? Carbon::parse($row->created_at)->isoFormat('DD-MM-YYYY') : '-';
                $tglSurat = $row->tgl_surat ? Carbon::parse($row->tgl_surat)->isoFormat('DD-MM-YYYY') : '-';
                $noSurat = e($row->no_surat ?? '-');

                $klas3 = e($row->klasifikasi->klas3 ?? '-');
                $ketJra = e($row->klasifikasi->ket_jra ?? '');
                $isiSurat = e($row->isi_surat ?? $row->perihal ?? '-');

                $kepada = $isKeluar ? ($row->kepada ?? '-') : ($row->kepada ?? 'Sekretariat Daerah');
                $dari = $isKeluar
                    ? ($row->creator?->leveluser?->nama ?? ($row->creator?->nama_lengkap ?? (!empty($row->dari) ? $row->dari : 'Sekretariat Daerah')))
                    : ($row->dari ?? ($row->creator?->leveluser?->nama ?? ($row->creator?->nama_lengkap ?? '-')));

                $disposisis = $isKeluar ? collect([]) : ($row->disposisi ?? collect([]));

                $noAgenda = $row->no_agenda ?? '-';
                if (empty($jenis)) {
                    $badgeClass = $isKeluar ? 'badge-light-danger' : 'badge-light-primary';
                    $label = $isKeluar ? '[Keluar]' : '[Masuk]';
                    $noAgenda .= '<br><span class="badge ' . $badgeClass . '">' . $label . '</span>';
                }

                $data[$l] = [
                    'no_agenda' => $noAgenda,
                    'kepada'    => $kepada,
                    'tgl_buat'  => $tglKirim,
                    'tanggal'   => $tglSurat,
                    'nomor'     => $noSurat,
                    'row3'      => $tglKirim . '<br>' . $tglSurat . '<br>' . $noSurat,
                    'kode'      => $klas3,
                    'jra'       => $ketJra,
                    'isi_surat' => $isiSurat,
                    'row4'      => $klas3 . '<br><b>' . $ketJra . '</b><br>' . $isiSurat,
                    'berkas'    => $row->nama_berkas ?? '-',
                    'wilayah'   => $row->wilayah ?? '-',
                    'dari'      => $dari,
                    'perihal'   => $row->perihal ?? '-',
                    'class'     => $row->posisi_level ?? '',
                    'uid'       => Crypt::encryptString($row->uuid),
                ];

                if ($user->hasAnyRole(['administrator', 'umum', 'setda'])) {
                    $data[$l] += [
                        'sekda'     => $getDisposisiInfo($disposisis, 'sekda'),
                        'bupati'    => $getDisposisiInfo($disposisis, 'bupati'),
                        'wakil'     => $getDisposisiInfo($disposisis, 'wakil'),
                    ];
                }

                if ($user->hasRole('wabup')) {
                    $data[$l] += [
                        'wakil'     => $getDisposisiInfo($disposisis, 'wakil'),
                    ];
                }

                if ($user->hasRole('bupati')) {
                    $data[$l] += [
                        'bupati'    => $getDisposisiInfo($disposisis, 'bupati'),
                    ];
                }
            }

            return response()->json([
                'draw' => intval($request->draw) ?? 0,
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        return response()->json([
            'draw' => intval($request->draw) ?? 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ]);
    }

    /**
     * Helper terpusat untuk mengambil data agenda dan memformatnya secara konsisten untuk PDF (DomPDF & FPDF).
     */
    protected function getAgendaExportData(Request $request): array
    {
        $year    = !empty($request->tahun) ? $request->tahun : date('Y');
        $month   = $request->bulan;
        $type    = $request->jenis;

        $startDate = null;
        $endDate = null;
        $sDateStr = null;
        $eDateStr = null;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
            $sDateStr = $request->start_date;
            $eDateStr = $request->end_date;

            // Validasi batas maksimal 31 hari untuk keamanan performa render PDF
            if ($startDate->diffInDays($endDate) > 31) {
                throw new \InvalidArgumentException('Rentang waktu cetak PDF maksimal 31 hari (1 bulan). Silakan gunakan fitur Ekspor Excel untuk rentang waktu yang lebih panjang.');
            }
        }

        $filterDate = function($q) use ($startDate, $endDate, $sDateStr, $eDateStr, $year) {
            if ($startDate && $endDate) {
                $q->where(function ($sub) use ($startDate, $endDate, $sDateStr, $eDateStr) {
                    $sub->whereBetween('created_at', [$startDate, $endDate])
                        ->orWhereBetween('tgl_surat', [$sDateStr, $eDateStr]);
                });
            } elseif (!empty($year)) {
                $q->where('year', $year);
            }
        };

        $getDisposisiInfo = function($disposisis, $target) {
            if (!$disposisis || $disposisis->isEmpty()) return '-';
            $found = $disposisis->first(function($d) use ($target) {
                $senderLevel = strtolower($d->pengirim?->leveluser?->nama ?? '');
                if ($target === 'bupati') {
                    return $d->id_pimpinan == 1 || str_contains($senderLevel, 'bupati') || ($d->pengirim && $d->pengirim->hasRole('bupati'));
                }
                if ($target === 'wakil') {
                    return $d->id_pimpinan == 3 || str_contains($senderLevel, 'wakil') || ($d->pengirim && $d->pengirim->hasRole('wabup'));
                }
                if ($target === 'sekda') {
                    return $d->id_pimpinan == 4 || str_contains($senderLevel, 'sekda') || str_contains($senderLevel, 'sekretaris daerah') || ($d->pengirim && $d->pengirim->hasRole('setda'));
                }
                return false;
            });

            if ($found && !empty($found->catatan_disposisi)) {
                $tgl = $found->created_at ? Carbon::parse($found->created_at)->isoFormat('DD-MM-YYYY HH:mm') : '';
                return '<b><p style="width: 100%; text-align: right;">' . $tgl . '</p></b><br>' . e($found->catatan_disposisi);
            }

            return '-';
        };

        $inboxSelect = [
            'id', 'uuid', 'no_agenda', 'no_surat', 'tgl_surat', 'created_at',
            'isi_surat', 'perihal', 'dari', 'id_klasifikasi', 'sifat_surat',
            'created_by', 'posisi_surat', 'nama_berkas', 'wilayah'
        ];
        $outboxSelect = [
            'id', 'uuid', 'no_agenda', 'no_surat', 'tgl_surat', 'created_at',
            'isi_surat', 'perihal', 'kepada', 'id_klasifikasi', 'sifat_surat',
            'created_by', 'unit', 'id_unit', 'wilayah'
        ];

        $inboxWith = [
            'klasifikasi:id,klas3,ket_jra',
            'creator:id,nama_lengkap,level',
            'creator.leveluser:id,nama',
            'disposisi:id,uid_surat,catatan_disposisi,created_at,id_pimpinan,pengirim_uuid',
            'disposisi.pengirim:id,uuid,nama_lengkap,level',
            'disposisi.pengirim.leveluser:id,nama',
            'disposisi.pimpinan:id,nama,level',
        ];
        $outboxWith = [
            'klasifikasi:id,klas3,ket_jra',
            'creator:id,nama_lengkap,level',
            'creator.leveluser:id,nama',
            'pengolah:id,nama_unit',
        ];

        if ($type === 'Keluar') {
            $queryOutbox = Outbox::whereNull('on_delete')->select($outboxSelect)->with($outboxWith);
            $filterDate($queryOutbox);
            $items = $queryOutbox->orderBy('no_agenda', 'ASC')->get();
        } elseif ($type === 'Masuk') {
            $queryInbox = Inbox::whereNull('on_delete')->select($inboxSelect)->with($inboxWith);
            $filterDate($queryInbox);
            $items = $queryInbox->orderBy('no_agenda', 'ASC')->get();
        } else {
            // Semua (Masuk dan Keluar digabungkan)
            $queryInbox = Inbox::whereNull('on_delete')->select($inboxSelect)->with($inboxWith);
            $filterDate($queryInbox);
            $inboxList = $queryInbox->get();

            $queryOutbox = Outbox::whereNull('on_delete')->select($outboxSelect)->with($outboxWith);
            $filterDate($queryOutbox);
            $outboxList = $queryOutbox->get();

            $items = $inboxList->concat($outboxList)->sortByDesc(function($item) {
                return $item->created_at;
            });
        }

        $data = [];
        foreach ($items as $row) {
            $isKeluar = ($row instanceof Outbox);
            $tglKirim = $row->created_at ? Carbon::parse($row->created_at)->isoFormat('DD-MM-YYYY') : '-';
            $tglSurat = $row->tgl_surat ? Carbon::parse($row->tgl_surat)->isoFormat('DD-MM-YYYY') : '-';
            $noSurat = e($row->no_surat ?? '-');

            $klas3 = e($row->klasifikasi->klas3 ?? '-');
            $ketJra = e($row->klasifikasi->ket_jra ?? '');
            $isiSurat = e($row->isi_surat ?? $row->perihal ?? '-');

            $kepada = $isKeluar ? ($row->kepada ?? '-') : ($row->kepada ?? 'Sekretariat Daerah');
            $dari = $isKeluar
                ? ($row->creator?->leveluser?->nama ?? ($row->creator?->nama_lengkap ?? ($row->unit ?? 'Sekretariat Daerah')))
                : ($row->dari ?? ($row->creator?->leveluser?->nama ?? ($row->creator?->nama_lengkap ?? '-')));

            $disposisis = $isKeluar ? collect([]) : ($row->disposisi ?? collect([]));

            $noAgenda = $row->no_agenda ?? '-';
            if (empty($type)) {
                $labelJenis = $isKeluar ? '[Keluar]' : '[Masuk]';
                $noAgenda = $noAgenda . '<br><small>' . $labelJenis . '</small>';
            }

            $data[] = [
                'no_agenda'   => $noAgenda,
                'jenis_surat' => $isKeluar ? 'Keluar' : 'Masuk',
                'kepada'      => $kepada,
                'row3'        => $tglKirim . '<br>' . $tglSurat . '<br>' . $noSurat,
                'row4'        => $klas3 . '<br><b>' . $ketJra . '</b><br>' . $isiSurat,
                'dari'        => $dari,
                'sekda'       => $getDisposisiInfo($disposisis, 'sekda'),
                'bupati'      => $getDisposisiInfo($disposisis, 'bupati'),
                'wakil'       => $getDisposisiInfo($disposisis, 'wakil'),
            ];
        }

        unset($items, $inboxList, $outboxList);

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $rangeFormatted = Carbon::createFromFormat('Y-m-d', $request->start_date)->isoFormat('DD MMMM YYYY') . ' s/d ' . Carbon::createFromFormat('Y-m-d', $request->end_date)->isoFormat('DD MMMM YYYY');
        } elseif (!empty($month) && !empty($year)) {
            $rangeFormatted = 'Bulan: ' . Carbon::createFromDate((int)$year, (int)$month, 1)->isoFormat('MMMM YYYY');
        } elseif (!empty($year)) {
            $rangeFormatted = 'Tahun: ' . $year;
        } else {
            $rangeFormatted = '';
        }

        return [
            'data'       => $data,
            'type'       => $type,
            'year'       => $year,
            'month'      => $month,
            'range'      => $rangeFormatted,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ];
    }

    /**
     * Cetak Laporan Agenda menggunakan Engine DomPDF (Dioptimalkan).
     */
    public function agenda_print(Request $request) 
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        try {
            $exportData = $this->getAgendaExportData($request);
        } catch (\InvalidArgumentException $e) {
            return $this->renderSwalErrorAndClose($e->getMessage());
        }

        $pdf = $this->build_pdf(
            $exportData['data'],
            $exportData['type'],
            $exportData['year'],
            $exportData['month'],
            $exportData['start_date'],
            $exportData['end_date'],
            $exportData['range']
        );

        return $pdf->stream('agenda_dompdf_' . $exportData['year'] . ($exportData['month'] ?: date('m')) . '.pdf');
    }

    /**
     * Cetak Laporan Agenda menggunakan Engine Native FPDF (Cepat & Hemat RAM).
     */
    public function agenda_print_fpdf(Request $request)
    {
        ini_set('memory_limit', '128M');
        set_time_limit(120);

        try {
            $exportData = $this->getAgendaExportData($request);
        } catch (\InvalidArgumentException $e) {
            return $this->renderSwalErrorAndClose($e->getMessage());
        }

        $fpdfService = new AgendaFpdfService();
        $pdfContent = $fpdfService->build(
            $exportData['data'],
            $exportData['type'] ?? '',
            $exportData['range'],
            $request->user()
        );

        $fileName = 'agenda_fpdf_' . $exportData['year'] . ($exportData['month'] ?: date('m')) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function build_pdf($agenda, $type, $tahun, $bulan, $start_date = null, $end_date = null, $range = null)
    {
        $pdf = Pdf::setPaper('legal', 'landscape');
        $pdf->setOption([
            'dpi' => 96,
            'interpolate' => false,
            'isPhpEnabled' => false,
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'Helvetica',
        ]);

        $data = [
            'data'  => $agenda,
            'jenis' => $type,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'range' => $range ?? (!empty($start_date) && !empty($end_date) ? Carbon::createFromFormat('Y-m-d', $start_date)->isoFormat('DD MMMM YYYY') . ' s/d ' . Carbon::createFromFormat('Y-m-d', $end_date)->isoFormat('DD MMMM YYYY') : ''),
        ];
        $pdf->loadView('main.laporan.template_agenda', $data);
        return $pdf;
    }

    /**
     * Export Agenda Surat Masuk / Keluar to Excel (.xlsx).
     */
    public function export_agenda(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $jenis = $request->jenis;

        $fileName = 'Agenda_' . ($jenis ?: 'Semua') . '_' . date('Ymd_His') . '.xlsx';

        if ($jenis === 'Keluar') {
            return Excel::download(new AgendaKeluarExport($startDate, $endDate), $fileName);
        } elseif ($jenis === 'Masuk') {
            return Excel::download(new AgendaMasukExport($startDate, $endDate), $fileName);
        }

        return Excel::download(new AgendaAllExport($startDate, $endDate), $fileName);
    }

    /**
     * Export Statistik Persuratan to Excel (.xlsx).
     */
    public function export_statistik(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $year = intval($request->input('year', date('Y')));
        $fileName = 'Statistik_Persuratan_' . $year . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new StatistikExport($year), $fileName);
    }

    /**
     * Render halaman error ramah pengguna dengan SweetAlert2 dan tutup tab otomatis.
     */
    protected function renderSwalErrorAndClose(string $message): string
    {
        $safeMsg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $cssUrl = asset('templates/plugins/src/sweetalerts2/sweetalerts2.min.css');
        $jsUrl = asset('templates/plugins/src/sweetalerts2/sweetalerts2.min.js');

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Peringatan Cetak Laporan</title>
    <link rel="stylesheet" href="{$cssUrl}">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body>
    <script src="{$jsUrl}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "warning",
                title: "Peringatan",
                text: "{$safeMsg}",
                confirmButtonText: "Tutup Jendela",
                confirmButtonColor: "#4361ee"
            }).then(function() {
                window.close();
            });
        });
    </script>
</body>
</html>
HTML;
    }
}
