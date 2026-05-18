<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('thesis_groups')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('lecturers')->onDelete('cascade');
            $table->enum('reviewer_type', ['gvhd', 'gvpb'])->default('gvpb');
            $table->text('content')->nullable();
            $table->integer('score')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();

            $table->index(['group_id', 'reviewer_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
