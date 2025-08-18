<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PasswordResetCode;
use Carbon\Carbon;

class PasswordResetCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyer les anciens codes
        PasswordResetCode::truncate();

        // Créer quelques codes de test (optionnel)
        PasswordResetCode::create([
            'telephone' => '+2250701234567',
            'code' => '123456',
            'expires_at' => Carbon::now()->addMinutes(10),
            'used' => false
        ]);

        $this->command->info('Codes de réinitialisation de mot de passe créés avec succès !');
    }
}
