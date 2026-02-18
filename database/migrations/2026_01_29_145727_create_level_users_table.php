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
        Schema::create('level_users', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('nama');
            $table->json('akses')->nullable();
            $table->string('warna')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('tindak_lanjut')->default(0);
            $table->json('daftar_terusan')->nullable();
            $table->boolean('can_disposisi')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_users');
    }
};
