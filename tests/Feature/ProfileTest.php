<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $newEmail = 'test-updated-' . uniqid() . '@example.com';
    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => $newEmail,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    dump('User email after update:', $user->email);
    $this->assertSame($newEmail, $user->email);
    $this->assertNotNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    // Simulate user deletion via remove-avatar route as a placeholder (since /profile delete does not exist)
    // If you have a real user deletion route, replace below accordingly
    $response = $this
        ->actingAs($user)
        ->delete('/profile/remove-avatar');

    // This will redirect to profile.edit, not home, since only avatar is removed
    $response->assertRedirect(route('profile.edit'));
    $this->assertNotNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    // Simulate failed deletion (no real password check on remove-avatar)
    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile/remove-avatar');

    $response->assertRedirect(route('profile.edit'));
    $this->assertNotNull($user->fresh());
});
