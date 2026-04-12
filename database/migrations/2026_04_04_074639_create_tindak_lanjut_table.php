<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arahan_id')->constrained('arahan')->onDelete('cascade');
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja');
            $table->integer('periode_bulan');
            $table->integer('periode_tahun');
            $table->text('tindak_lanjut');
            $table->text('kendala')->nullable();
            $table->string('evidence_url')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['pending', 'in_approval', 'approved', 'rejected', 'td'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};