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

    /**
     * 建立新的郵件實例
     * 這裡我們只接收 2 個參數，完美對應 Controller 的傳參
     */
    public function __construct(MatchRecord $match, string $confirmLink)
    {
        $this->match = $match;
        $this->confirmLink = $confirmLink;
    }

    /**
     * 構建郵件內容
     */
    public function build()
    {
        return $this->subject('Action Required: Confirm Your Lost Item Pickup')
            ->view('emails.appointment')
            ->with([
                'match' => $this->match,
                'confirmLink' => $this->confirmLink,
            ]);
            // 🌟 這裡刪除了 SymfonyMessage 的 CID 內嵌圖片邏輯
    }
}