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
use Illuminate\Support\Facades\Cache; // 🌟 必須引入 Cache
use Illuminate\Support\Str;

class ClaimController extends Controller
{
    /**
     * 1. 準備領取流程頁面 (針對剛配對成功，尚未建立預約的狀態)
     */
    public function createClaim(Request $request)
    {
        $lostId = $request->query('lost_id');
        $lostItem = LostItemReport::findOrFail($lostId);
        $match = MatchRecord::where('lostId', $lostItem->id)
            ->where('status', 'Verified')
            ->firstOrFail();
        $foundItem = FoundItem::findOrFail($match->foundId);

        return view('staff.claims.process', compact('lostItem', 'foundItem', 'match'));
    }

    /**
     * 🌟 2. 處理既有的配對紀錄 (包含 Step 2 雷達等待頁面)
     */
    public function process($id)
    {
        $match = MatchRecord::findOrFail($id);
        $lostItem = LostItemReport::findOrFail($match->lostId);
        $foundItem = FoundItem::findOrFail($match->foundId);

        // 🌟 物理隔離：員工進入此頁面，立刻清空舊的掃描快取，避免幽靈跳轉
        Cache::forget('staff_scan_' . auth()->id());

        return view('staff.claims.process', compact('match', 'lostItem', 'foundItem'));
    }

    /**
     * 3. 設定預約時間並發送確認信
     */
    public function schedule(Request $request)
    {
        // ... (保持你原本的 schedule 邏輯不變) ...
        $request->validate([
            'match_id'         => 'required',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $fullDateTimeString = $request->appointment_date . ' ' . $request->appointment_time;
        
        $fullDateTime = \Carbon\Carbon::parse($fullDateTimeString);
        if ($fullDateTime->isPast()) {
            return back()->withErrors(['appointment_time' => 'Oops! The time has already passed. Please select a future time.']);
        }

        $match = MatchRecord::findOrFail($request->match_id);
        $lostItem = LostItemReport::findOrFail($match->lostId);
        $token = strtoupper(Str::random(6));

        $match->update([
            'appointment_at'     => $fullDateTimeString,
            'verification_token' => $token,
            'is_confirmed'       => false,
            'status'             => 'Verified', 
        ]);

        $confirmLink = route('pickup.confirm', ['token' => $token]);
        $passengerEmail = $lostItem->passenger_email ?? 'chiabx-wp22@student.tarc.edu.my';

        try {
            Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, $confirmLink));
            $message = 'Appointment scheduled! Verification link sent to ' . $passengerEmail;
        } catch (\Exception $e) {
            \Log::error('Email #1 Failed', ['to' => $passengerEmail, 'error' => $e->getMessage()]);
            $message = 'Appointment set, but email failed: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    /**
     * 🌟 4. 智能驗證入口 (QR Code 掃描後) - 手機與電腦分工
     */
    public function smartVerify($token)
    {
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['lostItem', 'foundItem'])
                    ->firstOrFail();

        if (auth()->check()) {
            $userRole = strtolower(auth()->user()->role); 
            
            // 判斷是否為工作人員掃描
            if ($userRole === 'admin' || $userRole === 'staff') {
                
                // 🚫 防呆：如果已經領取，導向你現有的已領取頁面
                if ($match->status === 'Claimed') {
                    return view('staff.claims.qr_status_claimed', compact('match'));
                }

                // ✅ 發送雷達訊號：把這筆 Match ID 存進快取
                Cache::put('staff_scan_' . auth()->id(), $match->id, now()->addMinutes(2));

                // 🌟 手機端顯示成功提示 (請確保有建立 scan_success.blade.php)
                return view('staff.claims.scan_success', compact('match'));
            }
        }

        // --- 旅客視角 ---
        if ($match->status === 'Claimed') {
            return view('passenger.claims.pickup_success_receipt', compact('match'));
        }

        return view('passenger.claims.qr_status', compact('match'));
    }

    /**
     * 🌟 5. 電腦端：雷達監聽器 (必須補上這個方法，雷達才有用)
     */
    public function checkRecentScan(Request $request)
    {
        $staffId = auth()->id();
        $cacheKey = 'staff_scan_' . $staffId;

        // 🌟 讀取後立刻刪除 (pull)，保證不重複跳轉
        $scannedId = Cache::pull($cacheKey);

        if ($scannedId) {
            $currentId = (int)$request->query('current_id', 0);

            // 🌟 隔離比對：只有手機掃的 ID 跟電腦現在看的 ID 對上了，才准跳轉
            if ($currentId > 0 && $scannedId === $currentId) {
                return response()->json([
                    'status' => 'success',
                    // 🚀 核心：跳轉到你現有的 verify_action (對比圖) 頁面！
                    'redirect_url' => route('staff.claims.verify_action', $scannedId)
                ]);
            }
        }
        return response()->json(['status' => 'waiting']);
    }

    /**
     * 🌟 6. 顯示對比圖 (這就是你原本就有的 verify_action 頁面)
     */
    public function verifyAction($id)
    {
        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($id);
        
        // 確保不會報錯
        return view('staff.claims.verify_action', [
            'match' => $match,
            'lostItem' => $match->lostItem,
            'foundItem' => $match->foundItem,
        ]);
    }

    /**
     * 7. 最終領取確認 (Handover)
     */
    public function completeHandover(Request $request, $id)
    {
        // 驗證 IC 欄位
        $request->validate([
            'passenger_ic' => 'required|string|max:50',
        ]);

        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($id);

        if ($match->status === 'Claimed') {
            return redirect()->route('staff.claims.index')
                            ->with('info', 'This case has already been closed.');
        }

        try {
            DB::transaction(function () use ($match, $request) {
                // 更新 Match 紀錄並寫入 IC
                $match->update([
                    'status'       => 'Claimed',
                    'verifiedBy'   => auth()->id(), 
                    'verifiedAt'   => now(), 
                    'passenger_ic' => $request->passenger_ic, // 🌟 存入剛輸入的 IC
                ]);

                $match->lostItem->update(['status' => 'Claimed']);
                $match->foundItem->update(['status' => 'Claimed']);

                AdminActionLog::create([
                    'admin_name'  => auth()->user()->name,
                    'action_type' => 'ITEM_HANDOVER_SUCCESS',
                    'target_name' => "Passenger: " . ($match->lostItem->passenger_name ?? 'Unknown'),
                    'details'     => "Handed over Item: [{$match->foundItem->item_name}] via QR Code Security Verification. IC: {$request->passenger_ic}"
                ]);
            });

            return redirect()->route('staff.claims.index') 
                            ->with('success', '✅ Handover successful. The case is now permanently closed!');

        } catch (\Exception $e) {
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