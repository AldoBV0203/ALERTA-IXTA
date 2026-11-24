<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo — ALERTA IXTA</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial,sans-serif; color:#272b30;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color:#f3f4f6;">
        <tr>
            <td align="center" style="padding:24px 12px;">

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:16px;">

                    <tr>
                        <td align="center"
                            style="padding:26px 20px; background-color:#691C32; border-radius:16px 16px 0 0;">

                            <img
                                src="{{ $message->embed(public_path('images/logoIxtapaluca2025.png')) }}"
                                alt="Gobierno de Ixtapaluca"
                                width="64"
                                style="display:block; width:64px; height:auto; background-color:#ffffff; padding:8px; border-radius:10px;">

                            <h1 style="margin:16px 0 6px; color:#ffffff; font-size:26px;">
                                ALERTA IXTA
                            </h1>

                            <p style="margin:0; color:#ffffff; font-size:13px;">
                                Sistema Municipal de Alerta
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 26px;">

                            <h2 style="margin:0 0 18px; font-size:23px; color:#691C32;">
                                Verifica tu correo electrónico
                            </h2>

                            <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">
                                Para confirmar tu dirección de correo en ALERTA IXTA,
                                presiona el siguiente botón.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0"
                                style="margin:26px auto;">
                                <tr>
                                    <td align="center"
                                        style="background-color:#691C32; border-radius:8px;">
                                        <a
                                            href="{{ $urlVerificacion }}"
                                            style="display:inline-block; padding:16px 26px; color:#ffffff; font-size:16px; font-weight:bold; text-decoration:none;">
                                            Verificar mi correo
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
                                Este enlace vence en
                                <strong>{{ $minutosVigencia }} minutos</strong>.
                            </p>

                            <p style="margin:0 0 22px; font-size:14px; line-height:1.6; color:#59616b;">
                                Si no creaste una cuenta ni solicitaste este mensaje,
                                puedes ignorarlo. No necesitas responder a este correo.
                            </p>

                            <div style="border-top:1px solid #e5e7eb; padding-top:20px;">
                                <p style="margin:0 0 10px; font-size:12px; line-height:1.6; color:#59616b;">
                                    Si el botón no funciona, copia y pega este enlace
                                    en tu navegador:
                                </p>

                                <a
                                    href="{{ $urlVerificacion }}"
                                    style="font-size:12px; line-height:1.6; color:#691C32; word-break:break-all;">
                                    {{ $urlVerificacion }}
                                </a>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td align="center"
                            style="padding:20px; border-top:1px solid #e5e7eb;">
                            <p style="margin:0; color:#59616b; font-size:12px; line-height:1.6;">
                                ALERTA IXTA<br>
                                Gobierno de Ixtapaluca
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>