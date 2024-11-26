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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organizer_id')->constrained();
            $table->string('nama');
            $table->string('poster')->nullable();
            $table->string('lokasi');
            $table->string('kota')->nullable();
            $table->string('alamat_lengkap')->nullable();
            $table->string('tautan_acara')->nullable();
            $table->dateTime('jadwal_mulai');
            $table->dateTime('jadwal_selesai');
            $table->text('deskripsi')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
