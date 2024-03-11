<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationController extends Controller
{
    public function sendNotification($userId)
    {
        $user = User::find($userId);
        $token = $user->device_token; // Asumiendo que guardas el token en el usuario

        $messaging = Firebase::messaging();

        $message = CloudMessage::fromArray([
            'token' => $token,
            'notification' => [
                'title' => 'Notificación',
                'body' => 'Hola, este es un mensaje de prueba.',
            ],
        ]);

        $messaging->send($message);
    }
}
