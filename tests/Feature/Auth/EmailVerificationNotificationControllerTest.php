<?php

namespace Tests\Feature\Auth;

use App\Mail\VerifyEmail as VerifyEmailMailable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirects_if_email_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_skips_verification_for_example_com_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-skipped-example');
    }

    public function test_sends_verification_notification()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'user@domain.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
        Mail::assertSent(VerifyEmailMailable::class);
    }
}
