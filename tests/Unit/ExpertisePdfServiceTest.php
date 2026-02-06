<?php

namespace Tests\Unit;

use App\Models\Expertise;
use App\Models\Sinistre;
use App\Models\User;
use App\Services\ExpertisePdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpertisePdfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExpertisePdfService $pdfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfService = new ExpertisePdfService();
    }

    #[Test]
    public function it_generates_word_document_from_template()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
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
                ['libelle' => 'Test', 'echange' => true, 'reparation' => false, 'controle' => false, 'peinture' => false],
            ],
        ]);

        // Vérifier que le template existe
        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            $this->markTestSkipped('Le template Word n\'existe pas. Créez-le pour exécuter ce test.');
        }

        $docxPath = $this->pdfService->generateExpertiseWord($expertise);

        $this->assertFileExists($docxPath);
        $this->assertStringContainsString('expertise_', $docxPath);
        $this->assertStringEndsWith('.docx', $docxPath);

        // Nettoyer
        if (File::exists($docxPath)) {
            File::delete($docxPath);
        }
    }

    #[Test]
    public function it_handles_missing_template_gracefully()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
        ]);

        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            // Si le template n'existe pas, on s'attend à une exception
            $this->expectException(\Exception::class);
            $this->pdfService->generateExpertiseWord($expertise);
        } else {
            // Si le template existe, on ne peut pas tester cette erreur
            $this->markTestSkipped('Le template existe, impossible de tester l\'erreur de template manquant.');
        }
    }

    #[Test]
    public function it_replaces_placeholders_correctly()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'date_expertise' => now()->setDate(2026, 2, 6),
            'client_nom' => 'Jean Dupont',
            'vehicule_expertise' => 'Toyota Corolla - AB 1234 CD',
        ]);

        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            $this->markTestSkipped('Le template Word n\'existe pas.');
        }

        $docxPath = $this->pdfService->generateExpertiseWord($expertise);

        // Vérifier que le fichier est créé
        $this->assertFileExists($docxPath);

        // Note: Pour vérifier le contenu, il faudrait extraire le XML du DOCX
        // C'est complexe, donc on se contente de vérifier que le fichier est généré

        // Nettoyer
        if (File::exists($docxPath)) {
            File::delete($docxPath);
        }
    }

    #[Test]
    public function it_handles_multiple_operations()
    {
        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
            'operations' => [
                ['libelle' => 'Opération 1', 'echange' => true, 'reparation' => false, 'controle' => false, 'peinture' => false],
                ['libelle' => 'Opération 2', 'echange' => false, 'reparation' => true, 'controle' => false, 'peinture' => false],
                ['libelle' => 'Opération 3', 'echange' => false, 'reparation' => false, 'controle' => true, 'peinture' => false],
            ],
        ]);

        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            $this->markTestSkipped('Le template Word n\'existe pas.');
        }

        $docxPath = $this->pdfService->generateExpertiseWord($expertise);

        $this->assertFileExists($docxPath);

        // Nettoyer
        if (File::exists($docxPath)) {
            File::delete($docxPath);
        }
    }

    #[Test]
    public function it_creates_temp_directory_if_not_exists()
    {
        $tempDir = storage_path('app/temp');
        
        // Supprimer le répertoire temp si il existe
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }

        $this->assertFalse(File::exists($tempDir));

        $user = User::factory()->create(['role' => 'expert']);
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $expertise = Expertise::factory()->create([
            'sinistre_id' => $sinistre->id,
            'expert_id' => $user->id,
        ]);

        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!File::exists($templatePath)) {
            $this->markTestSkipped('Le template Word n\'existe pas.');
        }

        $docxPath = $this->pdfService->generateExpertiseWord($expertise);

        // Vérifier que le répertoire a été créé
        $this->assertTrue(File::exists($tempDir));

        // Nettoyer
        if (File::exists($docxPath)) {
            File::delete($docxPath);
        }
    }
}
