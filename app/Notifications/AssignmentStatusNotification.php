<?php

namespace App\Notifications;

use App\Models\AssetAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AssetAssignment $assignment, public string $action) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $actionLabel = ucfirst($this->action);

        return (new MailMessage)
            ->subject("[ITAMS] Asset Assignment {$actionLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your asset assignment request for **{$this->assignment->asset?->asset_name}** ({$this->assignment->asset?->asset_code}) has been **{$actionLabel}**.")
            ->when($this->assignment->notes, fn ($m) => $m->line("Note: {$this->assignment->notes}"))
            ->action('View Assignment', route('assignments.index'))
            ->line('Thank you for using ITAMS Enterprise.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment',
            'assignment_id' => $this->assignment->id,
            'asset_code' => $this->assignment->asset?->asset_code,
            'asset_name' => $this->assignment->asset?->asset_name,
            'action' => $this->action,
            'message' => "Your assignment request for {$this->assignment->asset?->asset_code} was {$this->action}.",
        ];
    }
}
