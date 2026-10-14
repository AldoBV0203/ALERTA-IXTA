<?php

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

class CurpService
{
    /*
     Claves oficiales de entidades federativas utilizadas en la CURP.
    NE corresponde a nacido en el extranjero.
     */
    private const ENTIDADES = [
        'AS', 'BC', 'BS', 'CC', 'CL', 'CM', 'CS', 'CH',
        'DF', 'DG', 'GT', 'GR', 'HG', 'JC', 'MC', 'MN',
        'MS', 'NT', 'NL', 'OC', 'PL', 'QT', 'QR', 'SP',
        'SL', 'SR', 'TC', 'TS', 'TL', 'VZ', 'YN', 'ZS',
        'NE',
    ];

    // Normaliza la CURP antes de trabajar con ella.
    
    public function normalizar(string $curp): string
    {
        return strtoupper(trim($curp));
    }

    // Comprueba estructura, fecha, entidad y dígito verificador.
   
    public function esValida(string $curp): bool
    {
        $curp = $this->normalizar($curp);

        /*
         Estructura para el curp:
        
         4 letras
         6 números de fecha AAMMDD
         H o M
         2 letras de entidad
         3 consonantes
         carácter diferenciador
         dígito verificador
         */
        $patron = '/^[A-Z][AEIOU][A-Z]{2}' .
                  '\d{6}' .
                  '[HM]' .
                  '[A-Z]{2}' .
                  '[B-DF-HJ-NP-TV-Z]{3}' .
                  '[0-9A-J]' .
                  '\d$/';

        if (!preg_match($patron, $curp)) {
            return false;
        }

        // Comprobar entidad federativa.
        $entidad = substr($curp, 11, 2);

        if (!in_array($entidad, self::ENTIDADES, true)) {
            return false;
        }

        // Extraer fecha AAMMDD.
        $anioCorto = (int) substr($curp, 4, 2);
        $mes = (int) substr($curp, 6, 2);
        $dia = (int) substr($curp, 8, 2);

        /*
         Posición 17:
         número -> nacimiento antes del año 2000
         letra  -> nacimiento a partir del año 2000
         */
        $caracterSiglo = $curp[16];

        if (ctype_digit($caracterSiglo)) {
            $anio = 1900 + $anioCorto;
        } else {
            $anio = 2000 + $anioCorto;
        }

        // Evita aceptar fechas imposibles, por ejemplo 31/02.
        if (!checkdate($mes, $dia, $anio)) {
            return false;
        }

        // No aceptar fechas futuras.
        $fechaNacimiento = sprintf(
            '%04d-%02d-%02d',
            $anio,
            $mes,
            $dia
        );

        if ($fechaNacimiento > date('Y-m-d')) {
            return false;
        }

        // Comprobar el último dígito.
        $base = substr($curp, 0, 17);
        $digitoEsperado = $this->calcularDigitoVerificador($base);

        return $curp[17] === $digitoEsperado;
    }

    
    // Genera el HMAC que utilizaremos para buscar la CURP sin almacenarla en texto plano.
     
    public function generarLookup(string $curp): string
    {
        $curp = $this->normalizar($curp);

        if (!$this->esValida($curp)) {
            throw new InvalidArgumentException(
                'La CURP proporcionada no tiene un formato válido.'
            );
        }

        $clave = config('services.alerta_ixta.curp_index_key');

        if (empty($clave)) {
            throw new RuntimeException(
                'No se encuentra configurada CURP_INDEX_KEY.'
            );
        }

        return hash_hmac(
            'sha256',
            $curp,
            $clave
        );
    }

    
    //Calcula el dígito verificador de la CURP.
     
    private function calcularDigitoVerificador(string $base): string
    {
        $diccionario = preg_split(
            '//u',
            '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ',
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $suma = 0;

        for ($i = 0; $i < 17; $i++) {

            $valor = array_search(
                $base[$i],
                $diccionario,
                true
            );

            if ($valor === false) {
                return '';
            }

            $peso = 18 - $i;

            $suma += $valor * $peso;
        }

        $digito = 10 - ($suma % 10);

        if ($digito === 10) {
            $digito = 0;
        }

        return (string) $digito;
    }
}