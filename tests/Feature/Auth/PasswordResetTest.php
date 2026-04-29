<?php

use App\Mail\ResetPassword as ResetPasswordMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertSent(ResetPasswordMailable::class);
});

test('reset password screen can be rendered', function () {
    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->get('/reset-password/' . $token);

    $response->assertStatus(200);
});

test('password can be reset with valid token', function () {
    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));
});
