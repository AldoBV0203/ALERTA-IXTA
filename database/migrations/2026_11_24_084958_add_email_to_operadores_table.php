<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->text('email')->nullable();
            $table->string('email_lookup', 64)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->dropUnique(['email_lookup']);
            $table->dropColumn([
                'email',
                'email_lookup',
                'email_verified_at',
            ]);
        });
    }
};