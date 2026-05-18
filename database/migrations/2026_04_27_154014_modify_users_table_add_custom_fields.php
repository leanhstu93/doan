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
        Schema::table('users', function (Blueprint $table) {
            // Rename name to full_name
            $table->renameColumn('name', 'full_name');

            // Add custom fields
            $table->string('username', 50)->unique()->after('id');
            $table->enum('role', ['admin', 'lecturer', 'gvhd', 'gvpb', 'student'])->after('password');
            $table->string('phone', 20)->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            // Make email nullable (SV may not have email yet)
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'phone', 'is_active', 'last_login_at']);
            $table->renameColumn('full_name', 'name');
            $table->string('email')->unique()->change();
        });
    }
};
