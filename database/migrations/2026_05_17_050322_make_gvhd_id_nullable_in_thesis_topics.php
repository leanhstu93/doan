<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL for MySQL to modify column to nullable
        DB::statement('ALTER TABLE `thesis_topics` MODIFY `gvhd_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `thesis_topics` MODIFY `gvhd_id` BIGINT UNSIGNED NOT NULL');
    }
};
