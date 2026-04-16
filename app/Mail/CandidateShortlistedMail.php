<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateShortlistedMail extends Mailable
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
            subject: 'Congratulations! You have been shortlisted',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.candidate_shortlisted',
        );
    }

    public function attachments()
    {
        return [];
    }
}
