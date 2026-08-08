<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AuditRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditRecordFactory extends Factory
{
    protected $model = AuditRecord::class;

    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'audited_by' => User::factory(),
            'audit_batch_id' => 'AUD-'.fake()->numberBetween(1000, 9999),
            'audit_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['verified', 'need_repair', 'missing']),
            'condition' => fake()->randomElement(['Good', 'Fair', 'Poor']),
            'location_match' => fake()->boolean(90),
            'findings' => fake()->optional()->sentence(),
            'evidence_path' => null,
        ];
    }
}
