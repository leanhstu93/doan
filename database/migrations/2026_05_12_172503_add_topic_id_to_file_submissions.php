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
        Schema::table('file_submissions', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->after('group_id')->constrained('thesis_topics')->onDelete('cascade');
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_submissions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
            $table->foreignId('group_id')->nullable()->constrained('thesis_groups');
        });
    }
};
