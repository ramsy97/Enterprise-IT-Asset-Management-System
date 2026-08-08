<?php

namespace Database\Factories;

use App\Models\SoftwareLicense;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoftwareLicenseFactory extends Factory
{
    protected $model = SoftwareLicense::class;

    public function definition(): array
    {
        $total = fake()->numberBetween(5, 500);
        $used = fake()->numberBetween(0, $total);

        return [
            'software_name' => fake()->unique()->randomElement([
                'Microsoft 365 E3', 'Microsoft Windows 11 Pro', 'Adobe Creative Cloud',
                'JetBrains All Products', 'VMware vSphere', 'Salesforce Enterprise',
                'Slack Business+', 'GitHub Enterprise', 'Zoom Business', 'Atlassian Cloud',
            ]),
            'vendor' => fake()->randomElement(['Microsoft', 'Adobe', 'JetBrains', 'VMware', 'Salesforce', 'Slack', 'GitHub', 'Zoom', 'Atlassian']),
            'license_key' => \Illuminate\Support\Facades\Crypt::encryptString(fake()->bothify('####-####-####-####')),
            'total_licenses' => $total,
            'used_licenses' => $used,
            'purchase_date' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'purchase_cost' => fake()->randomFloat(2, 500, 50000),
            'expires_at' => fake()->dateTimeBetween('-1 year', '+2 years')->format('Y-m-d'),
        ];
    }
}
