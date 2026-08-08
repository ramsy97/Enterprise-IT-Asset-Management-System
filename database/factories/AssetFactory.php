<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $categories = AssetCategory::pluck('id')->all();
        $locations = AssetLocation::pluck('id')->all();
        $brands = ['Dell', 'Lenovo', 'HP', 'Apple', 'ASUS', 'Cisco', 'Samsung', 'Epson'];

        $status = fake()->randomElement(['available', 'assigned', 'maintenance', 'retired']);

        return [
            'asset_code' => 'IT-XXX-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'asset_name' => fake()->randomElement([
                'Laptop Latitude 5420', 'Desktop OptiPlex 7070', 'MacBook Pro 14"',
                'UltraSharp 27 Monitor', 'ProLiant Server DL380', 'Cisco Catalyst 9200',
                'LaserJet Pro Printer', 'Galaxy Tab S9', 'iPhone 13 Pro', 'ThinkPad X1 Carbon',
            ]),
            'asset_category_id' => $categories ? fake()->randomElement($categories) : AssetCategory::factory(),
            'asset_location_id' => $locations ? fake()->randomElement($locations) : AssetLocation::factory(),
            'brand' => fake()->randomElement($brands),
            'model' => strtoupper(fake()->bothify('??####-##')),
            'serial_number' => strtoupper(fake()->bothify('????-####-??##')),
            'purchase_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'purchase_price' => fake()->randomFloat(2, 100, 5000),
            'status' => $status,
            'warranty_expires_at' => fake()->dateTimeBetween('-1 year', '+3 years')->format('Y-m-d'),
            'current_holder_id' => $status === 'assigned' ? \App\Models\User::factory() : null,
            'qr_path' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
