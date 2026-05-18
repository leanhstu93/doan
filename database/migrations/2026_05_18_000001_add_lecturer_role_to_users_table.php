<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'lecturer', 'gvhd', 'gvpb', 'student') NOT NULL");
        DB::table('users')
            ->whereIn('role', ['gvhd', 'gvpb'])
            ->update(['role' => 'lecturer']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'lecturer')
            ->update(['role' => 'gvhd']);

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'gvhd', 'gvpb', 'student') NOT NULL");
    }
};
