<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('badge')->unique()->after('id');
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerja');
            $table->foreignId('pic_unit_kerja_id')->nullable()->constrained('users');
            
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['badge', 'unit_kerja_id', 'pic_unit_kerja_id', 'role_id', 'status']);
        });
    }
};