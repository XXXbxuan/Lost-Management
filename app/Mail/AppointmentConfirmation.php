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
    public $qrCode; // 🆕 新增：用来装二维码图片的变量

    // 🆕 接收数据时，多接收一个 $qrCode
    public function __construct(MatchRecord $match, $confirmLink, $qrCode)
    {
        $this->match = $match;
        $this->confirmLink = $confirmLink;
        $this->qrCode = $qrCode; // 🆕 把它存起来
    }

    // 构建邮件 (这里不用改)
    public function build()
    {
        // 🌟 這是監控點：如果你在 storage/logs/laravel.log 看到 NO，表示資料根本沒傳進來
        \Illuminate\Support\Facades\Log::info('--- Email Debug Start ---');
        \Illuminate\Support\Facades\Log::info('QR Code data exists: ' . (empty($this->qrCode) ? 'NO' : 'YES'));
        \Illuminate\Support\Facades\Log::info('Confirm Link: ' . $this->confirmLink);
        \Illuminate\Support\Facades\Log::info('--- Email Debug End ---');

        return $this->subject('Action Required: Confirm Your Lost Item Pickup')
                    ->view('emails.appointment');
    }
}