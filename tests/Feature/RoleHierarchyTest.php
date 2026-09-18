<?php

namespace Tests\Feature;

use App\Models\Disposisi;
use App\Models\Inbox;
use App\Models\LevelUser;
use App\Models\Outbox;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoleHierarchyTest extends TestCase
{
    /**
     * Test that Bupati and Sekda have top-down and bottom-up daftar_terusan.
     */
    public function test_bupati_and_sekda_have_aligned_daftar_terusan(): void
    {
        $bupatiLevel = LevelUser::find(8);
        $this->assertNotNull($bupatiLevel);
        $this->assertTrue((bool) $bupatiLevel->tindak_lanjut);

        $bupatiTerusan = json_decode($bupatiLevel->daftar_terusan, true);
        $this->assertIsArray($bupatiTerusan);
        // Bupati can forward to Sekda (3) and Asisten (4, 5, 6)
        $this->assertContains(3, $bupatiTerusan);
        $this->assertContains(4, $bupatiTerusan);
        $this->assertContains(5, $bupatiTerusan);
        $this->assertContains(6, $bupatiTerusan);

        $sekdaLevel = LevelUser::find(3);
        $this->assertNotNull($sekdaLevel);
        $this->assertTrue((bool) $sekdaLevel->tindak_lanjut);

        $sekdaTerusan = json_decode($sekdaLevel->daftar_terusan, true);
        $this->assertIsArray($sekdaTerusan);
        // Sekda can forward up to Bupati (8) and down to Asisten 1, 2, 3 (4, 5, 6) and Bagian Umum (2)
        $this->assertContains(8, $sekdaTerusan);
        $this->assertContains(4, $sekdaTerusan);
        $this->assertContains(5, $sekdaTerusan);
        $this->assertContains(6, $sekdaTerusan);
        $this->assertContains(2, $sekdaTerusan);
    }

    /**
     * Test that Asisten 1, 2, 3 have tindak_lanjut enabled.
     */
    public function test_asisten_levels_have_tindak_lanjut_enabled(): void
    {
        foreach ([4, 5, 6] as $asistenId) {
            $level = LevelUser::find($asistenId);
            $this->assertNotNull($level);
            $this->assertTrue((bool) $level->tindak_lanjut);
            $terusan = json_decode($level->daftar_terusan, true);
            $this->assertIsArray($terusan);
            $this->assertContains(3, $terusan, "Asisten {$asistenId} should be able to coordinate back to Sekda");
        }
    }

    /**
     * Test that Sekda retains tracking visibility after forwarding a letter to Asisten.
     */
    public function test_sekda_retains_tracking_visibility_after_forwarding(): void
    {
        Notification::fake();

        $sekdaUser = User::where('level', 3)->first();
        if (!$sekdaUser) {
            $sekdaUser = User::create([
                'uuid'         => Str::uuid()->toString(),
                'nama_lengkap' => 'Pj. Sekda Test',
                'username'     => 'sekda_test_' . rand(1000, 9999),
                'email'        => 'sekda_test_' . rand(1000, 9999) . '@example.com',
                'password'     => Hash::make('password123'),
                'level'        => 3,
                'blokir'       => 'N',
            ]);
            $sekdaUser->assignRole('setda');
        }

        $asistenUser = User::where('level', 4)->first();
        if (!$asistenUser) {
            $asistenUser = User::create([
                'uuid'         => Str::uuid()->toString(),
                'nama_lengkap' => 'Asisten 1 Test',
                'username'     => 'asisten_test_' . rand(1000, 9999),
                'email'        => 'asisten_test_' . rand(1000, 9999) . '@example.com',
                'password'     => Hash::make('password123'),
                'level'        => 4,
                'blokir'       => 'N',
            ]);
            $asistenUser->assignRole('asisten1');
        }

        // Create a test Inbox letter currently at Sekda position
        $inbox = Inbox::create([
            'uuid'              => Str::uuid()->toString(),
            'nama_berkas'       => 'Berkas Uji Tracking',
            'tgl_diterima'      => Carbon::now()->format('Y-m-d'),
            'tgl_surat'         => Carbon::now()->format('Y-m-d'),
            'dari'              => 'Pengirim Uji',
            'wilayah'           => 'Karanganyar',
            'perihal'           => 'Uji Tracking Disposisi Sekda ' . rand(1000, 9999),
            'isi_surat'         => 'Isi surat uji tracking',
            'year'              => intval(date('Y')),
            'no_agenda'         => rand(90000, 99999),
            'no_surat'          => 'TEST/TRACK/' . rand(1000, 9999),
            'tindakan'          => 'non balas',
            'status_surat'      => 'diproses',
            'is_primary_agenda' => true,
            'created_by'        => $sekdaUser->uuid,
            'level_surat'       => 9, // created originally by TU
            'posisi_surat'      => $sekdaUser->uuid,
            'posisi_level'      => 3, // currently at Sekda
        ]);

        // Sekda forwards the letter to Asisten 1 (level 4)
        $forwardResponse = $this->actingAs($sekdaUser)->postJson('/surat-masuk/diteruskan', [
            'uid'    => Crypt::encryptString($inbox->uuid),
            'tujuan' => 4,
        ]);
        $forwardResponse->assertStatus(200);
        $forwardResponse->assertJson(['status' => 'success']);

        $inbox->refresh();
        $this->assertEquals(4, $inbox->posisi_level);

        // Verify that Sekda CAN STILL SEE the letter in serverside datatable
        $ssrResponse = $this->actingAs($sekdaUser)->getJson('/surat-masuk/daftar-surat?start=0&length=10');
        $ssrResponse->assertStatus(200);

        $dataRows = $ssrResponse->json('data');
        $found = false;
        foreach ($dataRows as $row) {
            if ($row['nomor'] === $inbox->no_surat) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Sekda should retain tracking visibility for the forwarded letter');

        // Clean up
        Disposisi::where('uid_surat', $inbox->uuid)->delete();
        $inbox->delete();
    }

    /**
     * Test that Asisten can reply (give disposition notes) with graceful pimpinan fallback.
     */
    public function test_asisten_can_reply_with_graceful_pimpinan_fallback(): void
    {
        $asistenUser = User::where('level', 4)->first();
        if (!$asistenUser) {
            $asistenUser = User::create([
                'uuid'         => Str::uuid()->toString(),
                'nama_lengkap' => 'Asisten 1 Reply Test',
                'username'     => 'asisten_reply_' . rand(1000, 9999),
                'email'        => 'asisten_reply_' . rand(1000, 9999) . '@example.com',
                'password'     => Hash::make('password123'),
                'level'        => 4,
                'blokir'       => 'N',
            ]);
            $asistenUser->assignRole('asisten1');
        }

        // Create an Inbox and an active Disposisi record assigned to Asisten
        $inbox = Inbox::create([
            'uuid'              => Str::uuid()->toString(),
            'nama_berkas'       => 'Berkas Reply Test',
            'tgl_diterima'      => Carbon::now()->format('Y-m-d'),
            'tgl_surat'         => Carbon::now()->format('Y-m-d'),
            'dari'              => 'Instansi Pengirim',
            'wilayah'           => 'Karanganyar',
            'perihal'           => 'Perihal Uji Reply ' . rand(1000, 9999),
            'isi_surat'         => 'Isi surat',
            'year'              => intval(date('Y')),
            'no_agenda'         => rand(90000, 99999),
            'no_surat'          => 'REPLY/TEST/' . rand(1000, 9999),
            'tindakan'          => 'non balas',
            'status_surat'      => 'diproses',
            'is_primary_agenda' => true,
            'created_by'        => $asistenUser->uuid,
            'level_surat'       => 9,
            'posisi_surat'      => $asistenUser->uuid,
            'posisi_level'      => 4,
        ]);

        $senderUser = User::where('id', '!=', $asistenUser->id)->first();
        $this->assertNotNull($senderUser);

        $dispo = Disposisi::create([
            'uid_disposisi' => Str::uuid()->toString(),
            'uid_surat'     => $inbox->uuid,
            'pengirim_uuid' => $senderUser->uuid,
            'penerima_uuid' => $asistenUser->uuid,
            'is_completed'  => false,
        ]);

        // Asisten replies with notes
        $replyResponse = $this->actingAs($asistenUser)->postJson('/surat-masuk/tanggapi', [
            'uid'   => Crypt::encryptString($inbox->uuid),
            'notes' => 'Telah ditindaklanjuti dan dikoordinasikan.',
        ]);

        $replyResponse->assertStatus(200);
        $replyResponse->assertJson(['status' => 'success']);

        $dispo->refresh();
        $this->assertTrue((bool) $dispo->is_completed);
        $this->assertEquals('Telah ditindaklanjuti dan dikoordinasikan.', $dispo->catatan_disposisi);

        // Clean up
        $dispo->delete();
        $inbox->delete();
    }

    /**
     * Test that Outbox datatable action buttons contain Edit button.
     */
    public function test_outbox_datatable_contains_edit_button(): void
    {
        $admin = User::role('administrator')->first();
        if (!$admin) {
            $this->markTestSkipped('No administrator found.');
        }

        // Create a test Outbox record
        $outbox = Outbox::create([
            'uuid'              => Str::uuid()->toString(),
            'nama_berkas'       => 'Berkas Outbox Test',
            'tgl_surat'         => Carbon::now()->format('Y-m-d'),
            'tgl_naik'          => Carbon::now()->format('Y-m-d'),
            'tgl_diteruskan'    => Carbon::now()->format('Y-m-d'),
            'kepada'            => 'Penerima Surat Keluar',
            'wilayah'           => 'Karanganyar',
            'perihal'           => 'Perihal Surat Keluar ' . rand(1000, 9999),
            'isi_surat'         => 'Isi surat keluar',
            'year'              => intval(date('Y')),
            'no_agenda'         => rand(90000, 99999),
            'no_surat'          => 'OUT/TEST/' . rand(1000, 9999),
            'is_primary_agenda' => true,
            'created_by'        => $admin->uuid,
            'level_surat'       => $admin->level,
        ]);

        $response = $this->actingAs($admin)->getJson('/surat-keluar/daftar-surat?start=0&length=10');
        $response->assertStatus(200);

        $dataRows = $response->json('data');
        $foundRow = null;
        foreach ($dataRows as $row) {
            if ($row['nomor'] === $outbox->no_surat) {
                $foundRow = $row;
                break;
            }
        }

        $this->assertNotNull($foundRow);
        $this->assertStringContainsString('title="Edit Surat"', $foundRow['option']);
        $this->assertStringContainsString('btn-outline-warning', $foundRow['option']);

        // Clean up
        $outbox->delete();
    }
}
