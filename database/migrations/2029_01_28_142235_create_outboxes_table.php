<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outboxes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('no_agenda');
            $table->string('nama_berkas')->nullable();
            $table->string('no_surat');
            $table->string('kepada');
            $table->string('wilayah');
            $table->string('perihal');
            $table->text('isi_surat');
            $table->date('tgl_surat');
            $table->date('tgl_naik')->nullable();
            $table->date('tgl_diteruskan')->nullable();
            $table->integer('year');
            
            $table->foreignId('id_media')->nullable()->constrained('media_surats')->cascadeOnUpdate()->nullOnDelete();
            
            $table->foreignId('id_klasifikasi')->nullable()->constrained('klasifikasis')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('id_unit')->nullable()->constrained('data_units')->cascadeOnUpdate()->nullOnDelete();
            $table->string('unit')->nullable();

            $table->foreignId('sifat_surat')->nullable()->constrained('sifat_surats')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('tempat_berkas')->nullable()->constrained('tempat_berkas')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('id_perkembangan')->nullable()->constrained('perkembangans')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('id_spd')->nullable()->constrained('spds')->cascadeOnUpdate()->nullOnDelete();

            // $table->enum('tindakan', ['balas', 'non balas']);
            $table->string('lampiran', '100')->nullable();
            $table->text('softcopy')->nullable();
            $table->text('file_download')->nullable();
            $table->text('keterangan')->nullable();

            $table->foreignId('level_surat')->nullable()->constrained('level_users')->cascadeOnUpdate()->nullOnDelete();

            $table->boolean('is_primary_agenda'); // untuk membedakan no. urut dari no_agenda

            $table->uuid('created_by');
            $table->foreign('created_by')->references('uuid')->on('users');
            
            $table->dateTime('on_delete')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outboxes');
    }
};
