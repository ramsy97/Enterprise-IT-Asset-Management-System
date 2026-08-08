<?php

namespace App\Console\Commands;

use App\Jobs\SendMaintenanceReminderJob;
use App\Jobs\SendWarrantyReminderJob;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReminderNotifications extends Command
{
    protected $signature = 'itams:send-reminders {--warranty-days=30 : days threshold for warranty reminders}';

    protected $description = 'Queue warranty expiration and maintenance reminders for IT staff';

    public function handle(): int
    {
        $threshold = (int) $this->option('warranty-days');

        $assets = Asset::whereNotNull('warranty_expires_at')
            ->where('status', '!=', 'retired')
            ->whereBetween('warranty_expires_at', [now()->toDateString(), now()->addDays($threshold)->toDateString()])
            ->get();

        $sentWarranty = 0;
        foreach ($assets as $asset) {
            $days = (int) now()->startOfDay()->diffInDays($asset->warranty_expires_at->startOfDay(), false);
            SendWarrantyReminderJob::dispatch($asset, $days);
            $sentWarranty++;
        }

        $records = MaintenanceRecord::where('status', 'scheduled')
            ->whereBetween('scheduled_date', [now()->toDateString(), now()->addDays(2)->toDateString()])
            ->get();

        $sentMaintenance = 0;
        foreach ($records as $record) {
            SendMaintenanceReminderJob::dispatch($record);
            $sentMaintenance++;
        }

        Log::channel('stack')->info('[ITAMS] Reminders queued', [
            'warranty' => $sentWarranty,
            'maintenance' => $sentMaintenance,
            'admin_email' => User::role('ADMIN')->value('email'),
        ]);

        $this->info("Queued {$sentWarranty} warranty reminders and {$sentMaintenance} maintenance reminders.");

        return self::SUCCESS;
    }
}
