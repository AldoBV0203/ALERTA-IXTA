<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->char('cuip_lookup', 64)
                ->nullable()
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->dropUnique(['cuip_lookup']);
            $table->dropColumn('cuip_lookup');
        });
    }
};