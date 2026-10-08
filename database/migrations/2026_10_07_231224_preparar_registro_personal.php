<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE operadores
            MODIFY rol VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE',
            MODIFY activo TINYINT(1) NOT NULL DEFAULT 0
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE operadores
            MODIFY rol VARCHAR(30) NOT NULL,
            MODIFY activo TINYINT(1) DEFAULT 1
        ");
    }
};