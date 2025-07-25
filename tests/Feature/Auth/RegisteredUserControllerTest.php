<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisteredUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_view_is_accessible()
    {
        $response = $this->get(route('register'));
        $response->assertOk();
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register_and_is_logged_in()
    {
        Event::fake();
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'testuser@domain.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'client',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('status', 'Welcome! Your account has been created successfully.');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@domain.com',
            'role' => 'client',
        ]);
        Event::assertDispatched(Registered::class);
    }

    public function test_registration_skips_event_for_example_com_email_and_marks_verified()
    {
        Event::fake();
        $response = $this->post(route('register'), [
            'name' => 'Example User',
            'email' => 'user@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'provider',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $user = User::where('email', 'user@example.com')->first();
        $this->assertNotNull($user->email_verified_at);
        Event::assertNotDispatched(Registered::class);
    }

    public function test_registration_requires_terms_acceptance()
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Test User',
            'email' => 'fail@domain.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'client',
            // 'terms' => 'on',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('terms');
        $this->assertGuest();
    }

    public function test_registration_requires_valid_role()
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Test User',
            'email' => 'fail2@domain.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'invalid',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}
