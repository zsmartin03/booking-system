<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_fields_are_assignable()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $data = [
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'title' => 'Test Title',
            'content' => 'Test Content',
            'is_read' => false,
            'sent_at' => now(),
            'read_at' => null,
        ];
        $notification = Notification::create($data);
        $this->assertDatabaseHas('notifications', ['id' => $notification->id] + $data);
    }

    public function test_casts_are_applied_correctly()
    {
        $notification = Notification::factory()->create([
            'is_read' => 1,
            'sent_at' => now(),
            'read_at' => now(),
        ]);
        $this->assertIsBool($notification->is_read);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $notification->sent_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $notification->read_at);
    }

    public function test_user_and_booking_relationships()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
        ]);
        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertInstanceOf(Booking::class, $notification->booking);
    }

    public function test_get_translated_title_attribute()
    {
        $notification = Notification::factory()->make(['title' => 'Test Title']);
        $this->assertEquals(__('Test Title'), $notification->translated_title);
    }

    public function test_get_translated_content_attribute_with_pipe_and_json()
    {
        $notification = Notification::factory()->make([
            'content' => 'messages.booking_status|{"previous_status":"pending","current_status":"confirmed"}'
        ]);
        $translated = __('messages.booking_status', [
            'previous_status' => __('messages.pending'),
            'current_status' => __('messages.confirmed'),
        ]);
        $this->assertEquals($translated, $notification->translated_content);
    }

    public function test_get_translated_content_attribute_without_pipe()
    {
        $notification = Notification::factory()->make(['content' => 'Simple content']);
        $this->assertEquals('Simple content', $notification->translated_content);
    }
}
