<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sinistre;
use App\Models\PasswordResetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_assure_can_request_password_reset()
    {
        // Créer un utilisateur assuré
        $user = User::factory()->create([
            'role' => 'assure',
            'username' => 'testuser'
        ]);

        // Créer un sinistre pour cet utilisateur
        $sinistre = Sinistre::factory()->create([
            'assure_id' => $user->id,
            'telephone_assure' => '+2250701234567'
        ]);

        $response = $this->post('/password/reset', [
            'telephone' => '0701234567'
        ]);

        $response->assertRedirect('/password/reset/verify');
        $response->assertSessionHas('success');
        $response->assertSessionHas('telephone', '+2250701234567');

        // Vérifier qu'un code a été créé
        $this->assertDatabaseHas('password_reset_codes', [
            'telephone' => '+2250701234567'
        ]);
    }

    public function test_assure_cannot_request_reset_with_invalid_phone()
    {
        $response = $this->post('/password/reset', [
            'telephone' => '123'
        ]);

        $response->assertSessionHasErrors(['telephone']);
    }

    public function test_assure_cannot_request_reset_with_unregistered_phone()
    {
        $response = $this->post('/password/reset', [
            'telephone' => '0701234567'
        ]);

        $response->assertSessionHasErrors(['telephone']);
    }

    public function test_assure_can_verify_reset_code()
    {
        // Créer un code de réinitialisation
        $code = PasswordResetCode::create([
            'telephone' => '+2250701234567',
            'code' => '123456',
            'expires_at' => Carbon::now()->addMinutes(10),
            'used' => false
        ]);

        $response = $this->withSession(['telephone' => '+2250701234567'])
            ->post('/password/reset/verify', [
                'code' => '123456'
            ]);

        $response->assertRedirect('/password/reset/new');
        $response->assertSessionHas('success');
        $response->assertSessionHas('verified_telephone', '+2250701234567');

        // Vérifier que le code a été marqué comme utilisé
        $this->assertDatabaseHas('password_reset_codes', [
            'id' => $code->id,
            'used' => true
        ]);
    }

    public function test_assure_cannot_verify_invalid_code()
    {
        $response = $this->withSession(['telephone' => '+2250701234567'])
            ->post('/password/reset/verify', [
                'code' => '000000'
            ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_assure_can_set_new_password()
    {
        // Créer un utilisateur et un sinistre
        $user = User::factory()->create([
            'role' => 'assure',
            'username' => 'testuser'
        ]);

        $sinistre = Sinistre::factory()->create([
            'assure_id' => $user->id,
            'telephone_assure' => '+2250701234567'
        ]);

        $response = $this->withSession(['verified_telephone' => '+2250701234567'])
            ->post('/password/reset/new', [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123'
            ]);

        $response->assertRedirect('/login/assure');
        $response->assertSessionHas('success');

        // Vérifier que le mot de passe a été mis à jour
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_password_must_be_confirmed()
    {
        $response = $this->withSession(['verified_telephone' => '+2250701234567'])
            ->post('/password/reset/new', [
                'password' => 'newpassword123',
                'password_confirmation' => 'differentpassword'
            ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_password_must_be_minimum_length()
    {
        $response = $this->withSession(['verified_telephone' => '+2250701234567'])
            ->post('/password/reset/new', [
                'password' => '123',
                'password_confirmation' => '123'
            ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_expired_codes_are_invalid()
    {
        // Créer un code expiré
        $code = PasswordResetCode::create([
            'telephone' => '+2250701234567',
            'code' => '123456',
            'expires_at' => Carbon::now()->subMinutes(1),
            'used' => false
        ]);

        $response = $this->withSession(['telephone' => '+2250701234567'])
            ->post('/password/reset/verify', [
                'code' => '123456'
            ]);

        $response->assertSessionHasErrors(['code']);
    }
}
