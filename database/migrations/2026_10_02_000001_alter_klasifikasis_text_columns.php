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
        Schema::table('klasifikasis', function (Blueprint $table) {
            $table->text('masalah1')->nullable()->change();
            $table->text('masalah2')->nullable()->change();
            $table->text('masalah3')->nullable()->change();
            $table->text('series')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasifikasis', function (Blueprint $table) {
            $table->string('masalah1', 255)->nullable()->change();
            $table->string('masalah2', 255)->nullable()->change();
            $table->string('masalah3', 255)->nullable()->change();
            $table->string('series', 255)->nullable()->change();
        });
    }
};
