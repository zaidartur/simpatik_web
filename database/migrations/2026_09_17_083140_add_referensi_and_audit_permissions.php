<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $p1 = Permission::findOrCreate('referensi', 'web');
        $p2 = Permission::findOrCreate('audit log', 'web');

        $admin = Role::where('name', 'administrator')->first();
        if ($admin) {
            $admin->givePermissionTo([$p1, $p2]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', ['referensi', 'audit log'])->delete();
    }
};
