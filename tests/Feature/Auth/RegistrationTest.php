<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create Account');
        $response->assertSee('passwordSecurityBox', false);
    }

    public function test_web_registration_rejects_weak_passwords(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'John Weak',
            'email'                 => 'weakweb@example.com',
            'password'              => 'weakpass',
            'password_confirmation' => 'weakpass',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', [
            'email' => 'weakweb@example.com',
        ]);
    }

    public function test_web_registration_succeeds_with_strong_password(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'John Strong',
            'email'                 => 'strongweb@example.com',
            'password'              => 'Str0ngP@ss2026!',
            'password_confirmation' => 'Str0ngP@ss2026!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'email' => 'strongweb@example.com',
        ]);
    }
}
