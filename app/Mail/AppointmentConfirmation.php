<?php

namespace App\Mail;

use App\Models\MatchRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public MatchRecord $match;
    public string $confirmLink;

    /** Raw PNG bytes */
    public string $qrRaw;

    /**
     * Content-ID MUST contain "@"
     * We'll use a valid CID format.
     */
    public string $qrCid = 'pickup-qrcode@lost-management.local';

    public function __construct(MatchRecord $match, string $confirmLink, string $qrRaw)
    {
        $this->match = $match;
        $this->confirmLink = $confirmLink;
        $this->qrRaw = $qrRaw;
    }

    public function build()
    {
        return $this->subject('Action Required: Confirm Your Lost Item Pickup')
            ->view('emails.appointment')
            ->with([
                'match' => $this->match,
                'confirmLink' => $this->confirmLink,
                'qrCid' => $this->qrCid,
            ])
            ->withSymfonyMessage(function (Email $email) {
                $part = new DataPart($this->qrRaw, 'qrcode.png', 'image/png');
                $part->asInline();

                // ✅ Must include "@"
                $part->setContentId($this->qrCid);

                $email->addPart($part);
            });
    }
}