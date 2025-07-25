<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirects_if_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);
        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
    }

    public function test_marks_email_as_verified_and_dispatches_event()
    {
        Event::fake();
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);
        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_invalid_signature_redirects_to_login()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]);
        $response = $this->actingAs($user)->get($url);
        $response->assertStatus(403); // Invalid signature should be forbidden
    }
}
