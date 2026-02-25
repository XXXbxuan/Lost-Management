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
use App\Mail\AppointmentConfirmation;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // 🆕 引入造码机
// 👇 1. 引入 Log 模型
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
        $fullDateTime = $request->appointment_date . ' ' . $request->appointment_time;
        $token = strtoupper(Str::random(6));

        $match->update([
            'appointment_at' => $fullDateTime,
            'verification_token' => $token,
            'is_confirmed' => false,
        ]);

        $link = route('pickup.confirm', ['token' => $token]);
        $passengerEmail = $lostReport->passenger_email ?? 'chiabx-wp22@student.tarc.edu.my'; 
        
        // 🌟 核心魔法：使用 SVG 格式（不需要 Imagick，不會報錯）
        $qrRaw = QrCode::format('svg') 
                         ->size(250)
                         ->color(0, 162, 255)
                         ->margin(1)
                         ->generate($link);
        
        // 將圖片代碼轉成 Base64 字串，這樣 Gmail 才能直接吃掉它
        $qrCodeBase64 = base64_encode($qrRaw);
        
        try {
            // 🆕 傳送 $qrCodeBase64 給郵件類別
            Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, $link, $qrCodeBase64));
            
            $message = 'Appointment set! QR Code Email sent to ' . $passengerEmail;
            
            // ✅ [LOG 4] 記錄發送預約
            AdminActionLog::create([
                'admin_name'  => auth()->user()->name ?? 'Staff',
                'action_type' => 'SEND_APPOINTMENT',
                'target_name' => "Passenger Email: " . $passengerEmail,
                'details'     => "Scheduled: {$fullDateTime}. Token generated. Email sent successfully with Base64 QR."
            ]);

        } catch (\Exception $e) {
            $message = 'Appointment set, but email failed to send: ' . $e->getMessage();
            
            AdminActionLog::create([
                'admin_name'  => auth()->user()->name ?? 'Staff',
                'action_type' => 'APPOINTMENT_EMAIL_FAIL',
                'target_name' => $passengerEmail,
                'details'     => "Error: " . $e->getMessage()
            ]);
        }

        return back()->with('success', $message);
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
            return back()->withErrors(['claimerIcPassport' => 'Error: The passenger has NOT confirmed the appointment via Email link yet. Cannot proceed.']);
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

            // ✅ [LOG 5] 记录物品归还 (Item Handover)
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