<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferensiStoreRequest;
use App\Http\Requests\ReferensiUpdateRequest;
use App\Models\Inbox;
use App\Models\Klasifikasi;
use App\Models\MediaSurat;
use App\Models\Outbox;
use App\Models\Perkembangan;
use App\Models\SifatSurat;
use App\Models\TempatBerkas;
use App\Services\ActivityLogService;
use App\Services\ReferenceCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferensiController extends Controller
{
    protected ReferenceCacheService $cacheService;

    public function __construct(ReferenceCacheService $cacheService)
    {
        $this->middleware(['auth', 'permission:referensi|administrator']);
        $this->cacheService = $cacheService;
    }

    /**
     * Display unified Reference Data view with tabs.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'klasifikasi');

        $klasifikasis = $this->cacheService->getKlasifikasi();
        $sifats = $this->cacheService->getSifatSurat();
        $tempats = $this->cacheService->getTempatBerkas();
        $perkembangans = $this->cacheService->getPerkembangan();
        $medias = $this->cacheService->getMediaSurat();

        return view('main.referensi.index', compact(
            'activeTab',
            'klasifikasis',
            'sifats',
            'tempats',
            'perkembangans',
            'medias'
        ));
    }

    /**
     * Store a newly created reference record.
     */
    public function store(ReferensiStoreRequest $request, string $type): JsonResponse
    {
        $data = $request->validated();
        $created = null;
        $label = '';

        switch ($type) {
            case 'klasifikasi':
                $created = Klasifikasi::create([
                    'klas3'     => $data['klas3'],
                    'masalah3'  => $data['masalah3'],
                    'series'    => $data['series'] ?? null,
                    'r_aktif'   => intval($data['r_aktif']),
                    'r_inaktif' => intval($data['r_inaktif']),
                    'ket_jra'   => $data['ket_jra'] ?? null,
                    'nilai_guna'=> $data['nilai_guna'] ?? null,
                ]);
                $label = "Klasifikasi JRA: {$created->klas3} - {$created->masalah3}";
                break;

            case 'sifat-surat':
                $created = SifatSurat::create(['nama_sifat' => $data['nama_sifat']]);
                $label = "Sifat Surat: {$created->nama_sifat}";
                break;

            case 'tempat-berkas':
                $created = TempatBerkas::create(['nama' => $data['nama']]);
                $label = "Tempat Berkas: {$created->nama}";
                break;

            case 'perkembangan':
                $created = Perkembangan::create(['nama' => $data['nama']]);
                $label = "Tingkat Perkembangan: {$created->nama}";
                break;

            case 'media-surat':
                $created = MediaSurat::create(['nama' => $data['nama']]);
                $label = "Media Surat: {$created->nama}";
                break;

            default:
                return response()->json(['status' => 'failed', 'message' => 'Tipe referensi tidak valid.'], 400);
        }

        ActivityLogService::log('create', 'referensi', "Menambahkan data master {$label}", $created, null, $created->toArray());
        $this->cacheService->forgetByType($type);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil ditambahkan.']);
    }

    /**
     * Update the specified reference record.
     */
    public function update(ReferensiUpdateRequest $request, string $type): JsonResponse
    {
        $data = $request->validated();
        $id = intval($data['id']);
        $label = '';

        switch ($type) {
            case 'klasifikasi':
                $item = Klasifikasi::find($id);
                if (!$item) return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
                $old = $item->toArray();
                $item->update([
                    'klas3'     => $data['klas3'],
                    'masalah3'  => $data['masalah3'],
                    'series'    => $data['series'] ?? null,
                    'r_aktif'   => intval($data['r_aktif']),
                    'r_inaktif' => intval($data['r_inaktif']),
                    'ket_jra'   => $data['ket_jra'] ?? null,
                    'nilai_guna'=> $data['nilai_guna'] ?? null,
                ]);
                $label = "Klasifikasi JRA: {$item->klas3}";
                ActivityLogService::log('update', 'referensi', "Memperbarui data master {$label}", $item, $old, $item->toArray());
                break;

            case 'sifat-surat':
                $item = SifatSurat::find($id);
                if (!$item) return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
                $old = $item->toArray();
                $item->update(['nama_sifat' => $data['nama_sifat']]);
                $label = "Sifat Surat: {$item->nama_sifat}";
                ActivityLogService::log('update', 'referensi', "Memperbarui data master {$label}", $item, $old, $item->toArray());
                break;

            case 'tempat-berkas':
                $item = TempatBerkas::find($id);
                if (!$item) return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
                $old = $item->toArray();
                $item->update(['nama' => $data['nama']]);
                $label = "Tempat Berkas: {$item->nama}";
                ActivityLogService::log('update', 'referensi', "Memperbarui data master {$label}", $item, $old, $item->toArray());
                break;

            case 'perkembangan':
                $item = Perkembangan::find($id);
                if (!$item) return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
                $old = $item->toArray();
                $item->update(['nama' => $data['nama']]);
                $label = "Tingkat Perkembangan: {$item->nama}";
                ActivityLogService::log('update', 'referensi', "Memperbarui data master {$label}", $item, $old, $item->toArray());
                break;

            case 'media-surat':
                $item = MediaSurat::find($id);
                if (!$item) return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
                $old = $item->toArray();
                $item->update(['nama' => $data['nama']]);
                $label = "Media Surat: {$item->nama}";
                ActivityLogService::log('update', 'referensi', "Memperbarui data master {$label}", $item, $old, $item->toArray());
                break;

            default:
                return response()->json(['status' => 'failed', 'message' => 'Tipe referensi tidak valid.'], 400);
        }

        $this->cacheService->forgetByType($type);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui.']);
    }

    /**
     * Remove the specified reference record with relational integrity protection.
     */
    public function destroy(Request $request, string $type): JsonResponse
    {
        $id = intval($request->input('id'));
        if (!$id) {
            return response()->json(['status' => 'failed', 'message' => 'ID data tidak valid.'], 400);
        }

        $inUse = false;
        $label = '';
        $item = null;

        switch ($type) {
            case 'klasifikasi':
                $item = Klasifikasi::find($id);
                $inUse = Inbox::where('id_klasifikasi', $id)->exists() || Outbox::where('id_klasifikasi', $id)->exists();
                $label = 'Klasifikasi JRA';
                break;

            case 'sifat-surat':
                $item = SifatSurat::find($id);
                $inUse = Inbox::where('sifat_surat', $id)->exists() || Outbox::where('sifat_surat', $id)->exists();
                $label = 'Sifat Surat';
                break;

            case 'tempat-berkas':
                $item = TempatBerkas::find($id);
                $inUse = Inbox::where('tempat_berkas', $id)->exists() || Outbox::where('tempat_berkas', $id)->exists();
                $label = 'Tempat Berkas';
                break;

            case 'perkembangan':
                $item = Perkembangan::find($id);
                $inUse = Inbox::where('id_perkembangan', $id)->exists() || Outbox::where('id_perkembangan', $id)->exists();
                $label = 'Tingkat Perkembangan';
                break;

            case 'media-surat':
                $item = MediaSurat::find($id);
                $inUse = Inbox::where('id_media', $id)->exists() || Outbox::where('id_media', $id)->exists();
                $label = 'Media Surat';
                break;

            default:
                return response()->json(['status' => 'failed', 'message' => 'Tipe referensi tidak valid.'], 400);
        }

        if (!$item) {
            return response()->json(['status' => 'failed', 'message' => 'Data tidak ditemukan.'], 404);
        }

        if ($inUse) {
            return response()->json([
                'status'  => 'failed',
                'message' => "Data {$label} tidak dapat dihapus karena sedang digunakan dalam arsip persuratan.",
            ], 422);
        }

        $item->delete();
        ActivityLogService::log('delete', 'referensi', "Menghapus data master {$label} ID #{$id}", $item);
        $this->cacheService->forgetByType($type);

        return response()->json(['status' => 'success', 'message' => "Data {$label} berhasil dihapus."]);
    }
}
