<?php

namespace Database\Seeders;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AuditRecord;
use App\Models\MaintenanceRecord;
use App\Models\SoftwareLicense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::updateOrCreate(
            ['email' => 'staff@itams.local'],
            ['name' => 'Staff IT', 'department' => 'IT Department', 'position' => 'IT Support', 'password' => Hash::make('password'), 'is_active' => true]
        );
        $staff->assignRole('IT STAFF');

        $manager = User::updateOrCreate(
            ['email' => 'manager@itams.local'],
            ['name' => 'Manager IT', 'department' => 'IT Management', 'position' => 'IT Manager', 'password' => Hash::make('password'), 'is_active' => true]
        );
        $manager->assignRole('MANAGER');

        $employees = collect();
        $employeeData = [
            ['name' => 'Jane Doe', 'department' => 'Finance', 'position' => 'Accountant'],
            ['name' => 'Alex Smith', 'department' => 'Operations', 'position' => 'Ops Analyst'],
            ['name' => 'Maria Klein', 'department' => 'Marketing', 'position' => 'Brand Manager'],
            ['name' => 'John Carter', 'department' => 'Sales', 'position' => 'Account Executive'],
            ['name' => 'Rina Wijaya', 'department' => 'IT Department', 'position' => 'Network Engineer'],
            ['name' => 'Dewi Lestari', 'department' => 'Finance', 'position' => 'Finance Manager'],
            ['name' => 'Budi Santoso', 'department' => 'Operations', 'position' => 'Warehouse Lead'],
        ];

        foreach ($employeeData as $i => $data) {
            $employee = User::updateOrCreate(
                ['email' => "employee{$i}@itams.local"],
                [
                    'name' => $data['name'],
                    'department' => $data['department'],
                    'position' => $data['position'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $employee->assignRole('IT STAFF');
            $employees->push($employee);
        }

        $technicians = $employees->slice(2, 3)->values();

        AuditRecord::query()->delete();
        MaintenanceRecord::query()->delete();
        AssetAssignment::query()->delete();
        Asset::query()->delete();
        SoftwareLicense::query()->delete();

        $laptopNames = [
            'Dell Latitude 5420', 'Lenovo ThinkPad X1 Carbon', 'MacBook Pro 14"',
            'HP EliteBook 840', 'ASUS ZenBook 14',
        ];
        $desktopNames = ['Dell OptiPlex 7070', 'HP ProDesk 400', 'Lenovo ThinkCentre M720'];
        $serverNames = ['Dell PowerEdge R740', 'HPE ProLiant DL380', 'Dell PowerEdge R650'];
        $monitorNames = ['Dell UltraSharp 27', 'LG UltraWide 29'];
        $printerNames = ['HP LaserJet Pro M404', 'Epson EcoTank L3210'];
        $networkNames = ['Cisco Catalyst 9200', 'Ubiquiti UniFi AP AC Pro'];

        $pool = [];
        $i = 1;

        $make = function (string $name, string $category, string $location, string $status, ?User $holder, array $extra = []) use (&$pool, &$i) {
            $categoryId = \App\Models\AssetCategory::where('code_prefix', $category)->value('id');
            $locationId = \App\Models\AssetLocation::where('name', 'like', "%{$location}%")->value('id');

            $asset = Asset::create(array_merge([
                'asset_code' => 'IT-'.$category.'-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'asset_name' => $name,
                'asset_category_id' => $categoryId,
                'asset_location_id' => $locationId,
                'brand' => explode(' ', $name)[0],
                'model' => explode(' ', $name, 2)[1] ?? $name,
                'serial_number' => strtoupper(\Illuminate\Support\Str::random(8)),
                'purchase_date' => now()->subMonths(rand(6, 40))->subDays(rand(1, 28))->format('Y-m-d'),
                'purchase_price' => rand(300, 8000),
                'status' => $status,
                'warranty_expires_at' => now()->addDays(rand(-40, 500))->format('Y-m-d'),
                'current_holder_id' => $holder?->id,
            ], $extra));
            $pool[] = $asset;
            $i++;

            return $asset;
        };

        $locations = ['Main Office', 'Finance', 'IT Server Room', 'Operations', 'Marketing', 'Storage Room', 'IT Helpdesk', 'Sales'];

        $hold = $employees->get(0);
        $make('Dell Latitude 5420', 'LAP', 'Finance', AssetStatus::Assigned->value, $hold);
        $make('Lenovo ThinkPad X1 Carbon', 'LAP', 'IT Helpdesk', AssetStatus::Assigned->value, $employees->get(2));
        $make('MacBook Pro 14"', 'LAP', 'Marketing', AssetStatus::Maintenance->value, $employees->get(2));
        $make('HP EliteBook 840', 'LAP', 'Sales', AssetStatus::Assigned->value, $employees->get(3));
        $make('ASUS ZenBook 14', 'LAP', 'Main Office', AssetStatus::Available->value, null);
        $make('Dell OptiPlex 7070', 'DESK', 'Main Office', AssetStatus::Assigned->value, $employees->get(1));
        $make('HP ProDesk 400', 'DESK', 'Operations', AssetStatus::Available->value, null);
        $make('Lenovo ThinkCentre M720', 'DESK', 'Storage Room', AssetStatus::Retired->value, null);
        $make('Dell PowerEdge R740', 'SRV', 'IT Server Room', AssetStatus::Assigned->value, $employees->get(4));
        $make('HPE ProLiant DL380', 'SRV', 'IT Server Room', AssetStatus::Maintenance->value, $employees->get(4));
        $make('Dell PowerEdge R650', 'SRV', 'IT Server Room', AssetStatus::Available->value, null);
        $make('Dell UltraSharp 27', 'MON', 'Main Office', AssetStatus::Available->value, null);
        $make('Dell UltraSharp 27', 'MON', 'Finance', AssetStatus::Assigned->value, $hold);
        $make('LG UltraWide 29', 'MON', 'Storage Room', AssetStatus::Available->value, null);
        $make('HP LaserJet Pro M404', 'PRT', 'Main Office', AssetStatus::Assigned->value, $employees->get(1));
        $make('Epson EcoTank L3210', 'PRT', 'Operations', AssetStatus::Available->value, null);
        $make('Cisco Catalyst 9200', 'NET', 'IT Server Room', AssetStatus::Assigned->value, $employees->get(4));
        $make('Ubiquiti UniFi AP AC Pro', 'NET', 'Main Office', AssetStatus::Available->value, null);

        foreach (range(1, 35) as $_) {
            $status = fake()->randomElement(['available', 'assigned', 'maintenance']);
            $name = fake()->randomElement([...$laptopNames, ...$desktopNames, ...$monitorNames, ...$networkNames]);
            $catCode = match (true) {
                str_contains($name, 'Latitude') || str_contains($name, 'ThinkPad') || str_contains($name, 'MacBook') || str_contains($name, 'EliteBook') || str_contains($name, 'ZenBook') => 'LAP',
                str_contains($name, 'OptiPlex') || str_contains($name, 'ProDesk') || str_contains($name, 'ThinkCentre') => 'DESK',
                str_contains($name, 'UltraSharp') || str_contains($name, 'UltraWide') => 'MON',
                default => 'NET',
            };
            $loc = $locations[array_rand($locations)];
            $make($name, $catCode, $loc, $status, $status === 'assigned' ? $employees->random() : null);
        }

        $assignments = [
            ['asset_code' => 'IT-LAP-0001', 'employee' => $hold, 'status' => 'approved'],
            ['asset_code' => 'IT-DESK-0006', 'employee' => $employees->get(1), 'status' => 'approved'],
            ['asset_code' => 'IT-NET-0017', 'employee' => $employees->get(4), 'status' => 'approved'],
            ['asset_code' => 'IT-SRV-0009', 'employee' => $employees->get(4), 'status' => 'approved'],
        ];

        foreach ($assignments as $a) {
            $asset = Asset::where('asset_code', $a['asset_code'])->first();
            if (! $asset) {
                continue;
            }
            AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $a['employee']->id,
                'assigned_by' => $staff->id,
                'approved_by' => $manager->id,
                'request_date' => now()->subMonths(3)->format('Y-m-d'),
                'approved_at' => now()->subMonths(3)->addDays(1)->format('Y-m-d'),
                'assigned_date' => now()->subMonths(3)->addDays(2)->format('Y-m-d'),
                'status' => $a['status'],
            ]);
        }

        AssetAssignment::create([
            'asset_id' => Asset::where('asset_code', 'IT-LAP-0005')->value('id'),
            'employee_id' => $employees->get(5)->id,
            'assigned_by' => $staff->id,
            'request_date' => now()->subDays(2)->format('Y-m-d'),
            'status' => 'pending',
        ]);

        $maintenanceAssets = Asset::whereIn('asset_code', ['IT-LAP-0003', 'IT-SRV-0010', 'IT-MON-0014'])->get();
        foreach ($maintenanceAssets as $mAsset) {
            MaintenanceRecord::create([
                'asset_id' => $mAsset->id,
                'technician_id' => $technicians->random()?->id ?? $staff->id,
                'type' => 'repair',
                'scheduled_date' => now()->addDays(rand(1, 14))->format('Y-m-d'),
                'status' => 'scheduled',
                'cost' => rand(50, 800),
                'description' => 'Scheduled repair / preventive maintenance.',
            ]);
        }

        $prevAssets = Asset::where('status', 'assigned')->take(6)->get();
        foreach ($prevAssets as $pa) {
            if (rand(0, 1)) {
                continue;
            }
            MaintenanceRecord::create([
                'asset_id' => $pa->id,
                'technician_id' => $technicians->random()?->id ?? $staff->id,
                'type' => 'preventive',
                'scheduled_date' => now()->subMonths(rand(1, 6))->format('Y-m-d'),
                'completed_date' => now()->subMonths(rand(0, 5))->format('Y-m-d'),
                'status' => 'completed',
                'cost' => rand(0, 200),
                'description' => 'Preventive maintenance.',
                'result' => 'Asset inspected and functioning normally.',
            ]);
        }

        foreach (array_slice($pool, 0, 20) as $auditAsset) {
            if (rand(0, 1)) {
                continue;
            }
            AuditRecord::create([
                'asset_id' => $auditAsset->id,
                'audited_by' => $staff->id,
                'audit_batch_id' => 'AUD-'.now()->format('Ymd'),
                'audit_date' => now()->subDays(rand(0, 45))->format('Y-m-d'),
                'status' => fake()->randomElement(['verified', 'verified', 'verified', 'need_repair']),
                'condition' => fake()->randomElement(['Good', 'Good', 'Good', 'Fair']),
                'location_match' => true,
                'findings' => fake()->optional()->sentence(),
            ]);
        }

        $licenses = [
            ['Microsoft 365 E3', 'Microsoft', 150, 118],
            ['Microsoft Windows 11 Pro', 'Microsoft', 300, 240],
            ['Adobe Creative Cloud', 'Adobe', 40, 38],
            ['JetBrains All Products', 'JetBrains', 25, 12],
            ['VMware vSphere', 'VMware', 10, 8],
            ['GitHub Enterprise', 'GitHub', 200, 167],
            ['Slack Business+', 'Slack', 500, 412],
            ['Zoom Business', 'Zoom', 250, 231],
            ['Atlassian Cloud', 'Atlassian', 100, 88],
            ['Salesforce Enterprise', 'Salesforce', 60, 44],
        ];

        foreach ($licenses as [$name, $vendor, $total, $used]) {
            SoftwareLicense::create([
                'software_name' => $name,
                'vendor' => $vendor,
                'license_key' => Crypt::encryptString(strtoupper(fake()->bothify('####-####-####-####'))),
                'total_licenses' => $total,
                'used_licenses' => $used,
                'purchase_date' => now()->subMonths(rand(2, 24))->format('Y-m-d'),
                'purchase_cost' => rand(1000, 50000),
                'expires_at' => now()->addDays(rand(-30, 365))->format('Y-m-d'),
            ]);
        }
    }
}
