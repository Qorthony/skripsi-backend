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
        Schema::create('resales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_issued_id')->constrained();
            $table->integer('harga_jual');
            $table->enum('status', ['active','booked','sold','cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resales');
    }
};
