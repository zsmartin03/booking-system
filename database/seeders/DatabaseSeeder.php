<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

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
