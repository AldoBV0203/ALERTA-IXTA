<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_lookup', 64)->nullable()->unique();
        });

        // El modelo descifra email al leerlo. No cambia el correo almacenado.
        User::whereNotNull('email')
            ->select(['id', 'email'])
            ->chunkById(100, function ($usuarios) {
                foreach ($usuarios as $usuario) {
                    $email = strtolower(trim($usuario->email));

                    DB::table('users')
                        ->where('id', $usuario->id)
                        ->update([
                            'email_lookup' => hash_hmac(
                                'sha256',
                                $email,
                                config('app.key')
                            ),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email_lookup']);
            $table->dropColumn('email_lookup');
        });
    }
};