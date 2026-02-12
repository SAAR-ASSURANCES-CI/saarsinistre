<?php

namespace Tests\Feature;

use App\Models\Expertise;
use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpertiseFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $gestionnaire;
    protected Sinistre $sinistre;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->gestionnaire = User::factory()->create([
            'role' => 'gestionnaire',
            'email' => 'gestionnaire@test.com',
        ]);
        $this->sinistre = Sinistre::factory()->create([
            'assure_id' => $this->gestionnaire->id,
            'nom_assure' => 'Jean Dupont',
            'telephone_assure' => '0225123456',
        ]);
    }

    #[Test]
    public function it_can_save_expertise_via_api()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
                'lieu_expertise' => 'Abidjan',
                'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
                'operations' => [
                    [
                        'libelle' => 'Remplacement pare-brise',
                        'echange' => true,
                        'reparation' => false,
                        'controle' => false,
                        'peinture' => false,
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('expertises', [
            'sinistre_id' => $this->sinistre->id,
            'lieu_expertise' => 'Abidjan',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
        ]);
    }

    #[Test]
    public function it_validates_required_fields_for_expertise()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
              
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lieu_expertise', 'operations']);
    }

    #[Test]
    public function it_requires_at_least_one_operation()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
                'lieu_expertise' => 'Abidjan',
                'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
                'operations' => [], 
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['operations']);
    }

    #[Test]
    public function it_validates_operation_structure()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
                'lieu_expertise' => 'Abidjan',
                'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
                'operations' => [
                    [
                        'echange' => true,
                    ],
                ],
            ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_can_update_existing_expertise()
    {
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $this->sinistre->id,
            'expert_id' => $this->gestionnaire->id,
            'lieu_expertise' => 'Abidjan',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
        ]);

        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
                'lieu_expertise' => 'Yamoussoukro',
                'vehicule_expertise' => 'Honda Civic - CD 5678 EF',
                'operations' => [
                    [
                        'libelle' => 'Nouvelle opération',
                        'echange' => false,
                        'reparation' => true,
                        'controle' => false,
                        'peinture' => false,
                    ],
                ],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expertises', [
            'id' => $expertise->id,
            'lieu_expertise' => 'Yamoussoukro',
            'vehicule_expertise' => 'Honda Civic - CD 5678 EF',
        ]);
    }

    #[Test]
    public function it_can_preview_expertise_pdf()
    {
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $this->sinistre->id,
            'expert_id' => $this->gestionnaire->id,
            'lieu_expertise' => 'Abidjan',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
            'operations' => [
                [
                    'libelle' => 'Test',
                    'echange' => true,
                    'reparation' => false,
                    'controle' => false,
                    'peinture' => false,
                ],
            ],
        ]);

        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            $this->markTestSkipped('Le template Word n\'existe pas.');
        }

        $response = $this->actingAs($this->gestionnaire)
            ->get("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise/preview");

        $this->assertNotEquals(500, $response->getStatusCode());
    }

    #[Test]
    public function it_can_download_expertise_pdf()
    {
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $this->sinistre->id,
            'expert_id' => $this->gestionnaire->id,
            'lieu_expertise' => 'Abidjan',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
            'operations' => [
                [
                    'libelle' => 'Test',
                    'echange' => true,
                    'reparation' => false,
                    'controle' => false,
                    'peinture' => false,
                ],
            ],
        ]);

        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            $this->markTestSkipped('Le template Word n\'existe pas.');
        }

        $response = $this->actingAs($this->gestionnaire)
            ->get("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise/pdf");

        $this->assertNotEquals(500, $response->getStatusCode());
    }

    #[Test]
    public function it_requires_authentication_to_access_expertise()
    {
        $response = $this->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
            'lieu_expertise' => 'Abidjan',
            'operations' => [
                ['libelle' => 'Test', 'echange' => true, 'reparation' => false, 'controle' => false, 'peinture' => false],
            ],
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_stores_vehicule_expertise_field()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
                'lieu_expertise' => 'Abidjan',
                'vehicule_expertise' => 'Mercedes Benz - XY 9876 ZA',
                'operations' => [
                    [
                        'libelle' => 'Contrôle moteur',
                        'echange' => false,
                        'reparation' => false,
                        'controle' => true,
                        'peinture' => false,
                    ],
                ],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expertises', [
            'sinistre_id' => $this->sinistre->id,
            'vehicule_expertise' => 'Mercedes Benz - XY 9876 ZA',
        ]);
    }

    #[Test]
    public function it_can_handle_multiple_operations_in_expertise()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->postJson("/gestionnaires/dashboard/sinistres/{$this->sinistre->id}/expertise", [
                'lieu_expertise' => 'Abidjan',
                'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
                'operations' => [
                    ['libelle' => 'Opération 1', 'echange' => true, 'reparation' => false, 'controle' => false, 'peinture' => false],
                    ['libelle' => 'Opération 2', 'echange' => false, 'reparation' => true, 'controle' => false, 'peinture' => false],
                    ['libelle' => 'Opération 3', 'echange' => false, 'reparation' => false, 'controle' => true, 'peinture' => false],
                    ['libelle' => 'Opération 4', 'echange' => false, 'reparation' => false, 'controle' => false, 'peinture' => true],
                    ['libelle' => 'Opération 5', 'echange' => true, 'reparation' => false, 'controle' => true, 'peinture' => false],
                ],
            ]);

        $response->assertStatus(200);

        $expertise = Expertise::where('sinistre_id', $this->sinistre->id)->first();
        
        $this->assertNotNull($expertise);
        $this->assertIsArray($expertise->operations);
        $this->assertCount(5, $expertise->operations);
    }
}
