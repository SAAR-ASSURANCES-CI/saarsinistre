<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender' => function($q) {
            $q->select('id', 'nom_complet');
        }]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('sinistre.' . $this->message->sinistre_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'sinistre_id' => $this->message->sinistre_id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'contenu' => $this->message->contenu,
            'lu' => $this->message->lu,
            'created_at' => $this->message->created_at->toISOString(),
            'updated_at' => $this->message->updated_at->toISOString(),
            'sender' => [
                'id' => $this->message->sender->id,
                'nom_complet' => $this->message->sender->nom_complet,
            ]
        ];
    }
}