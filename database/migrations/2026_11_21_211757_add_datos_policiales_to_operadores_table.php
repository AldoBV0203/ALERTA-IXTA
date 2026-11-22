<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->text('curp')->nullable();

            $table->char('curp_lookup', 64)
                ->nullable()
                ->unique();

            $table->text('telefono')->nullable();

            $table->string('numero_empleado', 30)
                ->nullable()
                ->unique();

            $table->string('numero_placa', 30)->nullable();
            $table->text('cuip')->nullable();
            $table->string('grado', 80)->nullable();
            $table->string('adscripcion', 120)->nullable();
            $table->string('numero_patrulla', 30)->nullable();
            $table->string('tipo_solicitud', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('operadores', function (Blueprint $table) {
            $table->dropUnique(['curp_lookup']);
            $table->dropUnique(['numero_empleado']);

            $table->dropColumn([
                'curp',
                'curp_lookup',
                'telefono',
                'numero_empleado',
                'numero_placa',
                'cuip',
                'grado',
                'adscripcion',
                'numero_patrulla',
                'tipo_solicitud',
            ]);
        });
    }
};