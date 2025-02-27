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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('event_id')->constrained();
            $table->integer('jumlah_tiket');
            $table->integer('total_harga');
            $table->string('batas_waktu');
            $table->enum('status', ['pending', 'payment','success', 'failed'])->default('pending');
            $table->string('metode_pembayaran')->nullable();
            $table->string('kode_pembayaran')->nullable();
            $table->json('detail_pembayaran')->nullable();
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->integer('biaya_pembayaran')->nullable();
            $table->integer('total_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
