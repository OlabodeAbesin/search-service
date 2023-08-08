<?php

namespace Database\Seeders;

use App\ParkAndRide;
use Illuminate\Database\Seeder;

class ParkAndRideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ParkAndRide::factory()
            ->count(50)
            ->create();
    }
}
