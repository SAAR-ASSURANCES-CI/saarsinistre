<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Message;

class NewMessageNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'sinistre_id' => $this->message->sinistre_id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'contenu' => $this->message->contenu,
            'created_at' => $this->message->created_at,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message_id' => $this->message->id,
            'sinistre_id' => $this->message->sinistre_id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'contenu' => $this->message->contenu,
            'created_at' => $this->message->created_at,
        ]);
    }

    public function broadcastOn()
    {
        // Canal privé pour l'utilisateur destinataire
        return ['users.' . $this->message->receiver_id];
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
} 