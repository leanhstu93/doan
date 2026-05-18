<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'lecturer')
            ->whereNotNull('email')
            ->update(['username' => DB::raw('email')]);

        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn('lecturer_code');
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('lecturer_code', 20)->nullable()->unique()->after('user_id');
        });
    }
};
