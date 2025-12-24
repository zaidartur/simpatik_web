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
        Schema::create('duplikats', function (Blueprint $table) {
            $table->id();
            $table->integer('id_surat');
            $table->integer('jumlah');
            $table->string('nomor_surat');
            $table->integer('nomor_awal');
            $table->integer('nomor_akhir');
            $table->json('list');
            $table->integer('tahun');
            $table->text('path_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duplikats');
    }
};
