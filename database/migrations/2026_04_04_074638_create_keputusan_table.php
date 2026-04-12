<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keputusan', function (Blueprint $table) {
            $table->id();
            $table->integer('periode_year');
            $table->enum('status', ['BD', 'BS', 'S', 'TD'])->default('BD');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keputusan');
    }
};