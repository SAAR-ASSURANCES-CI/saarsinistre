<?php

namespace Database\Factories;

use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sinistre>
 */
class SinistreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_sinistre' => 'SIN-' . date('Y') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nom_assure' => $this->faker->name(),
            'email_assure' => $this->faker->unique()->safeEmail(),
            'telephone_assure' => '+225' . $this->faker->numerify('########'),
            'numero_police' => 'POL-' . date('Y') . '-' . $this->faker->numberBetween(1000, 9999),
            'date_sinistre' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'heure_sinistre' => $this->faker->time(),
            'lieu_sinistre' => $this->faker->address(),
            'circonstances' => $this->faker->text(200),
            'conducteur_nom' => $this->faker->name(),
            'constat_autorite' => $this->faker->boolean(),
            'implique_tiers' => $this->faker->boolean(),
            'statut' => $this->faker->randomElement(['en_attente', 'en_cours', 'expertise_requise', 'regle', 'clos']),
            'montant_estime' => $this->faker->numberBetween(50000, 2000000),
            'jours_en_cours' => $this->faker->numberBetween(1, 30),
            'en_retard' => $this->faker->boolean(20),
        ];
    }

    /**
     * Indicate that the sinistre should have a gestionnaire assigned.
     */
    public function withGestionnaire(User $gestionnaire = null): static
    {
        return $this->state(function (array $attributes) use ($gestionnaire) {
            return [
                'gestionnaire_id' => $gestionnaire ? $gestionnaire->id : User::factory()->create(['role' => 'gestionnaire'])->id,
                'date_affectation' => now(),
            ];
        });
    }

    /**
     * Indicate that the sinistre should have an assure assigned.
     */
    public function withAssure(User $assure = null): static
    {
        return $this->state(function (array $attributes) use ($assure) {
            return [
                'assure_id' => $assure ? $assure->id : User::factory()->create(['role' => 'assure'])->id,
            ];
        });
    }
}
