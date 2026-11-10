<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_cancelacion', function (Blueprint $table) {

            $table->id();

            // Alerta y ciudadano que solicita la cancelación.
            $table->integer('id_alerta');
            $table->unsignedBigInteger('id_usuario');

            // Operador que verifica el contacto y gestiona el código.
            $table->integer('id_operador')->nullable();

            $table->text('motivo');

            $table->string('estado', 30)
                ->default('PENDIENTE');

            // Guardaremos el hash del código.
            $table->string('codigo_hash')->nullable();

            $table->dateTime('codigo_expira_at')->nullable();

            $table->unsignedTinyInteger('intentos')
                ->default(0);

            // Registro de la revisión realizada por el operador.
            $table->dateTime('contacto_verificado_at')->nullable();

            $table->text('observaciones_operador')->nullable();

            $table->dateTime('resuelta_at')->nullable();

            $table->timestamps();

            // Facilitar las consultas de solicitudes.
            $table->index(['id_alerta', 'estado']);
            $table->index('estado');

            // Relaciones con las tablas existentes.
            $table->foreign('id_alerta')
                ->references('id_alerta')
                ->on('alertas')
                ->restrictOnDelete();

            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            $table->foreign('id_operador')
                ->references('id_operador')
                ->on('operadores')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_cancelacion');
    }
};