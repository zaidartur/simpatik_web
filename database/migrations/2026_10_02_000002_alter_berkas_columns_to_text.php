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
        Schema::table('inboxes', function (Blueprint $table) {
            $table->text('nama_berkas')->nullable()->change();
        });

        Schema::table('outboxes', function (Blueprint $table) {
            $table->text('nama_berkas')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inboxes', function (Blueprint $table) {
            $table->string('nama_berkas', 255)->nullable()->change();
        });

        Schema::table('outboxes', function (Blueprint $table) {
            $table->string('nama_berkas', 255)->nullable()->change();
        });
    }
};
