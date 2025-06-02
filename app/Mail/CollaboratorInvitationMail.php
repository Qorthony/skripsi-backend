<?php

namespace App\Mail;

use App\Models\Collaborator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollaboratorInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collaborator $collaborator
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Kolaborator Event - ' . $this->collaborator->event->nama,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.collaborator-invitation',
            with: [
                'collaborator' => $this->collaborator,
                'event' => $this->collaborator->event,
                'accessLink' => $this->collaborator->access_link,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
