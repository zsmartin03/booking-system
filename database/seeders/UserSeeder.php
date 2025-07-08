<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
            'phone_number' => '1234567890',
        ]);

        // Create provider users
        $providers = [
            ['name' => 'Provider One', 'email' => 'provider@example.com', 'phone' => '2345678901'],
            ['name' => 'Provider Two', 'email' => 'provider2@example.com', 'phone' => '3456789012'],
            ['name' => 'Provider Three', 'email' => 'provider3@example.com', 'phone' => '4567890123'],
            ['name' => 'Provider Four', 'email' => 'provider4@example.com', 'phone' => '5678901234'],
            ['name' => 'Provider Five', 'email' => 'provider5@example.com', 'phone' => '6789012345'],
            ['name' => 'Provider Six', 'email' => 'provider6@example.com', 'phone' => '7890123456'],
            ['name' => 'Provider Seven', 'email' => 'provider7@example.com', 'phone' => '8901234567'],
            ['name' => 'Provider Eight', 'email' => 'provider8@example.com', 'phone' => '9012345678'],
            ['name' => 'Provider Nine', 'email' => 'provider9@example.com', 'phone' => '0123456789'],
            ['name' => 'Provider Ten', 'email' => 'provider10@example.com', 'phone' => '1234567890'],
            ['name' => 'Provider Eleven', 'email' => 'provider11@example.com', 'phone' => '2345678902'],
            ['name' => 'Provider Twelve', 'email' => 'provider12@example.com', 'phone' => '3456789013'],
            ['name' => 'Provider Thirteen', 'email' => 'provider13@example.com', 'phone' => '4567890124'],
            ['name' => 'Provider Fourteen', 'email' => 'provider14@example.com', 'phone' => '5678901235'],
            ['name' => 'Provider Fifteen', 'email' => 'provider15@example.com', 'phone' => '6789012346'],
            ['name' => 'Provider Sixteen', 'email' => 'provider16@example.com', 'phone' => '7890123457'],
            ['name' => 'Provider Seventeen', 'email' => 'provider17@example.com', 'phone' => '8901234568'],
            ['name' => 'Provider Eighteen', 'email' => 'provider18@example.com', 'phone' => '9012345679'],
            ['name' => 'Provider Nineteen', 'email' => 'provider19@example.com', 'phone' => '0123456780'],
            ['name' => 'Provider Twenty', 'email' => 'provider20@example.com', 'phone' => '1234567891'],
        ];

        foreach ($providers as $provider) {
            User::create([
                'name' => $provider['name'],
                'email' => $provider['email'],
                'password' => bcrypt('password'),
                'role' => 'provider',
                'email_verified_at' => Carbon::now(),
                'phone_number' => $provider['phone'],
            ]);
        }

        // Create client users
        $clients = [
            ['name' => 'Client', 'email' => 'client@example.com', 'phone' => '7890123456'],
            ['name' => 'Maria Garcia', 'email' => 'maria.garcia@example.com', 'phone' => '8901234567'],
            ['name' => 'James Smith', 'email' => 'james.smith@example.com', 'phone' => '9012345678'],
            ['name' => 'Emily Davis', 'email' => 'emily.davis@example.com', 'phone' => '0123456789'],
            ['name' => 'Michael Brown', 'email' => 'michael.brown@example.com', 'phone' => '1234509876'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah.wilson@example.com', 'phone' => '2345610987'],
            ['name' => 'David Miller', 'email' => 'david.miller@example.com', 'phone' => '3456721098'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.anderson@example.com', 'phone' => '4567832109'],
            ['name' => 'Robert Taylor', 'email' => 'robert.taylor@example.com', 'phone' => '5678943210'],
            ['name' => 'Jennifer Moore', 'email' => 'jennifer.moore@example.com', 'phone' => '6789054321'],
            ['name' => 'Christopher Lee', 'email' => 'christopher.lee@example.com', 'phone' => '7890165432'],
            ['name' => 'Amanda White', 'email' => 'amanda.white@example.com', 'phone' => '8901276543'],
            ['name' => 'Kevin Thompson', 'email' => 'kevin.thompson@example.com', 'phone' => '9012387654'],
            ['name' => 'Michelle Clark', 'email' => 'michelle.clark@example.com', 'phone' => '0123498765'],
            ['name' => 'Ryan Rodriguez', 'email' => 'ryan.rodriguez@example.com', 'phone' => '1234509876'],
        ];

        foreach ($clients as $client) {
            User::create([
                'name' => $client['name'],
                'email' => $client['email'],
                'password' => bcrypt('password'),
                'role' => 'client',
                'email_verified_at' => Carbon::now(),
                'phone_number' => $client['phone'],
            ]);
        }
    }
}
