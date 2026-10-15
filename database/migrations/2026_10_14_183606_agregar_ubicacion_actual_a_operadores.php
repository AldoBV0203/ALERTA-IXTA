<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->boolean('disponible')
                ->default(true)
                ->after('activo');

            $table->text('latitud_actual')
                ->nullable()
                ->after('disponible');

            $table->text('longitud_actual')
                ->nullable()
                ->after('latitud_actual');

            $table->decimal('precision_m', 8, 2)
                ->nullable()
                ->after('longitud_actual');

            $table->timestamp('ubicacion_actualizada_at')
                ->nullable()
                ->after('precision_m');
        });
    }

    public function down(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->dropColumn([
                'disponible',
                'latitud_actual',
                'longitud_actual',
                'precision_m',
                'ubicacion_actualizada_at',
            ]);
        });
    }
};