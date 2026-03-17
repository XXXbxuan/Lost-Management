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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ClaimController extends Controller
{
    /**
     * 🌟 全案數據大一統提取器 (The Single Source of Truth)
     */
    private function getCompleteCaseContext($id, $identifierType = 'match_id')
    {
        $query = MatchRecord::with([
            'foundItem.staff',
            'lostItem.staff',
            'verifier'
        ]);

        if ($identifierType === 'match_id') {
            $match = $query->find($id);
        } elseif ($identifierType === 'claim_id') {
            $claimTemp = Claim::find($id);
            $match = $claimTemp
                ? $query->where('lostId', $claimTemp->lostId)->where('foundId', $claimTemp->foundId)->first()
                : null;
        } else {
            $match = $query->where('lostId', $id)
                ->whereIn('status', ['Verified', 'Claimed', 'Confirmed', 'Reschedule Requested'])
                ->latest()
                ->first();
        }

        if (!$match) return null;

        $claim = Claim::with('handler')
            ->where('lostId', $match->lostId)
            ->where('foundId', $match->foundId)
            ->first();

        $auditLogs = AdminActionLog::where(function ($q) use ($match) {
            $q->where('target_name', 'like', "%Match #{$match->id}%")
              ->orWhere('target_name', '=', "Lost #{$match->lostId} vs Found #{$match->foundId}");
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return [
            'match'     => $match,
            'claim'     => $claim,
            'auditLogs' => $auditLogs,
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

        // 判斷是第一次還是重新預約（你原本寫法保留）
        $isReschedule = !is_null($match->appointment_at);
        $oldTime = $match->appointment_at ? \Carbon\Carbon::parse($match->appointment_at)->format('M d, h:i A') : 'None';
        $token = strtoupper(Str::random(6));

        // ✅ 核心：更新 appointment 時，清掉 Reject 提議資料（避免一直卡 Reject）
        $match->update([
            'appointment_at'     => $fullDateTimeString,
            'verification_token' => $token,
            'is_confirmed'       => false,
            'status'             => 'Verified',
            'verifiedBy'         => auth()->id(),

            // ✅ 清掉 passenger suggested
            'suggested_time_1'   => null,
            'suggested_time_2'   => null,
            'suggested_remarks'  => null,
            'rejected_at'        => null,
        ]);

        AdminActionLog::create([
            'admin_name'  => auth()->user()->name,
            'action_type' => $isReschedule ? 'RESCHEDULE_APPOINTMENT' : 'SEND_APPOINTMENT',
            'target_name' => "Match #{$match->id} (Lost #{$match->lostId} / Found #{$match->foundId})",
            'details'     => $isReschedule
                ? "Action by [" . strtoupper(auth()->user()->role) . "]. Rescheduled from [{$oldTime}] to [{$appointmentTime->format('M d, h:i A')}]. Venue: [Admin Office]. Token refreshed: [{$token}]."
                : "Action by [" . strtoupper(auth()->user()->role) . "]. Initial appointment set for [{$appointmentTime->format('M d, h:i A')}]. Venue: [Admin Office]."
        ]);

        $passengerEmail = $match->lostItem->passenger_email ?? 'staff@example.com';

        try {
            Mail::to($passengerEmail)->send(
                new AppointmentConfirmation($match, route('pickup.confirm', ['token' => $token]))
            );
            $message = 'Appointment ' . ($isReschedule ? 'Rescheduled' : 'Scheduled') . ' & Email sent.';
        } catch (\Exception $e) {
            \Log::error('Email Failed', ['error' => $e->getMessage()]);
            $message = 'Time updated, but email system failed.';
        }

        return back()->with('success', $message);
    }

    /**
     * ✅ Ajax：2 秒自動檢查 passenger 有沒有 Reject + suggested time/remarks
     * 你要求：參考 check_scan 的方式
     */
    public function checkReschedule($id)
    {
        $match = MatchRecord::findOrFail($id);

        $needRefresh =
            ($match->status === 'Reschedule Requested')
            || !is_null($match->rejected_at)
            || !is_null($match->suggested_time_1)
            || !is_null($match->suggested_time_2)
            || (trim((string)($match->suggested_remarks ?? '')) !== '');

        return response()->json([
            'status' => $needRefresh ? 'refresh' : 'waiting'
        ]);
    }

    // ==========================================
    // 2. 智能驗證與雷達 (QR & Radar)
    // ==========================================

    public function smartVerify($token)
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        if (auth()->check()) {
            $userRole = strtolower(auth()->user()->role);

            AdminActionLog::create([
                'admin_name'  => auth()->user()->name,
                'action_type' => 'SCAN_QR_ATTEMPT',
                'target_name' => "Match #{$match->id}",
                'details'     => "Staff [" . auth()->user()->name . "] (Role: {$userRole}) scanned QR code on-site. System state: " . $match->status
            ]);

            if ($userRole === 'admin' || $userRole === 'staff') {
                if ($match->status === 'Claimed') {
                    return view('staff.claims.qr_status_claimed', compact('match'));
                }

                Cache::put('staff_scan_' . auth()->id(), $match->id, now()->addMinutes(2));
                return view('staff.claims.scan_success', compact('match'));
            }
        }

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
            $currentId = (int) $request->query('current_id', 0);
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
        return response()->json(['is_confirmed' => (bool) $match->is_confirmed]);
    }

    // ==========================================
    // 3. 現場結案 (Handover)
    // ==========================================

    public function handover(Request $request, $id)
    {
        Cache::forget('staff_scan_' . auth()->id());

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
                    'match_id'          => $match->id,
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
        $claims = Claim::with(['foundItem', 'lostItem', 'handler'])
            ->latest('claimedAt')
            ->paginate(15);

        return view('staff.claims.index', compact('claims'));
    }

    public function showReceipt($matchId)
    {
        $data = $this->getCompleteCaseContext($matchId, 'match_id');
        if (!$data) abort(404, 'Receipt not found.');

        AdminActionLog::create([
            'admin_name'  => auth()->user()->name,
            'action_type' => 'VIEW_RECEIPT',
            'target_name' => "Match #{$matchId}",
            'details'     => "Admin " . auth()->user()->name . " viewed the official receipt/manifest for Match #{$matchId}."
        ]);

        return view('staff.claims.receipt', $data);
    }

    public function getTimelineHtml($id)
    {
        $data = $this->getCompleteCaseContext($id, 'lost_id');
        if (!$data) return '<div class="p-6 text-center text-gray-500 font-bold">No history available.</div>';
        return view('staff.claims.partials.timeline', $data)->render();
    }
}