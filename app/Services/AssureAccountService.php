<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendAccountCreationSms;
use Illuminate\Support\Facades\Hash;

class AssureAccountService
{
    public function createAssureAccount(array $data): User
    {
        $numeroAssure = $this->generateAssureNumber();
        $motDePasseTemporaire = $this->generateTemporaryPassword();

        $user = User::create([
            'numero_assure' => $numeroAssure,
            'nom_complet' => $data['nom_assure'],
            'email' => $data['email_assure'] ?? null,
            'password' => Hash::make($motDePasseTemporaire),
            'password_temp' => $motDePasseTemporaire,
            'password_expires_at' => now()->addHours(48),
            'role' => 'assure',
        ]);

        SendAccountCreationSms::dispatch($user, $data['telephone_assure']);

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
}
