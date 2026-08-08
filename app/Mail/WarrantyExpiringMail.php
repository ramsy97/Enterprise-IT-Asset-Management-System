<?php

namespace App\Mail;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarrantyExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Asset $asset, public int $daysLeft) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[ITAMS] Warranty Expiring Soon — {$this->asset->asset_code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.warranty-expiring',
            with: [
                'asset' => $this->asset,
                'daysLeft' => $this->daysLeft,
            ],
        );
    }
}
