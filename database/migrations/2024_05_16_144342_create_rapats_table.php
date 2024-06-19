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
        Schema::create('rapat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('kategori')->nullable();
            $table->string('tanggal')->nullable();
            $table->string('urgensi')->nullable();
            $table->string('waktu')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('metode')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('pimpinan')->nullable();
            $table->string('jenis')->nullable();
            $table->string('pemapar')->nullable();
            $table->string('tautan')->nullable();
            $table->string('catatan')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('tema')->nullable();
            $table->index('tema', 'index_tema');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapat');
    }
};
