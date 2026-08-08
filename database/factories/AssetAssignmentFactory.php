<?php

namespace Database\Factories;

use App\Enums\AssignmentStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetAssignmentFactory extends Factory
{
    protected $model = AssetAssignment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(AssignmentStatus::cases());

        return [
            'asset_id' => Asset::factory(),
            'employee_id' => User::factory(),
            'assigned_by' => User::factory(),
            'approved_by' => $status === AssignmentStatus::Pending ? null : User::factory(),
            'request_date' => fake()->dateTimeBetween('-6 months', '-1 month')->format('Y-m-d'),
            'approved_at' => $status === AssignmentStatus::Pending ? null : fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
            'rejected_at' => $status === AssignmentStatus::Rejected ? fake()->date() : null,
            'assigned_date' => $status === AssignmentStatus::Approved ? fake()->date() : null,
            'return_date' => $status === AssignmentStatus::Returned ? fake()->date() : null,
            'status' => $status,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
