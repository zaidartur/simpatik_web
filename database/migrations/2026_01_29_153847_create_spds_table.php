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
        Schema::create('spds', function (Blueprint $table) {
            $table->id();
            $table->string('no_spd', '100');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('tujuan');
            $table->string('kendaraan');
            $table->date('tgl_surat');
            $table->date('tgl_berangkat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spds');
    }
};
