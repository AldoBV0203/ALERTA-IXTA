<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curp_prefijos', function (Blueprint $table) {
            $table->foreignId('id_usuario')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('longitud');
            $table->char('prefijo_hash', 64);

            $table->primary(['id_usuario', 'longitud']);

            $table->index(
                ['longitud', 'prefijo_hash'],
                'curp_prefijos_busqueda'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curp_prefijos');
    }
};