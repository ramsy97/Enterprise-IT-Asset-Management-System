<?php

namespace App\Mail;

use App\Models\MaintenanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public MaintenanceRecord $record) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[ITAMS] Maintenance Reminder — {$this->record->asset?->asset_code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.maintenance-reminder',
            with: ['record' => $this->record],
        );
    }
}
