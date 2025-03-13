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
        Schema::create('ticket_issueds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('transaction_item_id')->constrained();
            $table->uuid('kode_tiket')->nullable()->unique();
            $table->string('email_penerima')->nullable();
            $table->dateTime('waktu_penerbitan')->nullable();
            $table->enum('status', ['inactive','active','resale','sold','checkin'])->default('inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_issueds');
    }
};
