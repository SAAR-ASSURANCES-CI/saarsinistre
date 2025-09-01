<?php

namespace Tests\Feature;

use App\Models\User;
use App\Jobs\SendUserCredentialsEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_generates_password_and_queues_email()
    {
        Queue::fake();

        $userData = [
            'nom_complet' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'role' => 'gestionnaire',
        ];

        $response = $this->post(route('dashboard.users.store'), $userData);

        // Vérifier que l'utilisateur a été créé
        $this->assertDatabaseHas('users', [
            'nom_complet' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'role' => 'gestionnaire',
        ]);

        $user = User::where('email', 'jean.dupont@example.com')->first();

        // Vérifier que l'utilisateur a un mot de passe temporaire
        $this->assertNotNull($user->password_temp);
        $this->assertNotNull($user->password_expire_at);
        $this->assertTrue($user->password_expire_at->gt(now()));

        // Vérifier que l'email a été mis en queue
        Queue::assertPushed(SendUserCredentialsEmail::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });

        $response->assertRedirect(route('dashboard.users.index'));
        $response->assertSessionHas('success');
    }

    public function test_password_generation_creates_6_digit_password()
    {
        // Utiliser la réflexion pour tester la méthode privée
        $controller = new \App\Http\Controllers\UserController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('generateTemporaryPassword');
        $method->setAccessible(true);

        $password = $method->invoke($controller);

        // Vérifier que le mot de passe a 6 chiffres
        $this->assertEquals(6, strlen($password));
        $this->assertTrue(is_numeric($password));
    }
}
