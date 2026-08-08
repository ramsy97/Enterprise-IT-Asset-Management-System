<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Laptop', 'Desktop', 'Monitor', 'Printer', 'Server',
                'Network Device', 'Smartphone', 'Peripheral', 'Tablet',
            ]),
            'code_prefix' => fake()->unique()->lexify('???'),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
