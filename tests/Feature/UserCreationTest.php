<?php

namespace Tests\Feature;

use App\Models\User;
use App\Jobs\SendUserCredentialsEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_generates_password_and_queues_email()
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $userData = [
            'nom_complet' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'role' => 'gestionnaire',
        ];

        $response = $this->actingAs($admin)
            ->post(route('gestionnaires.dashboard.users.store'), $userData);

        
        $this->assertDatabaseHas('users', [
            'nom_complet' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'role' => 'gestionnaire',
        ]);

        $user = User::where('email', 'jean.dupont@example.com')->first();

        $this->assertNotNull($user->password_temp);
        $this->assertNotNull($user->password_expire_at);
        $this->assertTrue($user->password_expire_at->gt(now()));

        Queue::assertPushed(SendUserCredentialsEmail::class, function ($job) use ($user) {
            
            $reflection = new \ReflectionClass($job);
            $userProperty = $reflection->getProperty('user');
            $userProperty->setAccessible(true);
            $jobUser = $userProperty->getValue($job);
            return $jobUser->id === $user->id;
        });

        $response->assertRedirect(route('gestionnaires.dashboard.users.index'));
        $response->assertSessionHas('success');
    }

    public function test_password_generation_creates_6_digit_password()
    {
        $controller = new \App\Http\Controllers\UserController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('generateTemporaryPassword');
        $method->setAccessible(true);

        $password = $method->invoke($controller);

        $this->assertEquals(6, strlen($password));
        $this->assertTrue(is_numeric($password));
    }

    public function test_can_create_user_with_expert_role()
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $userData = [
            'nom_complet' => 'Expert Test',
            'email' => 'expert@example.com',
            'role' => 'expert',
        ];

        $response = $this->actingAs($admin)
            ->post(route('gestionnaires.dashboard.users.store'), $userData);

        $this->assertDatabaseHas('users', [
            'nom_complet' => 'Expert Test',
            'email' => 'expert@example.com',
            'role' => 'expert',
        ]);

        $response->assertRedirect(route('gestionnaires.dashboard.users.index'));
        $response->assertSessionHas('success');
    }

    public function test_can_update_user_to_expert_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'gestionnaire']);

        $response = $this->actingAs($admin)
            ->putJson(route('gestionnaires.dashboard.users.update', $user->id), [
                'nom_complet' => $user->nom_complet,
                'email' => $user->email,
                'role' => 'expert',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'expert',
        ]);
    }
}
