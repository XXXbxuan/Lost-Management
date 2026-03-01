<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LostItemReport;
use App\Models\FoundItem;
use App\Models\MatchRecord;
use App\Models\Claim;
use App\Models\AdminActionLog;
use App\Mail\AppointmentConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClaimController extends Controller
{
    /**
     * 1. 準備領取流程頁面 (針對剛配對成功，尚未建立預約的狀態)
     */
    public function createClaim(Request $request)
    {
        $lostId = $request->query('lost_id');

        // 🌟 統一使用 lostItem
        $lostItem = LostItemReport::findOrFail($lostId);

        $match = MatchRecord::where('lostId', $lostItem->id)
            ->where('status', 'Verified')
            ->firstOrFail();

        $foundItem = FoundItem::findOrFail($match->foundId);

        return view('staff.claims.process', compact('lostItem', 'foundItem', 'match'));
    }

    /**
     * 🌟🌟🌟 新增：處理既有的配對紀錄 (包含 Reschedule 狀態) 🌟🌟🌟
     * 點擊 Manage Claim 時會進入這裡
     */
    public function process($id)
    {
        // 1. 直接透過 Match ID 找到這筆配對紀錄
        $match = MatchRecord::findOrFail($id);

        // 2. 順著紅線 (lostId 和 foundId) 找出對應的遺失物與拾獲物
        $lostItem = LostItemReport::findOrFail($match->lostId);
        $foundItem = FoundItem::findOrFail($match->foundId);

        // 3. 把資料打包，送到你貼好藍色提示框的 process 畫面
        return view('staff.claims.process', compact('match', 'lostItem', 'foundItem'));
    }
    
    /**
     * 2. 設定預約時間並發送第一封確認信 (Email #1)
     */
    public function schedule(Request $request)
    {
        // 1. 修正驗證規則：允許選擇「今天 (today)」，不要直接在這裡用 now 擋掉
        $request->validate([
            'match_id'         => 'required',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        // 2. 將日期和時間合體
        $fullDateTimeString = $request->appointment_date . ' ' . $request->appointment_time;
        
        // 3. 🌟 精準時間驗證：檢查「合體後的時間」是不是已經過去了
        $fullDateTime = \Carbon\Carbon::parse($fullDateTimeString);
        if ($fullDateTime->isPast()) {
            return back()->withErrors(['appointment_time' => 'Oops! The time has already passed. Please select a future time.']);
        }

        // --- 驗證通過，繼續原本的流程 ---
        $match = MatchRecord::findOrFail($request->match_id);
        $lostItem = LostItemReport::findOrFail($match->lostId);

        $token = strtoupper(Str::random(6));

        // 更新預約資訊
        $match->update([
            'appointment_at'     => $fullDateTimeString,
            'verification_token' => $token,
            'is_confirmed'       => false,
            // 如果原本是 Reschedule Requested，排好時間後我們把它改回 Verified 等待確認
            'status'             => 'Verified', 
        ]);

        // 自動生成驗證連結
        $confirmLink = route('pickup.confirm', ['token' => $token]);

        $passengerEmail = $lostItem->passenger_email ?? 'chiabx-wp22@student.tarc.edu.my';

        try {
            Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, $confirmLink));
            $message = 'Appointment scheduled! Verification link sent to ' . $passengerEmail;
        } catch (\Exception $e) {
            \Log::error('Email #1 Failed', [
                'to' => $passengerEmail,
                'error' => $e->getMessage(),
            ]);
            $message = 'Appointment set, but email failed: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    /**
     * 3. 智能驗證入口 (QR Code 指向此路由)
     */
    public function smartVerify($token)
    {
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['lostItem', 'foundItem'])
                    ->firstOrFail();

        // 🌟 優化後的判斷：
        // 1. 先確認是否登入
        // 2. 使用 strtolower 將角色轉為小寫，並同時支援 'admin' 和 'staff'
        if (auth()->check()) {
            $userRole = strtolower(auth()->user()->role); // 轉為小寫比較安全
            
            if ($userRole === 'admin' || $userRole === 'staff') {
                
                if ($match->status === 'Claimed') {
                    return view('staff.claims.qr_status_claimed', compact('match'));
                }

                return view('staff.claims.verify_action', compact('match'));
            }
        }

        // --- 否則一律顯示旅客畫面 ---
        if ($match->status === 'Claimed') {
            return view('passenger.claims.pickup_success_receipt', compact('match'));
        }

        return view('passenger.claims.qr_status', compact('match'));
    }
    /**
     * 4. 最終領取確認 (Handover)
     */
    public function completeHandover(Request $request, $id)
    {
        // 1. 預載關聯，避免 N+1 問題
        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($id);

        // 🌟 安全檢查：如果已經結案了，就不要再跑一次
        if ($match->status === 'Claimed') {
            return redirect()->route('staff.claims.index')
                            ->with('info', 'This case has already been closed.');
        }

        try {
            // 2. 使用資料庫事務，確保「要嘛全成功，要嘛全失敗」
            \DB::transaction(function () use ($match) {
                
                // A. 更新 Match 紀錄：標記狀態、記錄核准員工與時間
                $match->update([
                    'status'     => 'Claimed',
                    'verifiedBy' => auth()->id(), // 🌟 記錄是誰核對的 (Audit)
                    'verifiedAt' => now(),       // 🌟 記錄確切領取時間
                ]);

                // B. 同步更新相關物品狀態 (使用 Eloquent 關聯更新)
                $match->lostItem->update(['status' => 'Claimed']);
                $match->foundItem->update(['status' => 'Claimed']);

                // C. 寫入 Admin 操作日誌 (展現系統的嚴謹性)
                \App\Models\AdminActionLog::create([
                    'admin_name'  => auth()->user()->name,
                    'action_type' => 'ITEM_HANDOVER_SUCCESS',
                    'target_name' => "Passenger: " . ($match->lostItem->passenger_name ?? 'Unknown'),
                    'details'     => "Handed over Item: [{$match->foundItem->item_name}] via QR Code Security Verification."
                ]);
            });

            // 3. 成功後跳轉 (建議跳轉到 Claims List 或 Dashboard)
            return redirect()->route('staff.claims.index') 
                            ->with('success', '✅ Handover successful. The case is now permanently closed!');

        } catch (\Exception $e) {
            // 4. 錯誤處理與日誌記錄
            \Log::error("Critical Handover Error: " . $e->getMessage());
            return back()->with('error', 'Critical Error: Data update failed. Please check system logs.');
        }
    }

    /**
     * 5. 手動處理領取 (無 QR Code 情況下的手動輸入)
     */
    public function store(Request $request)
    {
        $request->validate([
            'lostId'            => 'required',
            'foundId'           => 'required',
            'claimerName'       => 'required',
            'claimerIcPassport' => 'required',
            'claimerPhone'      => 'required',
        ]);

        $match = MatchRecord::where('lostId', $request->lostId)
                    ->where('foundId', $request->foundId)
                    ->firstOrFail();

        if (!$match->is_confirmed) {
            return back()->withErrors([
                'claimerIcPassport' => 'The passenger has NOT confirmed the appointment via Email link yet.'
            ]);
        }

        DB::transaction(function () use ($request) {
            Claim::create([
                'lostId'            => $request->lostId,
                'foundId'           => $request->foundId,
                'claimerName'       => $request->claimerName,
                'claimerIcPassport' => $request->claimerIcPassport,
                'claimerPhone'      => $request->claimerPhone,
                'processedBy'       => auth()->id(),
                'claimedAt'         => now(),
            ]);

            LostItemReport::where('id', $request->lostId)->update(['status' => 'Claimed']);
            FoundItem::where('id', $request->foundId)->update(['status' => 'Claimed']);
        });

        return redirect()->route('staff.lost-items.index')
                         ->with('success', 'Manual handover completed!');
    }

    /**
     * 6. 領取歷史紀錄列表
     */
    public function index()
    {
        $claims = Claim::with(['foundItem', 'lostItem', 'handler']) 
            ->latest('claimedAt')
            ->paginate(10);

        return view('staff.claims.index', compact('claims'));
    }

    /**
     * 7. 彈出視窗使用的時間軸 HTML
     */
    public function getTimelineHtml($id)
    {
        $lostItem = LostItemReport::findOrFail($id);

        $match = MatchRecord::where('lostId', $lostItem->id)
            ->where('status', 'Verified')
            ->first();

        if (!$match || !$match->foundItem) {
            return '<div class="p-6 text-center text-gray-500">No verified timeline data.</div>';
        }

        $foundItem = $match->foundItem;

        return view('staff.claims.partials.timeline', compact('foundItem', 'match'))->render();
    }
}