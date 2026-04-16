<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateSelectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $candidate;

    public function __construct($candidate)
    {
        $this->candidate = $candidate;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Congratulations! You have been selected',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.candidate_selected',
        );
    }

    public function attachments()
    {
        return [];
    }
}
