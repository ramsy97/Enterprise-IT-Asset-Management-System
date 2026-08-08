<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Models\User;
use App\Notifications\WarrantyExpiringNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWarrantyReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Asset $asset, public int $daysLeft) {}

    public function handle(): void
    {
        $recipients = User::role(['ADMIN', 'IT STAFF'])
            ->where('is_active', true)
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new WarrantyExpiringNotification($this->asset, $this->daysLeft));
        }
    }
}
