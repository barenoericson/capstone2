<?php

namespace App\Mail;

use App\Models\Viewing;
use App\Models\ViewingNegotiation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ViewingNegotiationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Viewing $viewing,
        public ViewingNegotiation $negotiation,
        public string $recipientRole
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Schedule Change Proposed - ' . $this->viewing->property->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.viewing-negotiation',
        );
    }
}
