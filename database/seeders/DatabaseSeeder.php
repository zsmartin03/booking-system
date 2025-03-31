<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
            'phone_number' => '1234567890',
        ]);

        // Create a provider user
        $provider = User::create([
            'name' => 'Service Provider',
            'email' => 'provider@example.com',
            'password' => bcrypt('password'),
            'role' => 'provider',
            'email_verified_at' => Carbon::now(),
            'phone_number' => '1234567890',
        ]);

        // Create a business for the provider
        $business = Business::create([
            'user_id' => $provider->id,
            'name' => 'Example Business',
            'description' => 'This is an example business',
            'address' => '123 Main St, City',
            'phone_number' => '1234567890',
            'email' => 'business@example.com',
        ]);

        // Add business working hours
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ($days as $day) {
            BusinessWorkingHour::create([
                'business_id' => $business->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }

        // Create a service
        $service = Service::create([
            'business_id' => $business->id,
            'name' => 'Haircut',
            'description' => 'Basic haircut service',
            'price' => 3000, // $30.00
            'duration' => 30, // minutes
            'active' => true,
        ]);

        // Create an employee
        $employeeUser = User::create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
            'role' => 'provider',
            'email_verified_at' => Carbon::now(),
            'phone_number' => '2234567890',
        ]);

        $employee = Employee::create([
            'business_id' => $business->id,
            'user_id' => $employeeUser->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'active' => true,
            'email_verified_at' => Carbon::now(),
            'phone_number' => '3234567890',
        ]);

        // Add employee working hours
        foreach ($days as $day) {
            EmployeeWorkingHour::create([
                'employee_id' => $employee->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }

        // Associate employee with service
        $service->employees()->attach($employee->id);

        // Create a client user
        User::create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'email_verified_at' => Carbon::now(),
            'phone_number' => '5234567890',
        ]);
    }
}
