<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

class ChatMessageNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chatMessage;
    public $gestionnaire;
    public $assure;
    public $sinistre;
    public $url_chat;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message, User $gestionnaire)
    {
        $this->chatMessage = $message->load(['sender', 'sinistre', 'attachments']);
        $this->gestionnaire = $gestionnaire;
        $this->assure = $message->sender;
        $this->sinistre = $message->sinistre;
        $this->url_chat = url('/gestionnaires/sinistres/' . $message->sinistre->id . '/chat');
        $this->company = [
            'name' => 'SAAR ASSURANCES',
            'phone' => '+225 20 30 30 30',
            'email' => 'contact@saar-assurance.ci',
            'address' => 'Abidjan, Côte d\'Ivoire'
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.nouveau-message-chat')
                    ->subject('Nouveau message reçu - Sinistre N° ' . $this->sinistre->numero_sinistre)
                    ->with([
                        'chatMessage' => $this->chatMessage,
                        'gestionnaire' => $this->gestionnaire,
                        'assure' => $this->assure,
                        'sinistre' => $this->sinistre,
                        'url_chat' => $this->url_chat,
                        'company' => $this->company,
                    ]);
    }
}
