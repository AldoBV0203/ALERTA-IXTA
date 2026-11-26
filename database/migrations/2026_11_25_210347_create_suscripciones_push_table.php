<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripciones_push', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->char('endpoint_hash', 64)->unique();
            $table->text('suscripcion_cifrada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones_push');
    }
};
