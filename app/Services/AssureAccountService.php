<?php

namespace App\Services;

use App\Models\User;
use App\Services\OrangeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AssureAccountService
{
    public function createAssureAccount(array $data, OrangeService $orangeService): User
    {
        $numeroAssure = $this->generateAssureNumber();
        $motDePasseTemporaire = $this->generateTemporaryPassword();
        $username = $this->generateUniqueUsername($data['nom_assure']);

        $user = User::create([
            'numero_assure' => $numeroAssure,
            'nom_complet' => $data['nom_assure'],
            'email' => $data['email_assure'] ?? null,
            'username' => $username,
            'password' => Hash::make($motDePasseTemporaire),
            'password_temp' => $motDePasseTemporaire,
            'password_expire_at' => now()->addHours(48),
            'role' => 'assure',
        ]);

        try {
            $nomFormate = strtoupper(explode(' ', trim($user->nom_complet ?: 'CLIENT'))[0]);
            
            $message = "SAAR ASSURANCES\nCher(e) {$nomFormate}, votre espace client est pret :\nIdentifiant: {$user->username}\nCode: {$motDePasseTemporaire}\nValable 48h";

            $telephoneSMS = $this->formatPhoneNumber($data['telephone_assure']);
            $orangeService->sendSMS($telephoneSMS, $message, 'SAAR CI');

            Log::info('SMS de création de compte envoyé avec succès', [
                'user_id' => $user->id,
                'username' => $user->username,
                'telephone' => $data['telephone_assure']
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de création de compte: ' . $e->getMessage());
        }

        return $user;
    }


    protected function generateAssureNumber(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        $prefix = 'SAAR-';

        for ($i = 0; $i < 4; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $prefix . $randomString;
    }

    protected function generateTemporaryPassword(): string
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Génère un username unique basé sur le nom complet
     */
    public function generateUniqueUsername(string $nomComplet): string
    {
        $nomComplet = mb_strtolower($nomComplet, 'UTF-8');
        
        $base = strtolower(preg_replace('/[^a-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nomComplet)));
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }
        return $username;
    }

    /**
     * Formater le numéro de téléphone
     */
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
