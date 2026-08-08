<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRecordFactory extends Factory
{
    protected $model = MaintenanceRecord::class;

    public function definition(): array
    {
        $completed = fake()->boolean(60);

        return [
            'asset_id' => Asset::factory(),
            'technician_id' => User::factory(),
            'type' => fake()->randomElement(['preventive', 'repair', 'replacement']),
            'scheduled_date' => fake()->dateTimeBetween('-4 months', '+2 months')->format('Y-m-d'),
            'completed_date' => $completed ? fake()->date() : null,
            'status' => $completed ? 'completed' : fake()->randomElement(['scheduled', 'in_progress']),
            'cost' => fake()->randomFloat(2, 0, 1500),
            'description' => fake()->sentence(),
            'result' => $completed ? fake()->sentence() : null,
        ];
    }
}
