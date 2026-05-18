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
        Schema::create('thesis_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_code', 50)->unique(); // Mã nhóm
            $table->foreignId('topic_id')->unique()->constrained('thesis_topics'); // Gắn với đề tài
            $table->foreignId('academic_year_id')->constrained('academic_years'); // Khóa học
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_groups');
    }
};
