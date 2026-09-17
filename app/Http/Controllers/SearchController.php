<?php

namespace App\Http\Controllers;

use App\Models\Inbox;
use App\Models\LevelUser;
use App\Models\Outbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Perform global search across Inbox and Outbox.
     */
    public function search(Request $request): View
    {
        $term = trim($request->input('q', ''));
        $filterType = $request->input('type', 'all');
        $user = Auth::user();
        $isAdmin = $user->hasRole(['administrator', 'admin']);
        $level = LevelUser::where('id', $user->level)->first();
        $akses = $level ? $level->akses : [$user->level];

        $inboxResults = collect();
        $outboxResults = collect();

        // Search Surat Masuk if user has permission
        if (($user->can('lihat surat masuk') || $user->can('surat masuk') || $isAdmin) && in_array($filterType, ['all', 'masuk'])) {
            $inboxQuery = Inbox::with(['posisi:id,uuid,nama_lengkap,level', 'sifat', 'klasifikasi'])
                ->whereNull('on_delete');

            if (!$isAdmin) {
                $inboxQuery->where(function ($q) use ($akses, $user) {
                    $q->whereIn('posisi_level', $akses)
                      ->orWhere('posisi_surat', $user->uuid)
                      ->orWhere('created_by', $user->uuid);
                });
            }

            if (!empty($term)) {
                $inboxQuery->where(function ($q) use ($term) {
                    $q->where('no_surat', 'ilike', "%{$term}%")
                      ->orWhere('perihal', 'ilike', "%{$term}%")
                      ->orWhere('isi_surat', 'ilike', "%{$term}%")
                      ->orWhere('dari', 'ilike', "%{$term}%")
                      ->orWhere('wilayah', 'ilike', "%{$term}%")
                      ->orWhere('nama_berkas', 'ilike', "%{$term}%");

                    if (is_numeric($term)) {
                        $q->orWhere('no_agenda', intval($term));
                    }
                });
            }

            $inboxResults = $inboxQuery->orderBy('created_at', 'desc')->limit(50)->get()->map(function ($item) {
                $item->search_type = 'masuk';
                return $item;
            });
        }

        // Search Surat Keluar if user has permission
        if (($user->can('lihat surat keluar') || $user->can('surat keluar') || $isAdmin) && in_array($filterType, ['all', 'keluar'])) {
            $outboxQuery = Outbox::with(['sifat', 'klasifikasi'])
                ->whereNull('on_delete');

            if (!$isAdmin) {
                $outboxQuery->where(function ($q) use ($akses, $user) {
                    $q->whereIn('level_surat', $akses)
                      ->orWhere('created_by', $user->uuid);
                });
            }

            if (!empty($term)) {
                $outboxQuery->where(function ($q) use ($term) {
                    $q->where('no_surat', 'ilike', "%{$term}%")
                      ->orWhere('perihal', 'ilike', "%{$term}%")
                      ->orWhere('isi_surat', 'ilike', "%{$term}%")
                      ->orWhere('kepada', 'ilike', "%{$term}%")
                      ->orWhere('wilayah', 'ilike', "%{$term}%")
                      ->orWhere('nama_berkas', 'ilike', "%{$term}%");

                    if (is_numeric($term)) {
                        $q->orWhere('no_agenda', intval($term));
                    }
                });
            }

            $outboxResults = $outboxQuery->orderBy('created_at', 'desc')->limit(50)->get()->map(function ($item) {
                $item->search_type = 'keluar';
                return $item;
            });
        }

        $allResults = $inboxResults->concat($outboxResults)->sortByDesc('created_at');

        return view('main.search.index', [
            'term'         => $term,
            'filterType'   => $filterType,
            'results'      => $allResults,
            'countMasuk'   => $inboxResults->count(),
            'countKeluar'  => $outboxResults->count(),
            'totalCount'   => $allResults->count(),
        ]);
    }
}
