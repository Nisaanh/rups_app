<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keputusan_id')->constrained('keputusan')->onDelete('cascade');
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja');
            $table->foreignId('pic_unit_kerja_id')->constrained('users');
            $table->date('tanggal_terbit');
            $table->text('strategi');
            $table->enum('status', ['draft', 'dikirim'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arahan');
    }
};