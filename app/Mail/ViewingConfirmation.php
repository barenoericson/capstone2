<?php

namespace App\Mail;

use App\Models\Viewing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ViewingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Viewing $viewing,
        public string $recipientRole
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Viewing Confirmed - ' . $this->viewing->property->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.viewing-confirmation',
        );
    }
}
