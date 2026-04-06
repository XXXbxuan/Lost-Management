<?php

namespace App\Mail;

use App\Models\MatchRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public MatchRecord $match;
    public string $confirmLink;

    public function __construct(MatchRecord $match, string $confirmLink)
    {
        $this->match = $match;
        $this->confirmLink = $confirmLink;
    }

    public function build(): self
    {
        return $this->subject('Action Required: Confirm Your Lost Item Pickup')
            ->view('emails.appointment')
            ->with([
                'match' => $this->match,
                'confirmLink' => $this->confirmLink,
            ]);
    }
}