<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Códigos temporales de invitación.
        Schema::create('codigos_confianza', function (Blueprint $table) {

            $table->id();

            $table->foreignId('id_usuario')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('codigo_hash', 64)->unique();

            $table->timestamp('expira_en');

            $table->timestamp('usado_en')->nullable();

            $table->timestamps();
        });

        // Relaciones entre ciudadanos registrados.
        Schema::create('contactos_confianza', function (Blueprint $table) {

            $table->id();

            $table->foreignId('id_usuario_1')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_usuario_2')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_solicitante')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('estado', 20)
                ->default('PENDIENTE');

            $table->timestamp('aceptada_en')->nullable();

            $table->timestamps();

            // Evitar relaciones duplicadas.
            $table->unique([
                'id_usuario_1',
                'id_usuario_2'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos_confianza');

        Schema::dropIfExists('codigos_confianza');
    }
};