<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@saar-assurances.com'
        ], [
            'nom_complet' => 'Administrateur SAAR',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'actif' => true,
        ]);

        $this->command->info('Utilisateurs créés avec succès !');
    }
}
