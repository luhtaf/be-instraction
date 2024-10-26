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
        Schema::create('peserta', function (Blueprint $table) {
            // $table->id();
            // $table->unsignedBigInteger('rapat_id'); // Foreign key
            // $table->foreign('rapat_id')->references('id')->on('rapat')->onDelete('cascade');
            $table->uuid('id')->primary();
            $table->uuid('rapat_id');
            // $table->foreignUuid('rapat_id')->references('id')->on('rapat')->onDelete('cascade')->constrained();
            $table->string('nama');
            $table->string('keterangan')->nullable();
            $table->string('perwakilan')->nullable();
            $table->string('jenis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
