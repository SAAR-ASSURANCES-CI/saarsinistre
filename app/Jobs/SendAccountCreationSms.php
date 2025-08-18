<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\OrangeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAccountCreationSms implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public function __construct(
        public User $user,
        public string $telephone
    ) {}

    public function handle(OrangeService $orangeService): void
    {
        try {
            $this->sendAccountCreationSmsViaConfirmationMethod($orangeService);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de connexion: ' . $e->getMessage());
        }
    }
    
    private function sendAccountCreationSmsViaConfirmationMethod(OrangeService $orangeService): void
    {
        $nomFormate = strtoupper(explode(' ', trim($this->user->nom_complet ?: 'CLIENT'))[0]);
        
        $message = "SAAR ASSURANCE\n";
        $message .= "Cher(e) {$nomFormate}, votre espace client est pret :\n";
        $message .= "Identifiant: {$this->user->username}\n";
        $message .= "Code: {$this->user->password_temp}\n";
        $message .= "Valable 48h";

        $this->executeSmsUsingWorkingMethod($orangeService, $message);
    }
    
    private function executeSmsUsingWorkingMethod(OrangeService $orangeService, string $message): void
    {
        $token = $orangeService->getToken();
        $countrySenderNumber = env('ORANGE_SMS_SENDER_NUMBER', '2250000');

        $formattedRecipient = $this->formatPhoneNumber($this->telephone);
        $recipientWithoutPlus = ltrim($formattedRecipient, '+');

        if (!preg_match('/^2250[0-9]{8,10}$/', $recipientWithoutPlus)) {
            throw new \Exception("Numéro de téléphone invalide: {$this->telephone} (formaté: {$formattedRecipient})");
        }

        $sslConfig = [
            'verify' => app()->environment('production') ? storage_path('app/cacert.pem') : false,
            'timeout' => 30,
            'curl' => [
                CURLOPT_CAINFO => storage_path('app/cacert.pem'),
                CURLOPT_CAPATH => storage_path('app/'),
                CURLOPT_SSL_VERIFYPEER => app()->environment('production'),
                CURLOPT_SSL_VERIFYHOST => app()->environment('production') ? 2 : 0,
            ]
        ];
        
        $client = new \GuzzleHttp\Client($sslConfig);
        
        $response = $client->post('https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B' . $countrySenderNumber . '/requests', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token['access_token'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'outboundSMSMessageRequest' => [
                    'address' => 'tel:+' . $recipientWithoutPlus,
                    'senderAddress' => 'tel:+' . $countrySenderNumber,
                    'senderName' => 'SAAR CI',
                    'outboundSMSTextMessage' => [
                        'message' => $message,
                    ],
                ],
            ],
            'http_errors' => false
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $result = json_decode($body, true);

        if ($statusCode === 201 || $statusCode === 200) {
            Log::info('SMS de création de compte envoyé avec succès', [
                'recipient_original' => $this->telephone,
                'recipient_formatted' => $formattedRecipient,
                'recipient_final' => 'tel:+' . $recipientWithoutPlus,
                'username' => $this->user->username,
                'message_length' => strlen($message),
                'response' => $result
            ]);
        } else {
            $errorMsg = $result['error_description'] ?? $result['error']['message'] ?? $result['error'] ?? 'Erreur inconnue';
            throw new \Exception("Erreur {$statusCode} lors de l'envoi du SMS de création compte: {$errorMsg}");
        }
    }
    
    private function formatPhoneNumber(string $phoneNumber): string
    {
        $cleanNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (str_starts_with($cleanNumber, '+225')) {
            return $cleanNumber;
        }

        if (str_starts_with($cleanNumber, '225')) {
            return '+' . $cleanNumber;
        }

        if (str_starts_with($cleanNumber, '0')) {
            return '+225' . $cleanNumber;
        }

        return '+225' . $cleanNumber;
    }
}
