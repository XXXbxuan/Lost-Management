<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchRecord;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PickupPassMail;
use Carbon\Carbon;

class PickupController extends Controller
{
    /**
     * 1. 顯示確認頁面
     * 旅客點擊 Email #1 的連結後到達此處
     */
    public function showConfirmationPage($token)
    {
        // 🌟 預先加載關聯數據，確保頁面資訊完整且變數名統一為 lostItem
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['foundItem', 'lostItem']) 
                    ->firstOrFail();

        // 如果已經確認過，直接送他去 QR Code 狀態頁面
        if ($match->is_confirmed) {
            return redirect()->route('pickup.verify', ['token' => $token]);
        }

        // 💡 返回獨立 HTML 視圖，徹底避開後台 Layout 的 $slot 報錯
        return view('passenger.confirm_pickup', compact('match'));
    }

    /**
     * 2. 處理確認動作
     * 旅客在網頁按下 "CONFIRM" 按鈕後觸發
     */
    public function processConfirmation($token)
    {
        // 🌟 預先加載關聯，因為寄信時會用到 $match->lostItem->passenger_email
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['foundItem', 'lostItem'])
                    ->firstOrFail();

        // 安全檢查：只有經過驗證或匹配的紀錄才能確認
        if ($match->status !== 'Verified' && $match->status !== 'Matched') {
            return back()->with('error', 'This record is not ready for confirmation. Please contact airport staff.');
        }

        try {
            // 1. 更新資料庫狀態
            $match->update([
                'is_confirmed' => true,
                'confirmed_at' => now(),
            ]);

            // 2. 生成 QR Code 圖片資料
            // 此 QR 指向智能分流路由 smartVerify
            $verifyLink = route('pickup.verify', ['token' => $token]);
            
            /**
             * 🌟 關鍵修正：加上 (string) 強制轉型
             * 這是為了將 QrCode 物件轉為純字串數據，避免 Symfony Mailer 報出 
             * "must be a string, a resource... (got HtmlString)" 的錯誤
             */
            $qrRaw = (string) QrCode::format('png')
                        ->size(400)
                        ->margin(1)
                        ->color(15, 23, 42)
                        ->generate($verifyLink);

            // 3. 寄出正式的領取憑證信 (Email #2)
            // 使用內聯圖片技術 (CID)，支援旅客離線查看憑證
            Mail::to($match->lostItem->passenger_email)->send(
                new PickupPassMail($match, $qrRaw)
            );

            // 4. 成功後導向智能驗證路由 (網頁顯示 QR Code)
            return redirect()->route('pickup.verify', ['token' => $token])
                             ->with('success', 'Thank you! Your pickup has been confirmed and a digital pass was sent to your email.');

        } catch (\Exception $e) {
            // 記錄錯誤日誌，以便開發者調試
            Log::error("Pickup Confirmation Error: " . $e->getMessage());
            
            return back()->with('error', 'An error occurred while sending your pass. Please try again or check your history.');
        }
    }

    /**
     * 旅客點擊 Reject，顯示「建議新時間」的表單
     */
    /**
     * 1. 旅客點擊 Email 的 Reject，來到「拒絕與重新提議」的合併頁面
     */
    public function rejectAppointment($token)
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();
        return view('passenger.appointment_rejected', compact('match'));
    }

    /**
     * 2. 接收旅客送出的表單
     */
    public function submitProposal(Request $request, $token)
    {
        $request->validate([
            'suggested_time_1' => 'required|date',
            'suggested_time_2' => 'nullable|date',
            'suggested_remarks' => 'nullable|string|max:500',
        ]);

        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        // 更新資料庫
        $match->update([
            'appointment_at' => null,     
            'appointment_venue' => null,  
            'status' => 'Reschedule Requested',
            'is_confirmed' => false,
            'suggested_time_1' => $request->suggested_time_1,
            'suggested_time_2' => $request->suggested_time_2,
            'suggested_remarks' => $request->suggested_remarks,
            'rejected_at' => now(),       
            'verification_token' => \Illuminate\Support\Str::random(40), // 換掉 Token 確保安全
        ]);

        // 直接返回同一個視圖，但帶上 success 變數，畫面就會變成「綠色勾勾」
        return view('passenger.appointment_rejected', [
            'match' => $match,
            'success' => true
        ]);
    }
}