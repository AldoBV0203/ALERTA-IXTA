<?php

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

class CuipService
{
    public function normalizar(string $cuip): string
    {
        return strtoupper(trim($cuip));
    }

    public function generarLookup(string $cuip): string
    {
        $cuip = $this->normalizar($cuip);

        if ($cuip === '' || strlen($cuip) > 50) {
            throw new InvalidArgumentException(
                'La CUIP debe contener entre 1 y 50 caracteres.'
            );
        }

        $clave = config('services.alerta_ixta.curp_index_key');

        if (!is_string($clave) || trim($clave) === '') {
            throw new RuntimeException(
                'No se encuentra configurada CURP_INDEX_KEY.'
            );
        }

        return hash_hmac(
            'sha256',
            'cuip:v1:' . $cuip,
            $clave
        );
    }
}