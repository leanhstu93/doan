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
        Schema::create('submission_phases', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên giai đoạn: "Đầu kỳ", "Giữa kỳ", "Cuối kỳ - Trước bảo vệ", "Cuối kỳ - Sau bảo vệ"
            $table->string('code')->unique(); // phase_1, phase_2, phase_3a, phase_3b
            $table->text('description')->nullable();
            $table->string('allowed_file_types')->default('word,pdf,ppt'); // Các loại file cho phép
            $table->boolean('is_active')->default(true);
            $table->timestamp('open_date')->nullable(); // Ngày mở nộp
            $table->timestamp('close_date')->nullable(); // Ngày đóng nộp
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_phases');
    }
};
