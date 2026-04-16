<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateNextRoundMail extends Mailable
{
    use Queueable, SerializesModels;

    public $candidate;
    public $nextRound;

    public function __construct($candidate, $nextRound)
    {
        $this->candidate = $candidate;
        $this->nextRound = $nextRound;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Congratulations! You have advanced to the next round',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.candidate_next_round',
            with: [
                'nextRound' => $this->nextRound,
            ],
        );
    }
}