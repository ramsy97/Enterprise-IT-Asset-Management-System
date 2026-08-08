<?php

namespace Database\Seeders;

use App\Models\AssetLocation;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'HQ - Main Office', 'building' => 'Headquarters', 'floor' => '1-12', 'room' => 'Various', 'city' => 'Jakarta'],
            ['name' => 'HQ - IT Server Room', 'building' => 'Headquarters', 'floor' => 'B1', 'room' => 'Data Center', 'city' => 'Jakarta'],
            ['name' => 'Finance Department', 'building' => 'Tower A', 'floor' => '5', 'room' => 'Room 5A', 'city' => 'Jakarta'],
            ['name' => 'Operations Department', 'building' => 'Tower A', 'floor' => '7', 'room' => 'Room 7B', 'city' => 'Jakarta'],
            ['name' => 'Marketing Department', 'building' => 'Tower B', 'floor' => '3', 'room' => 'Room 3C', 'city' => 'Jakarta'],
            ['name' => 'Sales Department', 'building' => 'Tower B', 'floor' => '4', 'room' => 'Room 4A', 'city' => 'Jakarta'],
            ['name' => 'IT Helpdesk', 'building' => 'Headquarters', 'floor' => '2', 'room' => 'Room 2D', 'city' => 'Jakarta'],
            ['name' => 'Storage Room', 'building' => 'Headquarters', 'floor' => 'B2', 'room' => 'Warehouse', 'city' => 'Jakarta'],
            ['name' => 'Surabaya Branch', 'building' => 'Branch Office', 'floor' => '2', 'room' => 'Various', 'city' => 'Surabaya'],
            ['name' => 'Bandung Branch', 'building' => 'Branch Office', 'floor' => '1', 'room' => 'Various', 'city' => 'Bandung'],
        ];

        foreach ($locations as $location) {
            AssetLocation::firstOrCreate(['name' => $location['name']], $location);
        }
    }
}
