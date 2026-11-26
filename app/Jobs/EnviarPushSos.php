<?php

namespace App\Jobs;

use App\Models\Alerta;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class EnviarPushSos implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $idAlerta) {}

    public function handle(): void
    {
        $alerta = Alerta::find($this->idAlerta);

        if (!$alerta) {
            return;
        }

        $configuracion = config('services.webpush');

        if (
            empty($configuracion['subject']) ||
            empty($configuracion['public_key']) ||
            empty($configuracion['private_key'])
        ) {
            Log::warning('No se enviaron notificaciones SOS: falta configurar Web Push.');
            return;
        }

        $nombre = User::find($alerta->id_usuario)?->name ?? 'un contacto';

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $configuracion['subject'],
                'publicKey' => $configuracion['public_key'],
                'privateKey' => $configuracion['private_key'],
            ],
        ]);

        $notificaciones = DB::table('notificaciones_sos')
            ->where('id_alerta', $this->idAlerta)
            ->get(['id', 'id_destinatario', 'circulo']);

        foreach ($notificaciones as $notificacion) {
            $suscripciones = DB::table('suscripciones_push')
                ->where('id_usuario', $notificacion->id_destinatario)
                ->get(['id', 'suscripcion_cifrada']);

            foreach ($suscripciones as $registro) {
                try {
                    $datos = json_decode(
                        Crypt::decryptString($registro->suscripcion_cifrada),
                        true,
                        512,
                        JSON_THROW_ON_ERROR
                    );

                    $host = strtolower(
                        (string) parse_url($datos['endpoint'], PHP_URL_HOST)
                    );

                    $hostPermitido = in_array($host, [
                        'fcm.googleapis.com',
                        'updates.push.services.mozilla.com',
                        'web.push.apple.com',
                    ], true) || str_ends_with($host, '.notify.windows.com');

                    if (!$hostPermitido) {
                        Log::warning('Proveedor Push no admitido.', [
                            'suscripcion_id' => $registro->id,
                        ]);
                        continue;
                    }

                    $payload = json_encode([
                        'titulo' => 'Alerta SOS de ' . $nombre,
                        'cuerpo' => $notificacion->circulo === 'VECINOS'
                            ? 'Un vecino de tu círculo creó una alerta.'
                            : 'Un contacto de confianza creó una alerta.',
                        'destino' => '/inicio?notificacion=' . $notificacion->id,
                        'notificacionId' => $notificacion->id,
                    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

                    $reporte = $webPush->sendOneNotification(
                        Subscription::create($datos),
                        $payload,
                        ['TTL' => 86400, 'urgency' => 'high']
                    );

                    if ($reporte->isSubscriptionExpired()) {
                        DB::table('suscripciones_push')
                            ->where('id', $registro->id)
                            ->delete();
                    } elseif (!$reporte->isSuccess()) {
                        Log::warning('Falló un envío Push SOS.', [
                            'notificacion_id' => $notificacion->id,
                            'suscripcion_id' => $registro->id,
                        ]);
                    } else {
                        Log::info('Proveedor aceptó Push SOS.', [
                            'notificacion_id' => $notificacion->id,
                            'suscripcion_id' => $registro->id,
                        ]);
                    }
                } catch (\Throwable $error) {
                    Log::warning('No se pudo procesar una suscripción Push SOS.', [
                        'notificacion_id' => $notificacion->id,
                        'suscripcion_id' => $registro->id,
                    ]);
                }
            }
        }
    }
}
