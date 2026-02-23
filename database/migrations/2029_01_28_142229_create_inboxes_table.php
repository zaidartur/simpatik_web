<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE TYPE status_surat_enum AS ENUM ('diproses', 'selesai')");
        
        Schema::create('inboxes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('no_agenda');
            $table->string('nama_berkas')->nullable();
            $table->string('no_surat');
            $table->string('dari');
            $table->string('wilayah');
            $table->string('perihal');
            $table->text('isi_surat');
            $table->date('tgl_surat');
            $table->date('tgl_diterima');
            $table->integer('year');

            $table->foreignId('id_media')->nullable()->constrained('media_surats')->nullOnDelete()->cascadeOnUpdate();

            $table->foreignId('id_klasifikasi')->nullable()->constrained('klasifikasis')->nullOnDelete()->cascadeOnUpdate();

            // $table->integer('id_unit');
            // $table->foreign('id_unit')->references('id')->on('media_surats');

            $table->foreignId('sifat_surat')->nullable()->constrained('sifat_surats')->nullOnDelete()->cascadeOnUpdate();

            $table->foreignId('tempat_berkas')->nullable()->constrained('tempat_berkas')->nullOnDelete()->cascadeOnUpdate();

            $table->foreignId('id_perkembangan')->nullable()->constrained('perkembangans')->nullOnDelete()->cascadeOnUpdate();

            $table->uuid('posisi_surat');
            $table->foreign('posisi_surat')->references('uuid')->on('users');

            // $table->foreignId('posisi_level')->nullable()->constrained('level_users')->nullOnDelete()->cascadeOnUpdate();
            $table->text('jml_lampiran')->nullable();
            $table->enum('tindakan', ['balas', 'non balas']);
            $table->dateTime('tgl_balas')->nullable();
            $table->text('softcopy')->nullable();
            $table->text('file_download')->nullable();

            $table->foreignId('level_surat')->nullable()->constrained('level_users')->nullOnDelete()->cascadeOnUpdate();

            $table->enum('status_surat', ['diproses', 'selesai'])->default('diproses');
            $table->boolean('is_primary_agenda'); // untuk membedakan no. urut dari no_agenda

            $table->uuid('created_by');
            $table->foreign('created_by')->references('uuid')->on('users');

            $table->foreignId('posisi_level')->nullable()->constrained('level_users')->nullOnDelete()->cascadeOnUpdate();
            
            $table->dateTime('on_delete')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inboxes');

        DB::statement('DROP TYPE IF EXISTS status_surat_enum');
    }
};
