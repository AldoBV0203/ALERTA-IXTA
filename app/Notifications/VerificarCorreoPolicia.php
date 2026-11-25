<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;

class VerificarCorreoPolicia extends VerificarCorreoCiudadano
{
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.policia',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}