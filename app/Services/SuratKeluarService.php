<?php

namespace App\Services;

use App\Models\DataUnit;
use App\Models\Klasifikasi;
use App\Models\Outbox;
use App\Models\Sppd;
use App\Models\User;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuratKeluarService
{
    protected NomorAgendaService $agendaService;
    protected FileUploadService $fileService;

    public function __construct(NomorAgendaService $agendaService, FileUploadService $fileService)
    {
        $this->agendaService = $agendaService;
        $this->fileService = $fileService;
    }

    /**
     * Store new Surat Keluar and optional integrated SPPD.
     */
    public function store(array $data, ?UploadedFile $scanFile, User $user): Outbox
    {
        return DB::transaction(function () use ($data, $scanFile, $user) {
            $isPrimary = (bool) ($user->leveluser->is_primary ?? false);
            $kodeUrut = $this->agendaService->generate('outbox', $isPrimary, $user->level, intval(date('Y')));

            $uuid = Str::uuid7()->toString();

            $file = null;
            if ($scanFile) {
                $file = $this->fileService->upload($scanFile, $uuid, 'suratkeluar');
            }

            $klas = null;
            if (!empty($data['klasifikasi_kode'])) {
                $klas = Klasifikasi::where('klas3', $data['klasifikasi_kode'])->first();
            }

            $unit = null;
            if (!empty($data['kode_up'])) {
                $unit = DataUnit::where('kode', $data['kode_up'])->first();
            }

            $outbox = new Outbox();
            $outbox->uuid            = $uuid;
            $outbox->nama_berkas     = $data['berkas'];
            $outbox->tgl_surat       = Carbon::parse($data['tgl_surat'])->format('Y-m-d');
            $outbox->tgl_naik        = Carbon::parse($data['tgl_naik'])->format('Y-m-d');
            $outbox->tgl_diteruskan  = Carbon::parse($data['tgl_diteruskan'])->format('Y-m-d');
            $outbox->kepada          = $data['darikepada'];
            $outbox->wilayah         = $data['wilayah'];
            $outbox->perihal         = $data['perihal'] ?? '';
            $outbox->isi_surat       = $data['isi'];
            $outbox->id_klasifikasi  = $klas ? $klas->id : null;
            $outbox->no_agenda       = $kodeUrut;
            $outbox->no_surat        = $data['no_surat'] ?? null;
            $outbox->tempat_berkas   = $data['tempat_berkas'] ?? null;
            $outbox->id_perkembangan = $data['perkembangan'] ?? null;
            $outbox->id_unit         = $unit ? $unit->id : null;
            $outbox->unit            = empty($unit) ? ($data['nama_up'] ?? null) : null;
            $outbox->sifat_surat     = $data['sifat_surat'] ?? null;
            $outbox->keterangan      = $data['keterangan'] ?? null;
            $outbox->year            = intval(date('Y'));
            $outbox->softcopy        = $file;

            $outbox->is_primary_agenda = $isPrimary;
            $outbox->created_by      = $user->uuid;
            $outbox->level_surat     = $user->leveluser->id ?? $user->level;

            $outbox->save();

            // Integrated SPPD
            if (!empty($data['sppd'])) {
                $sppd = new Sppd();
                $sppd->no_spd       = $data['sppd'];
                $sppd->nama         = $data['nama'] ?? '';
                $sppd->jabatan      = $data['jabatan'] ?? '';
                $sppd->tujuan       = $data['tujuan'] ?? '';
                $sppd->kendaraan    = $data['kendaraan'] ?? '';
                $sppd->tgl_surat    = Carbon::parse($data['tgl_surat'])->format('Y-m-d');
                $sppd->tgl_berangkat = !empty($data['berangkat']) ? Carbon::parse($data['berangkat'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                $sppd->save();

                $outbox->id_spd = $sppd->id;
                $outbox->save();
            }

            ActivityLogService::log('create', 'surat_keluar', "Membuat surat keluar baru No. Agenda {$outbox->no_agenda}/{$outbox->year} (No. Surat: {$outbox->no_surat})", $outbox, null, $outbox->toArray(), $user);

            return $outbox;
        });
    }

    /**
     * Update existing Surat Keluar.
     */
    public function update(Outbox $outbox, array $data, ?UploadedFile $scanFile): bool
    {
        if ($scanFile) {
            $this->fileService->deleteFile($outbox->softcopy, 'suratkeluar');
            $file = $this->fileService->upload($scanFile, $outbox->uuid, 'suratkeluar');
        } else {
            $file = $outbox->softcopy;
        }

        $oldValues = $outbox->only(['nama_berkas', 'perihal', 'kepada', 'no_surat', 'sifat_surat', 'isi_surat']);

        $outbox->nama_berkas     = $data['berkas'];
        $outbox->tgl_surat       = Carbon::parse($data['tgl_surat'])->format('Y-m-d');
        $outbox->tgl_naik        = Carbon::parse($data['tgl_naik'])->format('Y-m-d');
        $outbox->tgl_diteruskan  = Carbon::parse($data['tgl_diteruskan'])->format('Y-m-d');
        $outbox->kepada          = $data['darikepada'];
        $outbox->wilayah         = $data['wilayah'];
        $outbox->perihal         = $data['perihal'] ?? '';
        $outbox->isi_surat       = $data['isi'];
        $outbox->id_klasifikasi  = $data['klasifikasi_kode'] ?? $outbox->id_klasifikasi;
        $outbox->no_surat        = $data['no_surat'] ?? $outbox->no_surat;
        $outbox->tempat_berkas   = $data['tempat_berkas'] ?? $outbox->tempat_berkas;
        $outbox->id_perkembangan = $data['perkembangan'] ?? $outbox->id_perkembangan;
        $outbox->sifat_surat     = $data['sifat_surat'] ?? $outbox->sifat_surat;
        $outbox->keterangan      = $data['keterangan'] ?? $outbox->keterangan;
        $outbox->softcopy        = $file;

        $saved = $outbox->save();
        if ($saved) {
            ActivityLogService::log('update', 'surat_keluar', "Memperbarui surat keluar No. Agenda {$outbox->no_agenda}/{$outbox->year}", $outbox, $oldValues, $outbox->toArray());
        }

        return $saved;
    }

    /**
     * Soft delete Surat Keluar by setting on_delete timestamp.
     */
    public function destroy(Outbox $outbox): bool
    {
        $outbox->on_delete = Carbon::now();
        $saved = $outbox->save();
        if ($saved) {
            ActivityLogService::log('delete', 'surat_keluar', "Menghapus surat keluar No. Agenda {$outbox->no_agenda}/{$outbox->year} (No. Surat: {$outbox->no_surat})", $outbox);
        }
        return $saved;
    }
}
