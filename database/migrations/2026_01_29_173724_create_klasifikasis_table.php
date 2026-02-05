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
        Schema::create('klasifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('klas1')->nullable();
            $table->string('klas2')->nullable();
            $table->string('klas3')->nullable();
            $table->string('masalah1')->nullable();
            $table->string('masalah2')->nullable();
            $table->string('masalah3')->nullable();
            $table->string('series')->nullable();
            $table->integer('r_aktif');
            $table->integer('r_inaktif');
            $table->string('ket_jra')->nullable();
            $table->string('nilai_guna')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klasifikasis');
    }
};
