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
            $table->foreignUuid('transaction_id')->constrained();
            $table->foreignUuid('ticket_id')->constrained();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->string('email_penerima')->nullable();
            $table->dateTime('waktu_penerbitan')->nullable();
            $table->boolean('aktif')->default(false);
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
