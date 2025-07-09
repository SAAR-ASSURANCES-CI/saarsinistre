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
            'email' => 'AKOMPISSI@saar-assurances.com'
        ], [
            'nom_complet' => 'Administrateur SAAR',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'actif' => true,
            'limite_sinistres' => 50
        ]);

        User::firstOrCreate([
            'email' => 'k.kompissi@saarvie.ci'
        ], [
            'nom_complet' => 'Gestionnaire Principal',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'actif' => true,
            'limite_sinistres' => 20
        ]);

        $this->command->info('Utilisateurs de test créés avec succès !');
    }
}
