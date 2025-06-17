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
            'email' => 'admin@saar.ci'
        ], [
            'nom_complet' => 'Administrateur SAAR',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'actif' => true,
            'limite_sinistres' => 50
        ]);

        User::firstOrCreate([
            'email' => 'gestionnaire@saar.ci'
        ], [
            'nom_complet' => 'Gestionnaire Principal',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'actif' => true,
            'limite_sinistres' => 20
        ]);

        User::firstOrCreate([
            'email' => 'jean.dupont@saar.ci'
        ], [
            'nom_complet' => 'Jean Dupont',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'actif' => true,
            'limite_sinistres' => 20
        ]);

        User::firstOrCreate([
            'email' => 'marie.martin@saar.ci'
        ], [
            'nom_complet' => 'Marie Martin',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'actif' => true,
            'limite_sinistres' => 25
        ]);

        User::firstOrCreate([
            'email' => 'paul.durand@saar.ci'
        ], [
            'nom_complet' => 'Paul Durand',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'actif' => true,
            'limite_sinistres' => 20
        ]);

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Admin : admin@saar.ci / password');
        $this->command->info('Gestionnaire : gestionnaire@saar.ci / password');
    }
}
