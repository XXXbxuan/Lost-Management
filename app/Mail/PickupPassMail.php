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

    public $match;
    public $qrRaw;
    protected $qrCid = 'pickup-pass-qr@airport.system';

    /**
     * @param MatchRecord $match 🌟 內部已統一使用 lostItem 關聯
     * @param string $qrRaw 
     */
    public function __construct(MatchRecord $match, $qrRaw)
    {
        $this->match = $match;
        $this->qrRaw = $qrRaw;
    }

    public function build()
    {
        return $this->subject('🎫 Your Official Pickup Pass - Airport Lost & Found')
            ->view('emails.pickup_pass') // 🌟 建議 View 也同步改名
            ->with([
                'match' => $this->match,
                'qrCid' => $this->qrCid,
            ])
            ->withSymfonyMessage(function (Email $email) {
                // 🌟 透過 CID 內嵌圖片，支援離線查看
                $part = new DataPart($this->qrRaw, 'qrcode.png', 'image/png');
                $part->asInline();
                $part->setContentId($this->qrCid);
                $email->addPart($part);
            });
    }
}