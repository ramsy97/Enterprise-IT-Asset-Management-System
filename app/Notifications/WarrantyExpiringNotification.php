<?php

namespace App\Notifications;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WarrantyExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Asset $asset, public int $daysLeft) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[ITAMS] Warranty Expiring Soon — {$this->asset->asset_code}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The warranty for asset **{$this->asset->asset_name}** ({$this->asset->asset_code}) will expire in **{$this->daysLeft} days**.")
            ->line("Expiration date: {$this->asset->warranty_expires_at?->format('d M Y')}")
            ->action('View Asset', route('assets.show', $this->asset))
            ->line('Please take the necessary action before the warranty period ends.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'warranty',
            'asset_id' => $this->asset->id,
            'asset_code' => $this->asset->asset_code,
            'asset_name' => $this->asset->asset_name,
            'days_left' => $this->daysLeft,
            'message' => "Warranty for {$this->asset->asset_name} ({$this->asset->asset_code}) expires in {$this->daysLeft} days.",
        ];
    }
}
