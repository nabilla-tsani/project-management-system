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
        Schema::create('proyek_fitur_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proyek_fitur_id');
            $table->foreign('proyek_fitur_id')
                    ->references('id')
                    ->on('proyek_fitur')
                    ->onDelete('cascade');
                    
            $table->foreignId('user_id')->constrained('users');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_fitur_user');
    }
};
