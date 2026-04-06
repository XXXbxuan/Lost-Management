<?php

namespace App\Mail;

use App\Models\MatchRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class PickupPassMail extends Mailable
{
    use Queueable, SerializesModels;

    private const QR_CONTENT_ID = 'pickup-pass-qr@airport.system';

    public MatchRecord $match;
    public string $qrRaw;

    public function __construct(MatchRecord $match, string $qrRaw)
    {
        $this->match = $match;
        $this->qrRaw = $qrRaw;
    }

    public function build(): self
    {
        return $this->subject('Your Official Pickup Pass - Airport Lost & Found')
            ->view('emails.pickup_pass')
            ->with([
                'match' => $this->match,
                'qrCid' => self::QR_CONTENT_ID,
            ])
            ->withSymfonyMessage(function (Email $email) {
                $part = new DataPart($this->qrRaw, 'qrcode.png', 'image/png');
                $part->asInline();
                $part->setContentId(self::QR_CONTENT_ID);

                $email->addPart($part);
            });
    }
}