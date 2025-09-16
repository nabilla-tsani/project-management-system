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
        Schema::create('proyek_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique();
            $table->foreignId('proyek_id')->constrained('proyek');
            $table->string('judul_invoice');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_invoice');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['belum_dibayar', 'diproses', 'dibayar']);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_invoice');
    }
};
