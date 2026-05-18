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
        Schema::create('file_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('thesis_groups')->onDelete('cascade');
            $table->foreignId('phase_id')->constrained('submission_phases')->onDelete('cascade');
            $table->enum('file_type', ['word', 'pdf', 'ppt', 'other'])->default('word');
            $table->string('file_path', 500); // Đường dẫn file trên server
            $table->string('original_name', 255); // Tên file gốc khi upload
            $table->enum('status', ['pending', 'approved', 'rejected', 're_submitted'])->default('pending');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade'); // Sinh viên nộp
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Admin/GVHD duyệt
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable(); // Ghi chú khi duyệt/từ chối
            $table->text('rejection_reason')->nullable(); // Lý do từ chối
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_submissions');
    }
};
