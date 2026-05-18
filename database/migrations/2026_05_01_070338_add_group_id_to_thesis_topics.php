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
        Schema::table('thesis_topics', function (Blueprint $table) {
            // Thêm group_id để liên kết đề tài với nhóm (nullable vì admin có thể tạo đề tài trước)
            $table->foreignId('group_id')->nullable()->constrained('thesis_groups')->after('id');
            // Thêm submitted_by để biết ai nhập đề tài
            $table->foreignId('submitted_by')->nullable()->constrained('users')->after('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_topics', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['submitted_by']);
            $table->dropColumn(['group_id', 'submitted_by']);
        });
    }
};
