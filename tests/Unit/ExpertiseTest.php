<?php

namespace Tests\Unit;

use App\Models\Expertise;
use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpertiseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_expertise()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'date_expertise' => now(),
            'client_nom' => 'Jean Dupont',
            'collaborateur_nom' => 'Expert Test',
            'collaborateur_telephone' => '0747707127',
            'collaborateur_email' => 'expert@saar.com',
            'lieu_expertise' => 'Abidjan',
            'contact_client' => '0225123456',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
            'operations' => [
                ['libelle' => 'Remplacement pare-brise', 'echange' => true, 'reparation' => false, 'controle' => false, 'peinture' => false],
            ],
        ]);

        $this->assertDatabaseHas('expertises', [
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'client_nom' => 'Jean Dupont',
            'lieu_expertise' => 'Abidjan',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
        ]);
    }

    #[Test]
    public function it_has_required_fields()
    {
        $expertise = new Expertise();
        
        $requiredFields = [
            'sinistre_id',
            'expert_id',
            'date_expertise',
            'client_nom',
            'collaborateur_nom',
            'collaborateur_telephone',
            'collaborateur_email',
            'lieu_expertise',
            'contact_client',
            'vehicule_expertise',
            'operations'
        ];

        foreach ($requiredFields as $field) {
            $this->assertTrue(in_array($field, $expertise->getFillable()));
        }
    }

    #[Test]
    public function it_belongs_to_sinistre()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $expertise->sinistre());
        $this->assertEquals($sinistre->id, $expertise->sinistre->id);
    }

    #[Test]
    public function it_belongs_to_expert()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $expertise->expert());
        $this->assertEquals($user->id, $expertise->expert->id);
    }

    #[Test]
    public function it_stores_operations_as_json()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $operations = [
            ['libelle' => 'Opération 1', 'echange' => true, 'reparation' => false, 'controle' => false, 'peinture' => false],
            ['libelle' => 'Opération 2', 'echange' => false, 'reparation' => true, 'controle' => true, 'peinture' => false],
        ];
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'operations' => $operations,
        ]);

        $this->assertIsArray($expertise->operations);
        $this->assertCount(2, $expertise->operations);
        $this->assertEquals('Opération 1', $expertise->operations[0]['libelle']);
        $this->assertEquals('Opération 2', $expertise->operations[1]['libelle']);
    }

    #[Test]
    public function it_casts_date_expertise_to_date()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'date_expertise' => '2026-02-06',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $expertise->date_expertise);
    }

    #[Test]
    public function it_can_update_expertise()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'lieu_expertise' => 'Abidjan',
        ]);

        $expertise->update(['lieu_expertise' => 'Yamoussoukro']);

        $this->assertDatabaseHas('expertises', [
            'id' => $expertise->id,
            'lieu_expertise' => 'Yamoussoukro',
        ]);
    }

    #[Test]
    public function vehicule_expertise_field_is_fillable()
    {
        $expertise = new Expertise();
        
        $this->assertTrue(in_array('vehicule_expertise', $expertise->getFillable()));
    }
}
