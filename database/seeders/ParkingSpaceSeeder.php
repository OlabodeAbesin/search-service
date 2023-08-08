<?php

namespace Database\Seeders;

use App\ParkingSpace;
use Illuminate\Database\Seeder;

class ParkingSpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ParkingSpace::factory()
            ->count(50)
            ->create();
    }
}
