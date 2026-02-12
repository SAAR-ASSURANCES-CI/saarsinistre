<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Expertise;
use App\Models\Sinistre;
use App\Models\User;
use App\Services\ExpertisePdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpertisePdfServiceSimplifiedTest extends TestCase
{
    use RefreshDatabase;

    private ExpertisePdfService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExpertisePdfService();
    }

    /** @test */
    public function it_generates_pdf_from_blade_view()
    {
        // Créer les données de test
        $user = User::factory()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'expert@saar.com',
            'phone' => '0747707127',
        ]);

        $sinistre = Sinistre::factory()->create([
            'numero_sinistre' => 'SIN-2026-001',
            'nom_assure' => 'Client Test',
            'telephone_assure' => '0225123456',
        ]);

        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'date_expertise' => now(),
            'client_nom' => 'Client Test',
            'lieu_expertise' => 'Abidjan',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
            'collaborateur_nom' => 'Jean Dupont',
            'collaborateur_telephone' => '0747707127',
            'collaborateur_email' => 'expert@saar.com',
            'contact_client' => '0225123456',
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
        ]);

        // Tester la génération du PDF
        $response = $this->service->previewExpertisePdf($expertise);

        $this->assertNotNull($response);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_downloads_pdf_with_correct_filename()
    {
        $user = User::factory()->create();
        $sinistre = Sinistre::factory()->create(['numero_sinistre' => 'SIN-TEST-123']);
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
        ]);

        $response = $this->service->downloadExpertisePdf($expertise);

        $this->assertNotNull($response);
        $this->assertStringContainsString('Fiche_Expertise_SIN-TEST-123.pdf', $response->headers->get('Content-Disposition'));
    }

    /** @test */
    public function it_handles_empty_operations_gracefully()
    {
        $user = User::factory()->create();
        $sinistre = Sinistre::factory()->create();
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'operations' => [], // Aucune opération
        ]);

        $response = $this->service->previewExpertisePdf($expertise);

        $this->assertNotNull($response);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
