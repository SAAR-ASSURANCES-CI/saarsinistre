<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'nom_complet' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'nom_complet' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_can_hash_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertTrue(Hash::check('password', $user->password));
    }

    #[Test]
    public function it_has_required_fields()
    {
        $user = new User();
        
        $this->assertTrue(in_array('nom_complet', $user->getFillable()));
        $this->assertTrue(in_array('email', $user->getFillable()));
    }
}
