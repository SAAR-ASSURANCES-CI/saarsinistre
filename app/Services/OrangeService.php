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
    protected $sslConfig;

    public function __construct()
    {
        $this->ensureCaCertificateExists();
        
        $this->sslConfig = [
            'verify' => app()->environment('production') ? storage_path('app/cacert.pem') : false,
            'timeout' => 30,
            'curl' => [
                CURLOPT_CAINFO => storage_path('app/cacert.pem'),
                CURLOPT_CAPATH => storage_path('app/'),
                CURLOPT_SSL_VERIFYPEER => app()->environment('production'),
                CURLOPT_SSL_VERIFYHOST => app()->environment('production') ? 2 : 0,
            ]
        ];
        
        $this->client = new Client($this->sslConfig);
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
            if (!$this->validateConfiguration()) {
                throw new \Exception('Configuration Orange SMS API invalide. Vérifiez les variables d\'environnement requises.');
            }

            $clientId = env('ORANGE_CLIENT_ID');
            $clientSecret = env('ORANGE_CLIENT_SECRET');
            $tokenUrl = env('ORANGE_SMS_API_TOKEN_URL');

            if (empty($clientId) || empty($clientSecret)) {
                throw new \Exception('Identifiants client Orange manquants (ORANGE_CLIENT_ID ou ORANGE_CLIENT_SECRET)');
            }

            if (empty($tokenUrl)) {
                throw new \Exception('URL du token Orange non configurée (ORANGE_SMS_API_TOKEN_URL)');
            }

            $client = new Client($this->sslConfig);

            $credentials = base64_encode($clientId . ':' . $clientSecret);

            $response = $client->post($tokenUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                    'Accept'       => 'application/json'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ],
                'http_errors' => false 
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $tokenData = json_decode($body, true);

            if ($statusCode !== 200) {
                $errorMsg = $tokenData['error_description'] ?? 'Erreur inconnue';
                throw new \Exception("Erreur {$statusCode} lors de la récupération du token: {$errorMsg}");
            }

            if (!isset($tokenData['access_token'])) {
                throw new \Exception('Token d\'accès non reçu dans la réponse de l\'API Orange');
            }

            $requiredKeys = ['access_token', 'token_type', 'expires_in'];
            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $tokenData)) {
                    throw new \Exception("Clé manquante dans la réponse du token: {$key}");
                }
            }

            return $tokenData;
        } catch (\Exception $e) {
            Log::error('Erreur critique lors de la récupération du token Orange SMS API', [
                'error' => $e->getMessage(),
                'client_id' => $clientId ?? 'non défini',
                'token_url' => $tokenUrl ?? 'non défini',
                'response_body' => $body ?? null,
                'status_code' => $statusCode ?? null,
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
            $senderNumber = env('ORANGE_SMS_SENDER_NUMBER', '2250000');

            $formattedRecipient = $this->formatPhoneNumber($recipient);
            $recipientWithoutPlus = ltrim($formattedRecipient, '+');

            if (!preg_match('/^2250[0-9]{8,10}$/', $recipientWithoutPlus)) {
                throw new \Exception("Numéro de téléphone invalide: {$recipient} (formaté: {$formattedRecipient})");
            }

            $client = new Client($this->sslConfig);
            
            $response = $client->post('https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B' . $senderNumber . '/requests', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token['access_token'],
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'outboundSMSMessageRequest' => [
                        'address' => 'tel:+' . $recipientWithoutPlus,
                        'senderAddress' => 'tel:+' . $senderNumber,
                        'senderName' => $senderName ?: 'SAAR CI',
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

            Log::info('Réponse API Orange pour sendSMS', [
                'status_code' => $statusCode,
                'response_body' => $body,
                'recipient_formatted' => $formattedRecipient,
                'recipient_final' => 'tel:+' . $recipientWithoutPlus,
                'sender_number' => $senderNumber
            ]);

            if ($statusCode !== 201 && $statusCode !== 200) {
                $errorMsg = $result['error_description'] ?? $result['error'] ?? 'Erreur inconnue';
                throw new \Exception("Erreur {$statusCode} lors de l'envoi du SMS: {$errorMsg}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS Orange', [
                'recipient' => $recipient,
                'message' => $message,
                'error' => $e->getMessage(),
                'ssl_config' => $this->sslConfig,
                'environment' => app()->environment(),
                'api_url' => env('ORANGE_SMS_API_SEND_URL'),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Impossible d\'envoyer le SMS: ' . $e->getMessage());
        }
    }

    public function checkDeliveryStatus(string $requestId)
    {
        try {
            $token = $this->getToken();

            $client = new Client($this->sslConfig);

            $response = $client->get(env('ORANGE_SMS_API_STATUS_URL') . '/' . $requestId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token['access_token'],
                    'Accept' => 'application/json'
                ],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if ($statusCode !== 200) {
                $errorMsg = $result['error_description'] ?? $result['error'] ?? 'Erreur inconnue';
                throw new \Exception("Erreur {$statusCode} lors de la vérification du statut: {$errorMsg}");
            }

            return $result;
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

    public function validateConfiguration(): bool
    {
        $requiredEnvVars = [
            'ORANGE_CLIENT_ID',
            'ORANGE_CLIENT_SECRET',
            'ORANGE_SMS_API_TOKEN_URL',
            'ORANGE_SMS_API_SEND_URL',
            'ORANGE_SMS_SENDER_ADDRESS'
        ];

        foreach ($requiredEnvVars as $var) {
            if (empty(env($var))) {
                Log::error("Configuration Orange SMS API manquante: Variable d'environnement {$var} est requise");
                return false;
            }
        }

        return true;
    }

    private function ensureCaCertificateExists(): void
    {
        $certPath = storage_path('app/cacert.pem');
        
        if (!file_exists($certPath) || (time() - filemtime($certPath)) > (30 * 24 * 60 * 60)) {
            try {
                Log::info('Téléchargement du certificat CA racine...');
                
                $storageDir = storage_path('app');
                if (!is_dir($storageDir)) {
                    mkdir($storageDir, 0755, true);
                }
                
                $certContent = file_get_contents('https://curl.se/ca/cacert.pem');
                
                if ($certContent !== false) {
                    file_put_contents($certPath, $certContent);
                    Log::info('Certificat CA téléchargé avec succès', ['path' => $certPath]);
                } else {
                    Log::warning('Impossible de télécharger le certificat CA, utilisation de la configuration sans vérification SSL');
                }
            } catch (\Exception $e) {
                Log::warning('Erreur lors du téléchargement du certificat CA: ' . $e->getMessage());
            }
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

            $client = new Client($this->sslConfig);
            
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
                Log::info('SMS de confirmation sinistre envoyé avec succès', [
                    'recipient_original' => $recipientPhone,
                    'recipient_formatted' => $formattedRecipient,
                    'recipient_final' => 'tel:+' . $recipientWithoutPlus,
                    'numero_sinistre' => $numeroSinistre,
                    'message_length' => strlen($message),
                    'response' => $result
                ]);
            } else {
                $errorMsg = $result['error_description'] ?? $result['error']['message'] ?? $result['error'] ?? 'Erreur inconnue';
                Log::error('Erreur lors de l\'envoi du SMS de confirmation sinistre', [
                    'recipient_original' => $recipientPhone,
                    'recipient_formatted' => $formattedRecipient,
                    'recipient_final' => 'tel:+' . $recipientWithoutPlus,
                    'numero_sinistre' => $numeroSinistre,
                    'status_code' => $statusCode,
                    'response_body' => $body,
                    'error_message' => $errorMsg
                ]);

                throw new \Exception("Erreur {$statusCode} lors de l'envoi du SMS de confirmation: {$errorMsg}");
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

}
