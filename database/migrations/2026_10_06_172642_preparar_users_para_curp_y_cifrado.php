<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        });

        // Los valores cifrados pueden ser más largos que 255 caracteres.
        DB::statement('ALTER TABLE users MODIFY name TEXT NOT NULL');
        DB::statement('ALTER TABLE users MODIFY email TEXT NULL');
        DB::statement('ALTER TABLE users MODIFY telefono TEXT NULL');

        Schema::table('users', function (Blueprint $table) {

            // CURP cifrada.
            $table->text('curp')->nullable()->after('id');

            // Índice seguro utilizado para localizar al usuario por CURP.
            $table->char('curp_lookup', 64)
                  ->nullable()
                  ->unique()
                  ->after('curp');

            // Estado de validación de identidad.
            $table->string('estatus_verificacion', 20)
                  ->default('PENDIENTE')
                  ->after('estatus');

            $table->timestamp('telefono_verificado_at')
                  ->nullable()
                  ->after('estatus_verificacion');

            $table->timestamp('identidad_verificada_at')
                  ->nullable()
                  ->after('telefono_verificado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['curp_lookup']);

            $table->dropColumn([
                'curp',
                'curp_lookup',
                'estatus_verificacion',
                'telefono_verificado_at',
                'identidad_verificada_at',
            ]);
        });

        DB::statement('ALTER TABLE users MODIFY name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY telefono VARCHAR(20) NULL');

        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};

/*
users

id

curp                  ← CIFRADA
curp_lookup           ← HMAC para buscar

name                  ← CIFRADO
email                 ← CIFRADO
telefono              ← CIFRADO

password              ← HASH

estatus
estatus_verificacion
*/