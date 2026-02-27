<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchRecord;

class PickupController extends Controller
{
    /**
     * 1. 顯示確認頁面 (旅客點擊 Email 連結後抵達的地方)
     */
    public function showConfirmationPage($token)
    {
        // 查找 Token，包含關聯的失物與拾獲物資訊
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['foundItem', 'lostReport'])
                    ->firstOrFail();

        // 🌟 邏輯優化：如果已經確認過了，直接跳轉到 QR Code 顯示頁面，不需要再按一次確認
        if ($match->is_confirmed) {
            return redirect()->route('pickup.verify', ['token' => $token]);
        }

        // 導向剛才建立的確認頁面 (含有綠色大按鈕)
        return view('passenger.confirm_pickup', compact('match'));
    }

    /**
     * 2. 處理確認動作 (旅客點擊網頁上的 "YES, I CONFIRM" 綠色按鈕)
     */
    public function processConfirmation($token)
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        // 🛡️ 安全檢查：確保物品狀態還是 Verified (預防被撤回或已領取)
        if ($match->status !== 'Verified') {
            return back()->with('error', 'Item status has changed. Please contact the Lost & Found office.');
        }

        // 更新資料庫
        $match->update([
            'is_confirmed' => true,
            'updated_at'   => now(),
        ]);

        // 🚀 關鍵跳轉：跳到智能驗證路徑，此時旅客會看到 QR Code
        return redirect()->route('pickup.verify', ['token' => $token])
                         ->with('success', 'Your pickup has been confirmed! Please show the QR code below to the staff.');
    }
}