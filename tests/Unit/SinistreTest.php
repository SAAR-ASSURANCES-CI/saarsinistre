<?php

namespace Tests\Unit;

use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SinistreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_sinistre()
    {
        $user = User::factory()->create();
        
        $sinistre = Sinistre::factory()->create([
            'nom_assure' => 'John Doe',
            'email_assure' => 'john@example.com',
            'telephone_assure' => '+225123456789',
            'assure_id' => $user->id,
        ]);

        $this->assertDatabaseHas('sinistres', [
            'nom_assure' => 'John Doe',
            'email_assure' => 'john@example.com',
            'telephone_assure' => '+225123456789',
        ]);
    }

    #[Test]
    public function it_generates_sinistre_number()
    {
        $user = User::factory()->create();
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $this->assertNotNull($sinistre->numero_sinistre);
        $this->assertStringStartsWith('APP-', $sinistre->numero_sinistre);
    }

    #[Test]
    public function it_has_required_fields()
    {
        $sinistre = new Sinistre();
        
        $requiredFields = [
            'nom_assure',
            'email_assure', 
            'telephone_assure',
            'numero_police',
            'date_sinistre',
            'lieu_sinistre',
            'conducteur_nom'
        ];

        foreach ($requiredFields as $field) {
            $this->assertTrue(in_array($field, $sinistre->getFillable()));
        }
    }

    #[Test]
    public function it_can_have_documents()
    {
        $user = User::factory()->create();
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $sinistre->documents());
    }

    #[Test]
    public function it_can_have_tiers()
    {
        $user = User::factory()->create();
        $sinistre = Sinistre::factory()->create(['assure_id' => $user->id]);
        
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $sinistre->tiers());
    }
}
