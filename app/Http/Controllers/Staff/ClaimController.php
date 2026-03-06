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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ClaimController extends Controller
{
    /**
     * 🌟 私有方法：全案數據統一提取器
     * 為 Timeline 和 Receipt 提供完整的 5 階段數據與日誌證明
     */
    /**
 * 🌟 全案數據大一統提取器 (The Single Source of Truth)
 * 作用：將 Match 數據、結案紀錄、以及 AdminActionLog 裡的所有原始審計日誌合併。
 */
    private function getCompleteCaseContext($id, $identifierType = 'match_id')
    {
        // 1. 鎖定核心 Match 紀錄，預載你指定的 staff 關聯
        $query = MatchRecord::with([
            'foundItem.staff', // Phase 1 登記人 (原本有的一套)
            'lostItem.staff',  // Phase 2 登記人
            'verifier'         // Phase 4 預約發送者
        ]);

        // 2. 靈活定位：根據傳入的 ID 類型（MatchID, ClaimID, 或 LostID）找到那筆單子
        if ($identifierType === 'match_id') {
            $match = $query->find($id);
        } elseif ($identifierType === 'claim_id') {
            $claimTemp = Claim::find($id);
            $match = $claimTemp ? $query->where('lostId', $claimTemp->lostId)->where('foundId', $claimTemp->foundId)->first() : null;
        } else {
            // 默認作為 Lost ID 處理 (用於 Timeline 彈窗)
            $match = $query->where('lostId', $id)
                        ->whereIn('status', ['Verified', 'Claimed'])
                        ->latest()
                        ->first();
        }

        // 如果連 Match 紀錄都沒有，代表這是一筆「死單」，直接回傳
        if (!$match) return null;

        // 3. 抓取 Phase 5 結案紀錄 (Claim)
        $claim = Claim::with('handler')
                    ->where('lostId', $match->lostId)
                    ->where('foundId', $match->foundId)
                    ->first();

        // 4. 🌟 關鍵：連結原本有的那一套 Audit Logs
        // 我們掃描 target_name，只要包含 Lost ID 或 Found ID 的紀錄通通抓出來。
        // 這會自動包含你 Tinker 裡的：BLOCK_STAFF, SEND_APPOINTMENT, VERIFY_MATCH 等。
        $auditLogs = AdminActionLog::where(function($q) use ($match) {
                        $q->where('target_name', 'like', "%#{$match->lostId}%")
                        ->orWhere('target_name', 'like', "%#{$match->foundId}%")
                        ->orWhere('target_name', 'like', "%Match #{$match->id}%");
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

        // 5. 統一封裝回傳
        return [
            'match'     => $match,
            'claim'     => $claim,
            'auditLogs' => $auditLogs, // 這是你全專案唯一的證據清單
            'foundItem' => $match->foundItem,
            'lostItem'  => $match->lostItem,
        ];
    }

    // ==========================================
    // 1. 預約流程 (Schedule & Confirm)
    // ==========================================

    public function process($id)
    {
        $data = $this->getCompleteCaseContext($id, 'match_id');
        if (!$data) abort(404);

        Cache::forget('staff_scan_' . auth()->id());
        return view('staff.claims.process', $data);
    }

    public function schedule(Request $request)
    {
        $request->validate([
            'match_id' => 'required',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $fullDateTimeString = $request->appointment_date . ' ' . $request->appointment_time;
        $appointmentTime = \Carbon\Carbon::parse($fullDateTimeString);

        if ($appointmentTime->isPast()) {
            return back()->withErrors(['appointment_time' => 'Time already passed. Please select a future time.']);
        }

        $match = MatchRecord::findOrFail($request->match_id);
        
        // 🌟 判斷是「第一次預約」還是「重新預約」
        $isReschedule = !is_null($match->appointment_at);
        $oldTime = $match->appointment_at ? $match->appointment_at->format('M d, h:i A') : 'None';
        $token = strtoupper(Str::random(6));

        // 執行更新
        $match->update([
            'appointment_at'     => $fullDateTimeString,
            'verification_token' => $token,
            'is_confirmed'       => false,
            'status'             => 'Verified',
            'verifiedBy'         => auth()->id(),
        ]);

        // 🌟 寫入大一統日誌 (自動區分類型)
        AdminActionLog::create([
            'admin_name'  => auth()->user()->name,
            'action_type' => $isReschedule ? 'RESCHEDULE_APPOINTMENT' : 'SEND_APPOINTMENT',
            'target_name' => "Match #{$match->id} (Lost #{$match->lostId} / Found #{$match->foundId})",
            'details'     => $isReschedule 
                ? "Action by [" . strtoupper(auth()->user()->role) . "]. Rescheduled from [{$oldTime}] to [{$appointmentTime->format('M d, h:i A')}]. Venue: [Admin Office]. Token refreshed: [{$token}]."
                : "Action by [" . strtoupper(auth()->user()->role) . "]. Initial appointment set for [{$appointmentTime->format('M d, h:i A')}]. Venue: [Admin Office]."
        ]);

        // 發送郵件
        $passengerEmail = $match->lostItem->passenger_email ?? 'staff@example.com';
        try {
            Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, route('pickup.confirm', ['token' => $token])));
            $message = 'Appointment ' . ($isReschedule ? 'Rescheduled' : 'Scheduled') . ' & Email sent.';
        } catch (\Exception $e) {
            \Log::error('Email Failed', ['error' => $e->getMessage()]);
            $message = 'Time updated, but email system failed.';
        }

        return back()->with('success', $message);
    }
    /**
     * 🌟 旅客點擊 Email 連結後的確認動作
     */
    public function confirm($token)
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();
        
        // 1. 更新確認狀態
        $match->update([
            'is_confirmed' => true,
            'confirmed_at' => now(),
        ]);

        // 2. 🌟 寫入 PASSENGER_CONFIRM 審計日誌 (讓 Timeline 抓到活證據)
        AdminActionLog::create([
            'admin_name'  => "Passenger (System)",
            'action_type' => 'PASSENGER_CONFIRM',
            'target_name' => "Match #{$match->id}",
            'details'     => "User authentication successful via encrypted email token. Time confirmed: [" . now()->format('M d, h:i A') . "]. Status changed to [Ready for Handover]."
        ]);

        // 3. 獲取全案上下文數據並返回 QR 狀態頁
        $data = $this->getCompleteCaseContext($match->id, 'match_id');
        return view('passenger.claims.qr_status', $data);
    }

    // ==========================================
    // 2. 智能驗證與雷達 (QR & Radar)
    // ==========================================

    public function smartVerify($token)
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        // 🌟 只要觸發掃描，無論結果如何先記一筆「嘗試掃描」
        if (auth()->check()) {
            $userRole = strtolower(auth()->user()->role); 
            
            // 寫入大一統日誌 (SCAN_QR_ATTEMPT)
            AdminActionLog::create([
                'admin_name'  => auth()->user()->name,
                'action_type' => 'SCAN_QR_ATTEMPT',
                'target_name' => "Match #{$match->id}",
                'details'     => "Staff [".auth()->user()->name."] (Role: {$userRole}) scanned QR code on-site. System state: " . $match->status
            ]);

            // Staff/Admin 的後續邏輯
            if ($userRole === 'admin' || $userRole === 'staff') {
                if ($match->status === 'Claimed') {
                    return view('staff.claims.qr_status_claimed', compact('match'));
                }
                // 發送雷達訊號
                Cache::put('staff_scan_' . auth()->id(), $match->id, now()->addMinutes(2));
                return view('staff.claims.scan_success', compact('match'));
            }
        }

        // 乘客端邏輯
        $data = $this->getCompleteCaseContext($match->id, 'match_id');
        if ($match->status === 'Claimed') {
            return view('passenger.claims.pickup_success_receipt', $data);
        }
        return view('passenger.claims.qr_status', $data);
    }

    public function checkRecentScan(Request $request)
    {
        $scannedId = Cache::pull('staff_scan_' . auth()->id());
        if ($scannedId) {
            $currentId = (int)$request->query('current_id', 0);
            if ($currentId > 0 && $scannedId === $currentId) {
                return response()->json([
                    'status' => 'success',
                    'redirect_url' => route('staff.claims.handover', $scannedId)
                ]);
            }
        }
        return response()->json(['status' => 'waiting']);
    }

    public function checkConfirmation($id)
    {
        $match = MatchRecord::findOrFail($id);
        return response()->json(['is_confirmed' => (bool)$match->is_confirmed]);
    }

    // ==========================================
    // 3. 現場結案 (Handover)
    // ==========================================

    public function handover(Request $request, $id)
    {
        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($id);
        $view = ($request->query('step') == 2) ? 'staff.claims.enter_ic' : 'staff.claims.verify_action';
        
        return view($view, [
            'match' => $match,
            'lostItem' => $match->lostItem,
            'foundItem' => $match->foundItem
        ]);
    }

    public function completeHandover(Request $request, $id)
    {
        $request->validate([
            'passenger_name_ic' => 'required|string|max:255',
            'passenger_ic'      => 'required|string|max:50',
            'handover_photo'    => 'required|image|max:5120',
        ]);

        $match = MatchRecord::findOrFail($id);

        try {
            DB::transaction(function () use ($match, $request) {
                $photoPath = $request->file('handover_photo')->store('handover_photos', 'public');

                Claim::create([
                    'match_id'          => $match->id, // 🌟 核心：存入 Match 表的真實 ID，不再讓它亂跳
                    'lostId'            => $match->lostId,
                    'foundId'           => $match->foundId,
                    'claimerName'       => $request->passenger_name_ic,
                    'claimerIcPassport' => $request->passenger_ic,
                    'claimerPhone'      => $match->lostItem->passenger_phone ?? 'N/A',
                    'processedBy'       => auth()->id(),
                    'claimedAt'         => now(),
                    'handover_photo'    => $photoPath,
                ]);

                $match->update(['status' => 'Claimed', 'verifiedAt' => now()]);
                $match->lostItem->update(['status' => 'Claimed']);
                $match->foundItem->update(['status' => 'Claimed']);

                AdminActionLog::create([
                    'admin_name'  => auth()->user()->name,
                    'action_type' => 'ITEM_HANDOVER_SUCCESS',
                    'target_name' => "Lost Report #{$match->lostId} | Passenger: {$request->passenger_name_ic}",
                    'details'     => "Handover confirmed with photo. IC: {$request->passenger_ic}"
                ]);
            });

// 如果你的路由名稱是 history，請改成這樣：
            return redirect()->route('staff.claims.index')->with('success', 'Handover Completed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Handover Failed: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 4. 歷史與收據 (History & Timeline)
    // ==========================================

    public function index()
    {
        // 抓取所有結案紀錄，並預載關聯的物品、報失單與處理人 (Handler)
        $claims = Claim::with([
            'foundItem', 
            'lostItem', 
            'handler' // Phase 5 的實際執行 Staff
        ])
        ->latest('claimedAt') // 按照領取時間排序，最新的在上面
        ->paginate(15);      // 每頁顯示 15 筆

        // 🌟 這裡對應 resources/views/staff/claims/index.blade.php
        return view('staff.claims.index', compact('claims'));
    }

    public function showReceipt($matchId)
    {
        // 1. 拿到當前 Match 的完整數據（包含 LostItem, FoundItem, Claim）
        $data = $this->getCompleteCaseContext($matchId, 'match_id');

        if (!$data) {
            abort(404, 'Receipt not found.');
        }

        // 🌟 2. 完善審計：當點擊 View 時，立即在資料庫產生一條 VIEW_RECEIPT 日誌
        // 這樣你在 Audit Logs Page 就能看到這條紀錄了
        \App\Models\AdminActionLog::create([
            'admin_name'  => auth()->user()->name,
            'action_type' => 'VIEW_RECEIPT', 
            'target_name' => "Match #{$matchId}", 
            'details'     => "Admin " . auth()->user()->name . " viewed the official receipt/manifest for Match #{$matchId}."
        ]);

        // 3. 返回收據視圖
        return view('staff.claims.receipt', $data);
    }
    public function getTimelineHtml($id)
    {
        $data = $this->getCompleteCaseContext($id, 'lost_id');
        if (!$data) return '<div class="p-6 text-center text-gray-500 font-bold">No history available.</div>';
        return view('staff.claims.partials.timeline', $data)->render();
    }
}