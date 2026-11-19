<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Códigos temporales para vincular vecinos.
        Schema::create('codigos_vecinos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_usuario')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('codigo_hash', 64)->unique();

            $table->timestamp('expira_en');

            $table->timestamp('usado_en')->nullable();

            $table->timestamps();
        });

        // Vínculos entre los ciudadanos del círculo de vecinos.
        Schema::create('contactos_vecinos', function (Blueprint $table) {
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

            // Evitar vínculos duplicados dentro de este círculo.
            $table->unique([
                'id_usuario_1',
                'id_usuario_2',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos_vecinos');
        Schema::dropIfExists('codigos_vecinos');
    }
};