<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_displays_profile_edit_view()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
        $response->assertViewHas('user', $user);
    }

    public function test_update_profile_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'phone_number' => '1234567890',
        ];
        $response = $this->patch(route('profile.update'), $data);
        $response->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'phone_number' => '1234567890',
        ]);
    }

    public function test_update_profile_with_avatar_upload()
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => null]);
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data = [
            'name' => 'Avatar User',
            'email' => 'avatar@example.com',
            'phone_number' => '555-5555',
            'avatar' => $file,
        ];
        $response = $this->patch(route('profile.update'), $data);
        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_update_profile_avatar_replaces_old_avatar()
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => 'avatars/old.jpg']);
        Storage::disk('public')->put('avatars/old.jpg', 'dummy');
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('newavatar.jpg');
        $data = [
            'name' => 'Avatar User',
            'email' => 'avatar2@example.com',
            'phone_number' => '555-5555',
            'avatar' => $file,
        ];
        $response = $this->patch(route('profile.update'), $data);
        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertNotEquals('avatars/old.jpg', $user->avatar);
        $this->assertFalse(Storage::disk('public')->exists('avatars/old.jpg'));
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_update_profile_validation_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'name' => '', // required
            'email' => 'not-an-email', // invalid
        ];
        $response = $this->patch(route('profile.update'), $data);
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_update_password_successfully()
    {
        $user = User::factory()->create(['password' => bcrypt('oldpassword')]);
        $this->actingAs($user);
        $data = [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];
        $response = $this->post(route('profile.update-password'), $data);
        $response->assertSessionHas('status', 'Password updated successfully!');
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->password));
    }

    public function test_update_password_validation_fails()
    {
        $user = User::factory()->create(['password' => bcrypt('oldpassword')]);
        $this->actingAs($user);
        $data = [
            'current_password' => '',
            'password' => 'short',
            'password_confirmation' => 'different',
        ];
        $response = $this->post(route('profile.update-password'), $data);
        $response->assertSessionHasErrors(['current_password', 'password']);
    }

    public function test_remove_avatar_successfully()
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => 'avatars/test.jpg']);
        Storage::disk('public')->put('avatars/test.jpg', 'dummy');
        $this->actingAs($user);
        $response = $this->delete(route('profile.remove-avatar'));
        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNull($user->avatar);
        $this->assertFalse(Storage::disk('public')->exists('avatars/test.jpg'));
    }

    public function test_remove_avatar_when_none_exists()
    {
        $user = User::factory()->create(['avatar' => null]);
        $this->actingAs($user);
        $response = $this->delete(route('profile.remove-avatar'));
        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertNull($user->avatar);
    }
}
