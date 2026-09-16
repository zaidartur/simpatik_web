<?php

namespace App\Console\Commands;

use App\Models\Klasifikasi;
use App\Models\LevelUser;
use App\Models\Outbox;
use App\Models\Perkembangan;
use App\Models\SifatSurat;
use App\Models\Spd;
use App\Models\TempatBerkas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data persuratan dari tabel legacy aktif ke outbox/inbox';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses migrasi data legacy...');

        ini_set('max_execution_time', 3600);

        if (!DB::getSchemaBuilder()->hasTable('aktif')) {
            $this->warn('Tabel legacy "aktif" tidak ditemukan di database saat ini.');
            return Command::SUCCESS;
        }

        $totalMigrated = 0;

        DB::table('aktif')->where('JENISSURAT', 'Keluar')->orderBy('NO')->chunk(100, function ($datas) use (&$totalMigrated) {
            foreach ($datas as $value) {
                $klas   = Klasifikasi::where('klas3', $value->KLAS3)->first();
                $sifat  = SifatSurat::where('nama_sifat', $value->SIFAT_SURAT)->first();
                $tempat = TempatBerkas::where('nama', $value->TMPTBERKAS)->first();
                $ip     = Perkembangan::where('nama', $value->TK_PERKEMBANGAN)->first();
                $spd    = Spd::where('no_spd', $value->nosppd)->first();
                $level  = LevelUser::where('nama', $value->Posisi)->first();

                if ($level && $level->id) {
                    $user = User::where('level', $level->id)->first();
                    $save = new Outbox();
                    $save->uuid         = Str::uuid7();
                    $save->no_agenda    = intval($value->NOAGENDA);
                    $save->nama_berkas  = $value->NAMABERKAS;
                    $save->no_surat     = $value->NOSURAT;
                    $save->kepada       = $value->drkpd;
                    $save->wilayah      = $value->WILAYAH;
                    $save->perihal      = $value->PERIHAL;
                    $save->isi_surat    = $value->ISI;
                    $save->tgl_surat    = date_format(date_create($value->TGLSURAT), 'Y-m-d');
                    $save->year         = intval($value->TAHUN);
                    $save->id_media     = 1;

                    if ($klas && $klas->id) {
                        $save->id_klasifikasi = $klas->id;
                    }
                    if ($sifat && $sifat->id) {
                        $save->sifat_surat = $sifat->id;
                    }
                    if ($tempat && $tempat->id) {
                        $save->tempat_berkas = $tempat->id;
                    }
                    if ($ip && $ip->id) {
                        $save->id_perkembangan = $ip->id;
                    }
                    if ($spd && $spd->id) {
                        $save->id_spd = $spd->id;
                    }

                    $save->tindakan     = 'non balas';
                    $save->level_surat  = $level->id;
                    $save->is_primary_agenda = true;
                    $save->created_by   = $user ? $user->uuid : null;
                    $save->created_at   = Carbon::parse($value->TGLENTRY . ' ' . $value->JAM)->format('Y-m-d H:i:s');

                    if ($save->save()) {
                        $totalMigrated++;
                    }
                }
            }
            $this->output->write('.');
        });

        $this->newLine();
        $this->info("Migrasi selesai. Total baris berhasil dimigrasikan: {$totalMigrated}");

        return Command::SUCCESS;
    }
}
