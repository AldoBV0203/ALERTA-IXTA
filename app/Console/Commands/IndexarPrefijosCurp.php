<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CurpService;
use Illuminate\Console\Command;
use Throwable;

class IndexarPrefijosCurp extends Command
{
    protected $signature = 'curp:indexar-prefijos';

    protected $description =
        'Prepara la búsqueda por inicio de CURP de los ciudadanos registrados';

    public function handle(CurpService $curpService): int
    {
        $indexados = 0;
        $fallidos = 0;

        User::query()
            ->select(['id', 'curp'])
            ->chunkById(200, function ($ciudadanos) use (
                $curpService,
                &$indexados,
                &$fallidos
            ) {
                foreach ($ciudadanos as $ciudadano) {
                    try {
                        $curpService->indexarPrefijos(
                            (int) $ciudadano->id,
                            $ciudadano->curp
                        );

                        $indexados++;
                    } catch (Throwable $error) {
                        $fallidos++;

                        report($error);

                        $this->error(
                            'No se pudo preparar la búsqueda del ciudadano con ID ' .
                            $ciudadano->id . '.'
                        );
                    }
                }
            });

        $this->info('Ciudadanos indexados: ' . $indexados . '.');

        if ($fallidos > 0) {
            $this->warn(
                'Ciudadanos pendientes por revisar: ' . $fallidos . '.'
            );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}