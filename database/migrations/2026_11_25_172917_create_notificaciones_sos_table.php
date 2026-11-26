<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones_sos', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_alerta');

            $table->foreignId('id_destinatario')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('circulo', 20);
            $table->timestamp('leida_at')->nullable();
            $table->timestamps();

            $table->unique(['id_alerta', 'id_destinatario']);
            $table->index(['id_destinatario', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sos');
    }
};