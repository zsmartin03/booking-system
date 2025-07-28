<?php

namespace Tests\Unit;

use App\Models\BusinessWorkingHour;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessWorkingHourTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_business()
    {
        $hour = BusinessWorkingHour::factory()->create();
        $this->assertInstanceOf(Business::class, $hour->business);
    }

    public function test_fillable_fields()
    {
        $business = Business::factory()->create();
        $data = [
            'business_id' => $business->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ];
        $hour = BusinessWorkingHour::create($data);
        $this->assertEquals($data['business_id'], $hour->business_id);
        $this->assertEquals($data['day_of_week'], $hour->day_of_week);
        $this->assertEquals($data['start_time'], $hour->start_time);
        $this->assertEquals($data['end_time'], $hour->end_time);
    }
}
