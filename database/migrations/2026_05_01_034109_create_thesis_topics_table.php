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
        Schema::create('thesis_topics', function (Blueprint $table) {
            $table->id();
            $table->text('ten_de_tai_tv'); // Tên đề tài tiếng Việt
            $table->text('ten_de_tai_ta')->nullable(); // Tên đề tài tiếng Anh
            $table->foreignId('gvhd_id')->constrained('lecturers'); // Giảng viên hướng dẫn
            $table->foreignId('gvpb_id')->nullable()->constrained('lecturers'); // Giảng viên phản biện
            $table->foreignId('academic_year_id')->constrained('academic_years'); // Khóa học
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trạng thái
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_topics');
    }
};
