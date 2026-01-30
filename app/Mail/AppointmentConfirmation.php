<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MatchRecord;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $match;
    public $confirmLink;

    // 接收数据
    public function __construct(MatchRecord $match, $confirmLink)
    {
        $this->match = $match;
        $this->confirmLink = $confirmLink;
    }

    // 构建邮件
    public function build()
    {
        return $this->subject('Action Required: Confirm Your Lost Item Pickup')
                    ->view('emails.appointment');
    }
}