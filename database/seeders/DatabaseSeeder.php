<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            BusinessSeeder::class,
            AvailabilityExceptionSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
