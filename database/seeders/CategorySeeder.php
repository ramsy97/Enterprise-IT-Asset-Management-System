<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptop', 'code_prefix' => 'LAP', 'description' => 'Portable computers for employees'],
            ['name' => 'Desktop', 'code_prefix' => 'DESK', 'description' => 'Desktop workstations'],
            ['name' => 'Monitor', 'code_prefix' => 'MON', 'description' => 'Display monitors'],
            ['name' => 'Printer', 'code_prefix' => 'PRT', 'description' => 'Printers and multifunction devices'],
            ['name' => 'Server', 'code_prefix' => 'SRV', 'description' => 'Physical and rack servers'],
            ['name' => 'Network Device', 'code_prefix' => 'NET', 'description' => 'Switches, routers, access points'],
            ['name' => 'Smartphone', 'code_prefix' => 'MOB', 'description' => 'Company mobile devices'],
            ['name' => 'Tablet', 'code_prefix' => 'TAB', 'description' => 'Tablet devices'],
            ['name' => 'Peripheral', 'code_prefix' => 'PER', 'description' => 'Keyboards, mice, docks, accessories'],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(['code_prefix' => $category['code_prefix']], $category);
        }
    }
}
