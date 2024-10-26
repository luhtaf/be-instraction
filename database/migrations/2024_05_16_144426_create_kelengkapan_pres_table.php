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
        Schema::create('kelengkapan_pre', function (Blueprint $table) {
            // $table->id();
            // $table->unsignedBigInteger('rapat_id'); // Foreign key
            $table->uuid('id')->primary();
            // $table->foreignUuid('rapat_id')->references('id')->on('rapat')->onDelete('cascade')->constrained();
            $table->uuid('rapat_id');
            $table->string('poin');
            $table->string('keterangan');
            $table->timestamps();
            // $table->foreign('rapat_id')->references('id')->on('rapat')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelengkapan_pre');
    }
};
