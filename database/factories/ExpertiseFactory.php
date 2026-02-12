<?php

namespace Database\Factories;

use App\Models\Expertise;
use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expertise>
 */
class ExpertiseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_expertise' => $this->faker->dateTimeBetween('-10 days', 'now'),
            'client_nom' => $this->faker->name(),
            'collaborateur_nom' => $this->faker->name(),
            'collaborateur_telephone' => '0747707127/0711236714',
            'collaborateur_email' => $this->faker->safeEmail(),
            'lieu_expertise' => $this->faker->city(),
            'contact_client' => '+225' . $this->faker->numerify('########'),
            'vehicule_expertise' => $this->faker->randomElement([
                'Toyota Corolla - AB 1234 CD',
                'Honda Civic - EF 5678 GH',
                'Mercedes Benz - IJ 9012 KL',
                'Nissan Patrol - MN 3456 OP',
            ]),
            'operations' => [
                [
                    'libelle' => 'Remplacement pare-brise',
                    'echange' => true,
                    'reparation' => false,
                    'controle' => false,
                    'peinture' => false,
                ],
                [
                    'libelle' => 'Réparation portière avant',
                    'echange' => false,
                    'reparation' => true,
                    'controle' => true,
                    'peinture' => true,
                ],
            ],
        ];
    }

    /**
     * Indicate that the expertise should have a specific sinistre.
     */
    public function forSinistre(Sinistre $sinistre): static
    {
        return $this->state(function (array $attributes) use ($sinistre) {
            return [
                'sinistre_id' => $sinistre->id,
                'client_nom' => $sinistre->nom_assure,
                'contact_client' => $sinistre->telephone_assure,
            ];
        });
    }

    /**
     * Indicate that the expertise should have a specific expert.
     */
    public function byExpert(User $expert): static
    {
        return $this->state(function (array $attributes) use ($expert) {
            return [
                'expert_id' => $expert->id,
                'collaborateur_nom' => $expert->nom_complet ?? $expert->nom . ' ' . $expert->prenom,
                'collaborateur_email' => $expert->email,
            ];
        });
    }

    /**
     * Indicate that the expertise should have multiple operations.
     */
    public function withOperations(int $count = 3): static
    {
        return $this->state(function (array $attributes) use ($count) {
            $operations = [];
            $libelles = [
                'Remplacement pare-brise',
                'Réparation portière avant gauche',
                'Contrôle système de freinage',
                'Peinture capot',
                'Changement rétroviseur droit',
                'Réparation bumper arrière',
                'Remplacement phare avant',
                'Contrôle suspension',
            ];

            for ($i = 0; $i < min($count, count($libelles)); $i++) {
                $operations[] = [
                    'libelle' => $libelles[$i],
                    'echange' => $this->faker->boolean(),
                    'reparation' => $this->faker->boolean(),
                    'controle' => $this->faker->boolean(),
                    'peinture' => $this->faker->boolean(),
                ];
            }

            return [
                'operations' => $operations,
            ];
        });
    }
}
