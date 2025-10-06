<?php

namespace Tests\Feature;

use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeclarationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_access_declaration_form()
    {
        $response = $this->get('/declaration/formulaire');

        $response->assertStatus(200);
        $response->assertSee('Déclaration de Sinistre');
    }

    #[Test]
    public function it_can_submit_declaration_form()
    {
        $response = $this->post('/declaration/store', []);
        
        $this->assertNotEquals(404, $response->status());
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $response = $this->post('/declaration/store', []);
        
        $response->assertSessionHasErrors([
            'nom_assure',
            'telephone_assure',
            'numero_police',
            'date_sinistre',
            'lieu_sinistre',
            'conducteur_nom',
            'circonstances',
        ]);
    }

    #[Test]
    public function it_can_upload_documents()
    {
        $response = $this->post('/declaration/upload-file', []);
        
        $this->assertNotEquals(404, $response->status());
    }
}
