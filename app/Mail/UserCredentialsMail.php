<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCredentialsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $motDePasseTemporaire;
    public $company;
    public $urlConnexion;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $motDePasseTemporaire)
    {
        $this->user = $user;
        $this->motDePasseTemporaire = $motDePasseTemporaire;
        $this->urlConnexion = route('login');
        $this->company = [
            'name' => 'SAAR ASSURANCES',
            'phone' => '+225 20 30 30 30',
            'email' => 'contact@saar-assurance.ci',
            'address' => 'Abidjan, Côte d\'Ivoire'
        ];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.user-credentials')
                    ->subject('Votre compte SAAR ASSURANCES a été créé')
                    ->with([
                        'user' => $this->user,
                        'motDePasseTemporaire' => $this->motDePasseTemporaire,
                        'urlConnexion' => $this->urlConnexion,
                        'company' => $this->company,
                    ]);
    }
}
