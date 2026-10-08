<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Cambiamos las coordenadas de DECIMAL a TEXT
         * porque Laravel almacenará el contenido cifrado.
         */
        DB::statement("
            ALTER TABLE alertas
            MODIFY latitud TEXT NULL,
            MODIFY longitud TEXT NULL
        ");

        DB::statement("
            ALTER TABLE ubicaciones_alerta
            MODIFY latitud TEXT NOT NULL,
            MODIFY longitud TEXT NOT NULL
        ");
    }

    public function down(): void
    {
        /*
         * Solo sería seguro regresar a DECIMAL
         * si las tablas no contienen coordenadas cifradas.
         */
        DB::statement("
            ALTER TABLE alertas
            MODIFY latitud DECIMAL(10,7) NULL,
            MODIFY longitud DECIMAL(10,7) NULL
        ");

        DB::statement("
            ALTER TABLE ubicaciones_alerta
            MODIFY latitud DECIMAL(10,7) NOT NULL,
            MODIFY longitud DECIMAL(10,7) NOT NULL
        ");
    }
};