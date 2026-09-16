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
            $table->index('status_surat', 'inboxes_status_surat_idx');
            $table->index('year', 'inboxes_year_idx');
            $table->index('level_surat', 'inboxes_level_surat_idx');
            $table->index('posisi_level', 'inboxes_posisi_level_idx');
            $table->index('on_delete', 'inboxes_on_delete_idx');
            $table->index(['year', 'level_surat'], 'inboxes_year_level_surat_idx');
        });

        Schema::table('outboxes', function (Blueprint $table) {
            $table->index('year', 'outboxes_year_idx');
            $table->index('level_surat', 'outboxes_level_surat_idx');
            $table->index('on_delete', 'outboxes_on_delete_idx');
            $table->index(['year', 'level_surat'], 'outboxes_year_level_surat_idx');
        });

        Schema::table('disposisis', function (Blueprint $table) {
            $table->index('uid_surat', 'disposisis_uid_surat_idx');
            $table->index('is_completed', 'disposisis_is_completed_idx');
            $table->index('penerima_uuid', 'disposisis_penerima_uuid_idx');
            $table->index(['uid_surat', 'is_completed'], 'disposisis_uid_surat_completed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inboxes', function (Blueprint $table) {
            $table->dropIndex('inboxes_status_surat_idx');
            $table->dropIndex('inboxes_year_idx');
            $table->dropIndex('inboxes_level_surat_idx');
            $table->dropIndex('inboxes_posisi_level_idx');
            $table->dropIndex('inboxes_on_delete_idx');
            $table->dropIndex('inboxes_year_level_surat_idx');
        });

        Schema::table('outboxes', function (Blueprint $table) {
            $table->dropIndex('outboxes_year_idx');
            $table->dropIndex('outboxes_level_surat_idx');
            $table->dropIndex('outboxes_on_delete_idx');
            $table->dropIndex('outboxes_year_level_surat_idx');
        });

        Schema::table('disposisis', function (Blueprint $table) {
            $table->dropIndex('disposisis_uid_surat_idx');
            $table->dropIndex('disposisis_is_completed_idx');
            $table->dropIndex('disposisis_penerima_uuid_idx');
            $table->dropIndex('disposisis_uid_surat_completed_idx');
        });
    }
};
