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
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid_disposisi')->unique();
            $table->index('uid_disposisi');

            $table->uuid('uid_surat');
            $table->foreign('uid_surat')->references('uuid')->on('inboxes');
            // $table->foreignId('uid_surat')->nullable()->constrained('inboxes')->nullOnDelete();

            $table->uuid('pengirim_uuid'); 
            $table->uuid('penerima_uuid');
    
            $table->index('pengirim_uuid');
            $table->index('penerima_uuid');

            $table->foreign('pengirim_uuid')->references('uuid')->on('users');
            $table->foreign('penerima_uuid')->references('uuid')->on('users');

            $table->text('catatan_disposisi')->nullable();

            $table->foreignId('id_pimpinan')->nullable()->constrained('pimpinans')->nullOnDelete();

            $table->boolean('is_completed')->default(false);
            $table->date('on_delete')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisis');
    }
};
