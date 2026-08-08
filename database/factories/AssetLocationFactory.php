<?php

namespace Database\Factories;

use App\Models\AssetLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetLocationFactory extends Factory
{
    protected $model = AssetLocation::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city().' Office',
            'building' => fake()->randomElement(['Headquarters', 'Tower A', 'Tower B', 'Data Center']),
            'floor' => (string) fake()->numberBetween(1, 20),
            'room' => fake()->bothify('Room ??-##'),
            'city' => fake()->city(),
        ];
    }
}
