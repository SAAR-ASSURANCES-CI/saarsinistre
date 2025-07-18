<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OrangeService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getToken()
    {
        if (Cache::has('orange_sms_api_token')) {
            $cachedToken = Cache::get('orange_sms_api_token');

            if (
                isset($cachedToken['expires_at']) &&
                Carbon::parse($cachedToken['expires_at'])->gt(Carbon::now()->addMinutes(5))
            ) {
                return $cachedToken;
            }
        }

        $token = $this->fetchNewToken();

        $this->cacheToken($token);

        return $token;
    }

    private function fetchNewToken()
    {
        try {
            $client = new Client([
                'verify' => false,
            ]);
            $response = $client->post(env('ORANGE_SMS_API_TOKEN'), [
                'headers' => [
                    'Authorization' => 'Basic ' . env('ORANGE_SMS_API_AUTHORIZATION'),
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                    'Accept'        => 'application/json'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $tokenData = json_decode($response->getBody(), true);

            if (!isset($tokenData['access_token'])) {
                throw new \Exception('Token d\'accès non reçu de l\'API Orange');
            }

            return $tokenData;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du token Orange SMS API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Impossible de récupérer le token d\'authentification Orange: ' . $e->getMessage());
        }
    }

    private function cacheToken(array $token)
    {

        $token['expires_at'] = Carbon::now()->addSeconds($token['expires_in'] ?? 3600);

        $cacheLifetime = Carbon::now()->addSeconds(($token['expires_in'] ?? 3600) - 300);

        Cache::put('orange_sms_api_token', $token, $cacheLifetime);
    }

    public function sendSMS(string $recipient, string $message, string $senderName)
    {
        try {
            $token = $this->getToken();

            $payload = [
                'outboundSMSMessageRequest' => [
                    'address' => 'tel:+' . ltrim($recipient, '+'),
                    'senderAddress' => 'tel:+' . env('ORANGE_SMS_SENDER_ADDRESS'),
                    'outboundSMSTextMessage' => [
                        'message' => $message
                    ]
                ]
            ];

            if ($senderName) {
                $payload['outboundSMSMessageRequest']['senderName'] = $senderName;
            }

            $response = $this->client->post(env('ORANGE_SMS_API_SEND_URL'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token['access_token'],
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => $payload
            ]);

            $result = json_decode($response->getBody(), true);

            return $result;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS Orange', [
                'recipient' => $recipient,
                'message' => $message,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Impossible d\'envoyer le SMS: ' . $e->getMessage());
        }
    }

    public function checkDeliveryStatus(string $requestId)
    {
        try {
            $token = $this->getToken();

            $response = $this->client->get(env('ORANGE_SMS_API_STATUS_URL') . '/' . $requestId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token['access_token'],
                    'Accept' => 'application/json'
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut SMS', [
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Impossible de vérifier le statut du SMS: ' . $e->getMessage());
        }
    }

    public function clearTokenCache()
    {
        Cache::forget('orange_sms_api_token');
    }

    public function validateConfiguration()
    {
        $requiredEnvVars = [
            'ORANGE_SMS_API_TOKEN',
            'ORANGE_SMS_API_AUTHORIZATION',
            'ORANGE_SMS_SENDER_ADDRESS'
        ];

        foreach ($requiredEnvVars as $var) {
            if (empty(env($var))) {
                Log::warning("Variable d'environnement manquante: {$var}");
                return false;
            }
        }

        return true;
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

    public function sendSmsConfirmationSinistre(string $recipientPhone, string $nomAssure, string $numeroSinistre)
    {
        try {
            $token = $this->getToken();
            $countrySenderNumber = env('ORANGE_SMS_SENDER_NUMBER', '2250000');

            $formattedRecipient = $this->formatPhoneNumber($recipientPhone);

            $recipientWithoutPlus = ltrim($formattedRecipient, '+');

            if (!preg_match('/^2250[0-9]{8,10}$/', $recipientWithoutPlus)) {
                throw new \Exception("Numéro de téléphone invalide: {$recipientPhone} (formaté: {$formattedRecipient})");
            }

            $nomFormate = strtoupper(explode(' ', trim($nomAssure))[0]);

            $message = "SAAR ASSURANCE\nCher(e) {$nomFormate}, votre sinistre N°{$numeroSinistre} a ete declare avec succes. Vous serez contacte(e) prochainement.";

            if (strlen($message) > 160) {
                $message = "SAAR ASSURANCE\nSinistre N°{$numeroSinistre} declare avec succes. Vous serez contacte prochainement.";
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token['access_token'],
                'Content-Type' => 'application/json',
            ])->post('https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B' . $countrySenderNumber . '/requests', [
                'outboundSMSMessageRequest' => [
                    'address' => 'tel:+' . $recipientWithoutPlus,
                    'senderAddress' => 'tel:+' . $countrySenderNumber,
                    'senderName' => 'SAAR ASSURANCE CÔTE D\'IVOIRE',
                    'outboundSMSTextMessage' => [
                        'message' => $message,
                    ],
                ],
            ]);

            $result = $response->json();

            if ($response->successful()) {
                Log::info('SMS de confirmation sinistre envoyé avec succès', [
                    'recipient_original' => $recipientPhone,
                    'recipient_formatted' => $formattedRecipient,
                    'recipient_final' => 'tel:+' . $recipientWithoutPlus,
                    'numero_sinistre' => $numeroSinistre,
                    'message_length' => strlen($message),
                    'response' => $result
                ]);
            } else {
                Log::error('Erreur lors de l\'envoi du SMS de confirmation sinistre', [
                    'recipient_original' => $recipientPhone,
                    'recipient_formatted' => $formattedRecipient,
                    'recipient_final' => 'tel:+' . $recipientWithoutPlus,
                    'numero_sinistre' => $numeroSinistre,
                    'status' => $response->status(),
                    'response' => $result
                ]);

                throw new \Exception('Erreur API Orange: ' . ($result['error']['message'] ?? 'Erreur inconnue'));
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi du SMS de confirmation sinistre', [
                'recipient' => $recipientPhone,
                'numero_sinistre' => $numeroSinistre,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Impossible d\'envoyer le SMS de confirmation: ' . $e->getMessage());
        }
    }

    public function handle()
    {
        // Handle the service logic
    }
}
