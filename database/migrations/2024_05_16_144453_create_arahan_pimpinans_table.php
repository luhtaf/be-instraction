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
        Schema::create('arahan_pimpinan', function (Blueprint $table) {
            // $table->id();
            // $table->unsignedBigInteger('rapat_id'); // Foreign key
            // $table->foreign('rapat_id')->references('id')->on('rapat')->onDelete('cascade');
            $table->uuid('id')->primary();
            $table->uuid('rapat_id');
            // $table->foreignUuid('rapat_id')->references('id')->on('rapat')->onDelete('cascade')->constrained();
            $table->string('arahan');
            $table->string('deadline')->nullable();
            $table->integer('revisi')->default(0);
            $table->string('batas_konfirmasi')->nullable();
            $table->string('pelaksana')->nullable();
            $table->string('status')->nullable();
            $table->string('penyelesaian')->nullable();
            $table->string('data_dukung')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arahan_pimpinan');
    }
};
