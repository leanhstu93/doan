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
        Schema::table('thesis_groups', function (Blueprint $table) {
            // Xóa foreign key trước
            $table->dropForeign(['topic_id']);
            // Bỏ unique constraint
            $table->dropUnique(['topic_id']);
            // Cho phép null và thêm lại foreign key
            $table->unsignedBigInteger('topic_id')->nullable()->change();
            $table->foreign('topic_id')->references('id')->on('thesis_topics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_groups', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->unsignedBigInteger('topic_id')->required()->change();
            $table->unique('topic_id');
            $table->foreign('topic_id')->references('id')->on('thesis_topics');
        });
    }
};
