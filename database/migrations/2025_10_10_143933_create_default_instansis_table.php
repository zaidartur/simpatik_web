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
        Schema::create('default_instansis', function (Blueprint $table) {
            $table->id();
            $table->string('uuid_user', 50);
            $table->string('kode_instansi', 10);
            $table->string('nama_instansi', 100);
            $table->string('kode_wilayah', 10);
            $table->string('nama_wilayah', 100);
            $table->string('alamat_instansi', 200);
            $table->string('tahun', 4);
            $table->string('nip', 18)->nullable();
            $table->string('nama_pejabat', 100)->nullable();
            $table->string('ttd', 100)->nullable();
            $table->date('batas_waktu')->nullable();
            $table->string('foto', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_instansis');
    }
};
