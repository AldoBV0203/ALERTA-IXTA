<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerificarCorreoCiudadano extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica tu correo — ALERTA IXTA')
            ->view('emails.verificar-correo', [
                'urlVerificacion' => $this->verificationUrl($notifiable),
                'minutosVigencia' => config('auth.verification.expire', 60),
            ]);
    }
}