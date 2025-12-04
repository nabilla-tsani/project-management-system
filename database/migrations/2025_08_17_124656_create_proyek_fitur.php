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
        Schema::create('proyek_fitur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek');
            $table->string('nama_fitur');
            $table->text('keterangan')->nullable();
            $table->text('target')->nullable();
            $table->string('status_fitur')->default('belum_dikerjakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_fitur');
    }
};
