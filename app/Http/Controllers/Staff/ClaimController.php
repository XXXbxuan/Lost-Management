<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LostItemReport;
use App\Models\FoundItem;
use App\Models\MatchRecord;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage; // ✅ 新增
use App\Mail\AppointmentConfirmation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\AdminActionLog;

class ClaimController extends Controller
{
    public function createClaim(Request $request)
    {
        $lostId = $request->query('lost_id');

        $lostReport = LostItemReport::findOrFail($lostId);

        $match = MatchRecord::where('lostId', $lostReport->id)
            ->where('status', 'Verified')
            ->firstOrFail();

        $foundItem = FoundItem::findOrFail($match->foundId);

        return view('staff.claims.process', compact('lostReport', 'foundItem', 'match'));
    }

    public function schedule(Request $request)
    {
        $request->validate([
            'match_id' => 'required',
            'appointment_date' => 'required|date|after:now',
            'appointment_time' => 'required',
        ]);

        $match = MatchRecord::findOrFail($request->match_id);
        $lostReport = LostItemReport::find($match->lostId);
        $token = strtoupper(Str::random(6));

        $match->update([
            'appointment_at' => $request->appointment_date . ' ' . $request->appointment_time,
            'verification_token' => $token,
            'is_confirmed' => false,
        ]);

        // 🌟 核心修改：第一封信的連結是去「確認頁面」，而不是直接給 QR
        $confirmLink = route('pickup.confirm', ['token' => $token]);
        $passengerEmail = $lostReport->passenger_email ?? 'chiabx-wp22@student.tarc.edu.my';

        try {
            // 注意：這裡我們暫時不傳 $qrRaw，因為第一封信只需要確認按鈕
            Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, $confirmLink));
            $message = 'Appointment set! Confirmation link sent to ' . $passengerEmail;
        } catch (\Exception $e) {
            $message = 'Appointment set, but email failed: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    /**
     * 🌈 核心新增：Smart Verify (智能分流判斷)
     * 此網址為 QR Code 的內容：route('pickup.verify', ['token' => $token])
     */
    public function smartVerify($token)
    {
        $match = MatchRecord::where('verification_token', $token)
                    ->with(['lostReport', 'foundItem'])
                    ->firstOrFail();

        // 情況 B：判斷是否為已登入的工作人員   
        if (auth()->check() && auth()->user()->role === 'staff') {
            // 導向 Staff 專屬驗證頁面 (包含對比照片與確認按鈕)
            return view('staff.claims.verify_action', compact('match'));
        }

        // 情況 A：旅客或一般民眾掃碼
        // 僅作為數位收據顯示資訊，沒有操作按鈕
        return view('passenger.claims.qr_status', compact('match'));
    }

    /**
     * 🛡️ 核心新增：Staff 點擊按鈕後的結案動作
     */
    public function completeHandover($id)
    {
        DB::transaction(function () use ($id) {
            $match = MatchRecord::findOrFail($id);
            
            // 1. 同步更新資料庫狀態為已領取
            $match->update(['status' => 'Claimed']);
            $match->lostReport->update(['status' => 'Claimed']);
            $match->foundItem->update(['status' => 'Claimed']);

            // 2. 安全防禦：讓 Token 失效防止重複領取
            $match->update(['verification_token' => null]);

            // 3. 記錄專業日誌
            AdminActionLog::create([
                'admin_name'  => auth()->user()->name ?? 'Staff',
                'action_type' => 'ITEM_HANDOVER',
                'target_name' => "Match ID: {$id}",
                'details'     => "Handover finalized via Staff QR Scan. Item status changed to Claimed."
            ]);
        });

        return redirect()->route('staff.dashboard')->with('success', 'Item Handover Completed!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lostId' => 'required',
            'foundId' => 'required',
            'claimerName' => 'required',
            'claimerIcPassport' => 'required',
            'claimerPhone' => 'required',
        ]);

        $match = MatchRecord::where('lostId', $request->lostId)
            ->where('foundId', $request->foundId)
            ->firstOrFail();

        if (!$match->is_confirmed) {
            return back()->withErrors([
                'claimerIcPassport' => 'Error: The passenger has NOT confirmed the appointment via Email link yet. Cannot proceed.'
            ]);
        }

        DB::transaction(function () use ($request) {
            Claim::create([
                'lostId' => $request->lostId,
                'foundId' => $request->foundId,
                'claimerName' => $request->claimerName,
                'claimerIcPassport' => $request->claimerIcPassport,
                'claimerPhone' => $request->claimerPhone,
                'processedBy' => auth()->id(),
                'claimedAt' => now(),
            ]);

            LostItemReport::where('id', $request->lostId)->update(['status' => 'Claimed']);
            FoundItem::where('id', $request->foundId)->update(['status' => 'Claimed']);

            AdminActionLog::create([
                'admin_name'  => auth()->user()->name ?? 'Staff',
                'action_type' => 'ITEM_HANDOVER',
                'target_name' => "Claimer: " . $request->claimerName,
                'details'     => "Handed over Found Item #{$request->foundId} (matched to Lost Report #{$request->lostId}). " .
                    "ID: {$request->claimerIcPassport}, Phone: {$request->claimerPhone}."
            ]);
        });

        return redirect()->route('staff.lost-items.index')
            ->with('success', 'Handover completed! Item released from inventory.');
    }

    public function index()
    {
        $claims = Claim::with(['foundItem', 'lostReport', 'handler'])
            ->latest('claimedAt')
            ->paginate(10);

        return view('staff.claims.index', compact('claims'));
    }

    public function getTimelineHtml($id)
    {
        $lostReport = LostItemReport::findOrFail($id);

        $match = MatchRecord::where('lostId', $lostReport->id)
            ->where('status', 'Verified')
            ->first();

        if (!$match || !$match->foundItem) {
            return '<div class="p-6 text-center text-gray-500">No verified timeline data available.</div>';
        }

        $foundItem = $match->foundItem;

        return view('staff.claims.partials.timeline', compact('foundItem', 'match'))->render();
    }
}