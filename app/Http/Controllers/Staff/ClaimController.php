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
     * 1. 準備領取流程頁面
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
     * 2. 設定預約時間並發送第一封確認信 (Email #1)
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'match_id'         => 'required',
            'appointment_date' => 'required|date|after:now',
            'appointment_time' => 'required',
        ]);

        $match = MatchRecord::findOrFail($request->match_id);
        $lostItem = LostItemReport::find($match->lostId);
        $token = strtoupper(Str::random(6));

        // 更新預約資訊
        $match->update([
            'appointment_at'     => $request->appointment_date . ' ' . $request->appointment_time,
            'verification_token' => $token,
            'is_confirmed'       => false,
        ]);

        $confirmLink = route('pickup.confirm', ['token' => $token]);
        $passengerEmail = $lostItem->passenger_email;

        try {
            Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, $confirmLink));
            $message = 'Appointment scheduled! Verification link sent to ' . $passengerEmail;
        } catch (\Exception $e) {
            Log::error("Email #1 Failed: " . $e->getMessage());
            $message = 'Appointment set, but email failed: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    /**
     * 🌟 3. 智能驗證入口 (QR Code 指向此路由)
     * 根據訪問者身份顯示不同介面
     */
    public function smartVerify($token)
    {
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['lostItem', 'foundItem'])
                    ->firstOrFail();

        // 情況 A：工作人員掃碼 (已登入)
        if (auth()->check() && auth()->user()->role === 'staff') {
            
            if ($match->status === 'Claimed') {
                return view('staff.claims.qr_status_claimed', compact('match'));
            }

            // 進入照片比對與結案按鈕頁面
            return view('staff.claims.verify_action', compact('match'));
        }

        // 情況 B：旅客掃碼 (顯示數位收據，無按鈕)
        return view('passenger.claims.qr_status', compact('match'));
    }

    /**
     * 🌟 4. 最終領取確認 (Handover)
     * 工作人員在對比照片後按下按鈕結案
     */
    public function completeHandover(Request $request, $id)
    {
        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($id);

        try {
            DB::transaction(function () use ($match) {
                // 1. 同步更新所有關聯狀態為已領取
                $match->update(['status' => 'Claimed']);
                $match->lostItem->update(['status' => 'Claimed']);
                $match->foundItem->update(['status' => 'Claimed']);

                // 2. 寫入 Admin 操作日誌
                AdminActionLog::create([
                    'admin_name'  => auth()->user()->name,
                    'action_type' => 'ITEM_HANDOVER_SUCCESS',
                    'target_name' => "Passenger: " . $match->lostItem->passenger_name,
                    'details'     => "Handed over Found Item #{$match->foundItem->id} via QR verification."
                ]);
            });

            return redirect()->route('staff.lost-items.index')
                             ->with('success', '✅ Handover successful. The case is now closed!');

        } catch (\Exception $e) {
            Log::error("Handover Error: " . $e->getMessage());
            return back()->with('error', 'Critical Error: Could not update item status.');
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

        // 檢查旅客是否已點擊 Email 確認連結
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
        // 🌟 預載入統一命名關聯 lostItem
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