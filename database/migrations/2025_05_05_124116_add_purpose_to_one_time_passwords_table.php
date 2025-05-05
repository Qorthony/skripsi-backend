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
        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->string('purpose')->nullable()->after('otp_code')->comment('The purpose of OTP: login, register, etc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->dropColumn('purpose');
        });
    }
};
