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

            $table->unsignedBigInteger('proyek_fitur_id')->nullable();
            $table->foreign('proyek_fitur_id')
                    ->references('id')
                    ->on('proyek_fitur')
                    ->onDelete('cascade');

            $table->unsignedBigInteger('proyek_id');
            $table->foreign('proyek_id')
                ->references('id')
                ->on('proyek')
                ->onDelete('cascade');
              
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
