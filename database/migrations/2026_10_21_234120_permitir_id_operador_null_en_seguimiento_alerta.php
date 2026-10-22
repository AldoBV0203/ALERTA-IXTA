<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE seguimiento_alerta
             MODIFY id_operador INT NULL'
        );
    }


    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        DB::statement(
            'ALTER TABLE seguimiento_alerta
             MODIFY id_operador INT NOT NULL'
        );
    }
};