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
        Schema::create('kelengkapan_post', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary();
            $table->foreignUuid('rapat_id')->references('id')->on('rapat')->onDelete('cascade')->constrained();
            // $table->unsignedBigInteger('rapat_id'); // Foreign key
            // $table->foreign('rapat_id')->references('id')->on('rapat')->onDelete('cascade');
            $table->string('undangan')->nullable();
            $table->string('rekaman')->nullable();
            $table->string('risalah')->nullable();
            $table->string('bahan')->nullable();
            $table->string('absen')->nullable();
            $table->string('laporan')->nullable();
            $table->string('dokumentasi')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelengkapan_post');
    }
};
