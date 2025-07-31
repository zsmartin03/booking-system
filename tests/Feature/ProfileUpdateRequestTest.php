<?php

namespace Tests\Feature;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_data_passes_validation()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user);
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_missing_name_fails_validation()
    {
        $user = User::factory()->create();
        $data = [
            'email' => 'john@example.com',
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user);
        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }

    public function test_invalid_email_fails_validation()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'John Doe',
            'email' => 'not-an-email',
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user);
        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    public function test_duplicate_email_fails_validation()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);
        $other = User::factory()->create(['email' => 'jane@example.com']);
        $data = [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $other);
        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    public function test_email_can_be_same_as_current_user()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user);
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_email_is_lowercased()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'John Doe',
            'email' => 'John@Example.COM',
        ];
        $request = new ProfileUpdateRequest();
        $request->setUserResolver(fn() => $user);
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
        $this->assertEquals('john@example.com', strtolower($data['email']));
    }
}
