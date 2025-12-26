<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoUsuarioCreado extends Notification
{
    use Queueable;

    protected $password;
    protected $usuario;

    public function __construct($password, $usuario)
    {
        $this->password = $password;
        $this->usuario = $usuario;
    }

    public function via($notifiable): array
    {
        return ['mail']; // Canal de envío
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenido al Sistema - Credenciales de Acceso')
            ->greeting('¡Hola, ' . $this->usuario->nombres . '!')
            ->line('Se ha creado una cuenta para ti en nuestro sistema administrativo.')
            ->line('Tus credenciales de acceso son:')
            ->line('**Correo:** ' . $this->usuario->correo_electronico)
            ->line('**Contraseña:** ' . $this->password)
            ->action('Ingresar al Sistema', url('/login'))
            ->line('Por seguridad, te recomendamos cambiar tu contraseña al ingresar por primera vez.')
            ->salutation('Saludos, el equipo de IT.');
    }
}

