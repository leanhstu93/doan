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
        Schema::create('thesis_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('thesis_groups')->onDelete('cascade');
            $table->foreignId('student_id')->unique()->constrained('students')->onDelete('cascade'); // Mỗi SV chỉ thuộc 1 nhóm
            $table->boolean('is_leader')->default(false); // Trưởng nhóm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_group_members');
    }
};
