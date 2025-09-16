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
        Schema::create('proyek_catatan_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_fitur_id')->constrained('proyek_fitur');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('jenis', ['pekerjaan', 'bug']);
            $table->text('catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_catatan_pekerjaan');
    }
};
