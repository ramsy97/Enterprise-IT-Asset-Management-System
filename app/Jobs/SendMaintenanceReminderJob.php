<?php

namespace App\Jobs;

use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Notifications\MaintenanceReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMaintenanceReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public MaintenanceRecord $record) {}

    public function handle(): void
    {
        $recipients = User::role(['ADMIN', 'IT STAFF'])
            ->where('is_active', true)
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new MaintenanceReminderNotification($this->record));
        }
    }
}
