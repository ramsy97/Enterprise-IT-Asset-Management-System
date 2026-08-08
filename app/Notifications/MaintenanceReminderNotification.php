<?php

namespace App\Notifications;

use App\Models\MaintenanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MaintenanceRecord $record) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[ITAMS] Maintenance Reminder — {$this->record->asset?->asset_code}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A **{$this->record->type->label()}** is scheduled for **{$this->record->asset?->asset_name}** ({$this->record->asset?->asset_code}) on **{$this->record->scheduled_date?->format('d M Y')}**.")
            ->action('View Maintenance', route('maintenance.show', $this->record))
            ->line('Make sure the asset is ready and the technician is informed.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'maintenance',
            'maintenance_id' => $this->record->id,
            'asset_code' => $this->record->asset?->asset_code,
            'asset_name' => $this->record->asset?->asset_name,
            'scheduled_date' => $this->record->scheduled_date?->format('Y-m-d'),
            'message' => "{$this->record->type->label()} scheduled for {$this->record->asset?->asset_name} on {$this->record->scheduled_date?->format('d M Y')}.",
        ];
    }
}
