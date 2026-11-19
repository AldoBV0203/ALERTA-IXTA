<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->string('cierre_qr_hash', 64)->nullable();
            $table->dateTime('cierre_qr_expira_at')->nullable();
            $table->dateTime('cierre_qr_usado_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn([
                'cierre_qr_hash',
                'cierre_qr_expira_at',
                'cierre_qr_usado_at',
            ]);
        });
    }
};