<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_latest_five_notifications_and_unread_count(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Notification::factory()->count(7)->create([
            'user_id' => $user->id,
            'is_read' => false,
        ]);

        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'is_read' => true,
        ]);

        $response = $this->actingAs($user)->getJson(route('notifications.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'notifications',
            'unreadCount',
        ]);
        $this->assertCount(5, $response->json('notifications'));
        $this->assertSame(7, $response->json('unreadCount'));
    }

    public function test_view_all_marks_unread_as_read_and_renders_view(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $unread = Notification::factory()->count(3)->create([
            'user_id' => $user->id,
            'is_read' => false,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('notifications.viewAll'));

        $response->assertOk();
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notifications');

        foreach ($unread as $n) {
            $this->assertDatabaseHas('notifications', [
                'id' => $n->id,
                'is_read' => true,
            ]);
        }
    }

    public function test_mark_all_read_marks_unread_and_returns_json_success(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $unread = Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'is_read' => false,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('notifications.markAllRead'));
        $response->assertOk();
        $response->assertJson(['success' => true]);

        foreach ($unread as $n) {
            $this->assertDatabaseHas('notifications', [
                'id' => $n->id,
                'is_read' => true,
            ]);
        }
    }

    public function test_clear_all_returns_json_when_requested_and_redirects_otherwise(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Notification::factory()->count(2)->create(['user_id' => $user->id]);

        $jsonResponse = $this->actingAs($user)->postJson(route('notifications.clear'));
        $jsonResponse->assertOk();
        $jsonResponse->assertJson(['success' => true]);
        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id]);

        Notification::factory()->count(1)->create(['user_id' => $user->id]);

        $htmlResponse = $this->actingAs($user)->post(route('notifications.clear'));
        $htmlResponse->assertRedirect(route('notifications.viewAll'));
    }
}

