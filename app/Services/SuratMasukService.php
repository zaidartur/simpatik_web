<?php

namespace App\Services;

use App\Models\Disposisi;
use App\Models\Inbox;
use App\Models\Klasifikasi;
use App\Models\Pimpinan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuratMasukService
{
    protected NomorAgendaService $agendaService;
    protected FileUploadService $fileService;

    public function __construct(NomorAgendaService $agendaService, FileUploadService $fileService)
    {
        $this->agendaService = $agendaService;
        $this->fileService = $fileService;
    }

    /**
     * Store new Surat Masuk.
     */
    public function store(array $data, ?UploadedFile $scanFile, User $user): Inbox
    {
        return DB::transaction(function () use ($data, $scanFile, $user) {
            $isPrimary = (bool) ($user->leveluser->is_primary ?? false);
            $kodeUrut = $this->agendaService->generate('inbox', $isPrimary, $user->level, intval(date('Y')));

            $uuid = Str::uuid7()->toString();

            $file = null;
            if ($scanFile) {
                $file = $this->fileService->upload($scanFile, $uuid, 'suratmasuk');
            }

            $klas = null;
            if (!empty($data['klasifikasi_kode'])) {
                $klas = Klasifikasi::where('klas3', $data['klasifikasi_kode'])->first();
            }

            $inbox = new Inbox();
            $inbox->uuid            = $uuid;
            $inbox->nama_berkas     = $data['berkas'];
            $inbox->tgl_diterima    = Carbon::parse($data['tgl_terima'])->format('Y-m-d');
            $inbox->tgl_surat       = Carbon::parse($data['tgl_surat'])->format('Y-m-d');
            $inbox->dari            = $data['darikepada'];
            $inbox->wilayah         = $data['wilayah'];
            $inbox->perihal         = $data['perihal'];
            $inbox->isi_surat       = $data['isi'];
            $inbox->year            = intval(date('Y'));
            $inbox->id_klasifikasi  = $klas ? $klas->id : null;
            $inbox->no_agenda       = $kodeUrut;
            $inbox->no_surat        = $data['no_surat'];
            $inbox->tempat_berkas   = $data['tempat_berkas'] ?? null;
            $inbox->id_perkembangan = $data['perkembangan'] ?? null;
            $inbox->sifat_surat     = $data['sifat_surat'] ?? null;
            $inbox->tindakan        = $data['tindakan'] ?? 'non balas';
            $inbox->id_media        = 1;
            $inbox->tgl_balas       = (isset($data['tindakan']) && $data['tindakan'] === 'non balas') ? null : (!empty($data['tgl_balas']) ? Carbon::parse($data['tgl_balas'])->format('Y-m-d') : null);
            $inbox->status_surat    = (!empty($data['is_diteruskan']) && $data['is_diteruskan'] === 'yes') ? 'diproses' : 'selesai';
            $inbox->softcopy        = $file;

            if (empty($user->leveluser->tindak_lanjut)) {
                $inbox->keterangan = $data['keterangan'] ?? null;
            }

            $inbox->is_primary_agenda = $isPrimary;
            $inbox->created_by      = $user->uuid;
            $inbox->level_surat     = $user->leveluser->id ?? $user->level;
            $inbox->posisi_surat    = $user->uuid;
            $inbox->posisi_level    = $user->leveluser->id ?? $user->level;

            $inbox->save();

            // Automatic initial forward if requested
            if (!empty($data['is_diteruskan']) && $data['is_diteruskan'] === 'yes' && !empty($data['diteruskan_kpd'])) {
                $rcv = User::where('level', $data['diteruskan_kpd'])->first();
                if ($rcv) {
                    $dispo = new Disposisi();
                    $dispo->uid_disposisi = Str::uuid7()->toString();
                    $dispo->uid_surat     = $uuid;
                    $dispo->pengirim_uuid = $user->uuid;
                    $dispo->penerima_uuid = $rcv->uuid;
                    $dispo->is_completed  = false;
                    $dispo->save();

                    $inbox->posisi_surat = $rcv->uuid;
                    $inbox->posisi_level = $data['diteruskan_kpd'];
                    $inbox->save();
                }
            }

            return $inbox;
        });
    }

    /**
     * Update existing Surat Masuk.
     */
    public function update(Inbox $inbox, array $data, ?UploadedFile $scanFile, User $user): bool
    {
        if ($scanFile) {
            $this->fileService->deleteFile($inbox->softcopy, 'suratmasuk');
            $file = $this->fileService->upload($scanFile, $inbox->uuid, 'suratmasuk');
        } else {
            $file = $inbox->softcopy;
        }

        $inbox->nama_berkas     = $data['berkas'];
        $inbox->tgl_diterima    = Carbon::parse($data['tgl_terima'])->format('Y-m-d');
        $inbox->tgl_surat       = Carbon::parse($data['tgl_surat'])->format('Y-m-d');
        $inbox->dari            = $data['darikepada'];
        $inbox->wilayah         = $data['wilayah'];
        $inbox->perihal         = $data['perihal'];
        $inbox->isi_surat       = $data['isi'];
        $inbox->id_klasifikasi  = $data['klasifikasi_kode'] ?? $inbox->id_klasifikasi;
        $inbox->no_surat        = $data['no_surat'];
        $inbox->tempat_berkas   = $data['tempat_berkas'] ?? $inbox->tempat_berkas;
        $inbox->id_perkembangan = $data['perkembangan'] ?? $inbox->id_perkembangan;
        $inbox->sifat_surat     = $data['sifat_surat'] ?? $inbox->sifat_surat;
        $inbox->tindakan        = $data['tindakan'] ?? $inbox->tindakan;
        $inbox->tgl_balas       = (isset($data['tindakan']) && strtolower($data['tindakan']) === 'non balas') ? null : (!empty($data['tgl_balas']) ? Carbon::parse($data['tgl_balas'])->format('Y-m-d') : null);
        $inbox->softcopy        = $file;

        return $inbox->save();
    }

    /**
     * Soft delete Surat Masuk by setting on_delete timestamp.
     */
    public function destroy(Inbox $inbox): bool
    {
        $inbox->on_delete = Carbon::now();
        return $inbox->save();
    }

    /**
     * Forward (tindak lanjut) Surat Masuk to another jabatan level.
     */
    public function forward(Inbox $inbox, int $targetLevel, User $currentUser): array
    {
        $penerima = User::where('level', $targetLevel)->first();
        if (!$penerima) {
            return ['status' => 'failed', 'message' => 'User penerima pada level tujuan tidak ditemukan.'];
        }

        $check = Disposisi::where('uid_surat', $inbox->uuid)
            ->where('pengirim_uuid', $currentUser->uuid)
            ->exists();

        if ($check) {
            return ['status' => 'failed', 'message' => 'Duplikasi tindak lanjut: Anda sudah pernah meneruskan surat ini.'];
        }

        $dispo = new Disposisi();
        $dispo->uid_disposisi = Str::uuid7()->toString();
        $dispo->uid_surat     = $inbox->uuid;
        $dispo->pengirim_uuid = $currentUser->uuid;
        $dispo->penerima_uuid = $penerima->uuid;
        $dispo->is_completed  = false;

        if ($dispo->save()) {
            $inbox->posisi_level = $targetLevel;
            $inbox->posisi_surat = $penerima->uuid;
            $inbox->save();

            return ['status' => 'success', 'message' => 'Surat berhasil diteruskan.'];
        }

        return ['status' => 'failed', 'message' => 'Surat gagal diteruskan.'];
    }

    /**
     * Reply (memberikan catatan disposisi) on Surat Masuk.
     */
    public function reply(Inbox $inbox, string $notes, User $currentUser): array
    {
        $dispo = Disposisi::where('uid_surat', $inbox->uuid)
            ->where('penerima_uuid', $currentUser->uuid)
            ->where('is_completed', false)
            ->whereNull('on_delete')
            ->first();

        if (!$dispo) {
            return ['status' => 'failed', 'message' => 'Record disposisi belum selesai tidak ditemukan untuk akun Anda.'];
        }

        // Check if subordinate forwards are still pending
        $hasPendingSubordinates = Disposisi::where('uid_surat', $inbox->uuid)
            ->where('pengirim_uuid', $currentUser->uuid)
            ->where('is_completed', false)
            ->exists();

        if ($hasPendingSubordinates) {
            return ['status' => 'failed', 'message' => 'Anda belum bisa memberikan disposisi karena pihak yang Anda teruskan belum memberikan tanggapan.'];
        }

        $pimpinan = Pimpinan::where('level', $currentUser->level)->where('is_default', true)->first();
        if (!$pimpinan) {
            return ['status' => 'failed', 'message' => 'Data pejabat penandatangan default untuk level Anda belum ditentukan.'];
        }

        $dispo->catatan_disposisi = $notes;
        $dispo->id_pimpinan       = $pimpinan->id;
        $dispo->is_completed      = true;

        if ($dispo->save()) {
            $this->checkSuratSelesai($inbox->uuid);
            return ['status' => 'success', 'message' => 'Surat berhasil ditanggapi.'];
        }

        return ['status' => 'failed', 'message' => 'Surat gagal ditanggapi.'];
    }

    /**
     * Check if all disposisi are completed; if so, mark Inbox status as 'selesai'.
     */
    public function checkSuratSelesai(string $uuid): void
    {
        $hasPending = Disposisi::where('uid_surat', $uuid)->where('is_completed', false)->exists();

        if (!$hasPending) {
            Inbox::where('uuid', $uuid)->update(['status_surat' => 'selesai']);
        }
    }
}
