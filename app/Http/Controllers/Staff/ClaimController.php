<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\CompleteHandoverRequest;
use App\Http\Requests\Staff\ScheduleClaimAppointmentRequest;
use App\Mail\AppointmentConfirmation;
use App\Models\AdminActionLog;
use App\Models\Claim;
use App\Models\MatchRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ClaimController extends Controller
{
    private function buildCaseContext(int $id, string $identifierType = 'match_id'): ?array
    {
        $query = MatchRecord::with([
            'foundItem.staff',
            'lostItem.staff',
            'verifier.staff',
        ]);

        if ($identifierType === 'match_id') {
            $match = $query->find($id);
        } elseif ($identifierType === 'claim_id') {
            $claim = Claim::find($id);

            $match = $claim
                ? $query->where('lostId', $claim->lostId)
                    ->where('foundId', $claim->foundId)
                    ->first()
                : null;
        } else {
            $match = $query->where('lostId', $id)
                ->whereIn('status', ['Verified', 'Claimed', 'Confirmed', 'Reschedule Requested'])
                ->latest()
                ->first();
        }

        if (!$match) {
            return null;
        }

        $claim = Claim::with('handler.staff')
            ->where('match_id', $match->id)
            ->latest('id')
            ->first();

        $auditLogs = AdminActionLog::where(function ($query) use ($match) {
            $query->where('target_name', 'like', "%Match #{$match->id}%")
                ->orWhere('target_name', '=', "Lost #{$match->lostId} vs Found #{$match->foundId}");
        })
            ->orderBy('created_at', 'asc')
            ->get();

        return [
            'match' => $match,
            'claim' => $claim,
            'auditLogs' => $auditLogs,
            'foundItem' => $match->foundItem,
            'lostItem' => $match->lostItem,
        ];
    }

    public function showClaimProcessing(int $matchId): View
    {
        $data = $this->buildCaseContext($matchId, 'match_id');

        if (!$data) {
            abort(404);
        }

        Cache::forget('staff_scan_' . auth()->id());

        return view('staff.claims.claim_processing', $data);
    }

    public function scheduleAppointment(ScheduleClaimAppointmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $fullDateTimeString = $validated['appointment_date'] . ' ' . $validated['appointment_time'];
        $appointmentTime = now()->parse($fullDateTimeString);

        if ($appointmentTime->isPast()) {
            return back()->withErrors([
                'appointment_time' => 'Time already passed. Please select a future time.',
            ]);
        }

        $match = MatchRecord::findOrFail($validated['match_id']);

        $isReschedule = !is_null($match->appointment_at);
        $oldTime = $match->appointment_at
            ? now()->parse($match->appointment_at)->format('M d, h:i A')
            : 'None';

        $token = strtoupper(Str::random(6));

        $match->update([
            'appointment_at' => $fullDateTimeString,
            'verification_token' => $token,
            'is_confirmed' => false,
            'status' => 'Verified',
            'verifiedBy' => auth()->id(),
            'suggested_time_1' => null,
            'suggested_time_2' => null,
            'suggested_remarks' => null,
            'rejected_at' => null,
        ]);

        $this->writeActionLog(
            $isReschedule ? 'RESCHEDULE_APPOINTMENT' : 'SEND_APPOINTMENT',
            "Match #{$match->id} (Lost #{$match->lostId} / Found #{$match->foundId})",
            $isReschedule
                ? "Action by [" . strtoupper(auth()->user()->role) . "]. Rescheduled from [{$oldTime}] to [{$appointmentTime->format('M d, h:i A')}]. Venue: [Admin Office]. Token refreshed: [{$token}]."
                : "Action by [" . strtoupper(auth()->user()->role) . "]. Initial appointment set for [{$appointmentTime->format('M d, h:i A')}]. Venue: [Admin Office]."
        );

        $passengerEmail = $match->lostItem->passenger_email ?? 'staff@example.com';

        try {
            Mail::to($passengerEmail)->send(
                new AppointmentConfirmation($match, route('pickup.confirm', ['token' => $token]))
            );

            $message = 'Appointment ' . ($isReschedule ? 'rescheduled' : 'scheduled') . ' and email sent.';
        } catch (Throwable $e) {
            Log::error('Appointment confirmation email failed.', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);

            $message = 'Appointment updated, but the email system failed.';
        }

        return back()->with('success', $message);
    }

    public function checkRescheduleStatus(int $matchId): JsonResponse
    {
        $match = MatchRecord::findOrFail($matchId);

        $needRefresh =
            $match->status === 'Reschedule Requested'
            || !is_null($match->rejected_at)
            || !is_null($match->suggested_time_1)
            || !is_null($match->suggested_time_2)
            || trim((string) ($match->suggested_remarks ?? '')) !== '';

        return response()->json([
            'status' => $needRefresh ? 'refresh' : 'waiting',
        ]);
    }

    public function verifyPickupToken(string $token): View
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        if (auth()->check()) {
            $userRole = strtolower(auth()->user()->role);

            $this->writeActionLog(
                'SCAN_QR_ATTEMPT',
                "Match #{$match->id}",
                "Staff [{$this->getActorName()}] (Role: {$userRole}) scanned QR code on-site. System state: {$match->status}"
            );

            if (in_array($userRole, ['admin', 'staff'])) {
                if ($match->status === 'Claimed') {
                    return view('staff.claims.claimed_qr_status', compact('match'));
                }

                Cache::put('staff_scan_' . auth()->id(), $match->id, now()->addMinutes(2));

                return view('staff.claims.scan_verification_success', compact('match'));
            }
        }

        $data = $this->buildCaseContext($match->id, 'match_id');

        if ($match->status === 'Claimed') {
            return view('passenger.claims.pickup_success_receipt', $data);
        }

        return view('passenger.claims.qr_verification_status', $data);
    }

    public function checkRecentScan(Request $request): JsonResponse
    {
        $scannedId = Cache::pull('staff_scan_' . auth()->id());

        if ($scannedId) {
            $currentId = (int) $request->query('current_id', 0);

            if ($currentId > 0 && $scannedId === $currentId) {
                return response()->json([
                    'status' => 'success',
                    'redirect_url' => route('staff.claims.handover', $scannedId),
                ]);
            }
        }

        return response()->json([
            'status' => 'waiting',
        ]);
    }

    public function checkConfirmationStatus(int $matchId): JsonResponse
    {
        $match = MatchRecord::findOrFail($matchId);

        return response()->json([
            'is_confirmed' => (bool) $match->is_confirmed,
        ]);
    }

    public function showHandoverWorkflow(Request $request, int $matchId): View
    {
        Cache::forget('staff_scan_' . auth()->id());

        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($matchId);

        $view = $request->query('step') == 2
            ? 'staff.claims.identity_verification'
            : 'staff.claims.verification_result';

        return view($view, [
            'match' => $match,
            'lostItem' => $match->lostItem,
            'foundItem' => $match->foundItem,
        ]);
    }

    public function finalizeHandover(CompleteHandoverRequest $request, int $matchId): RedirectResponse
    {
        $validated = $request->validated();

        $match = MatchRecord::with(['lostItem', 'foundItem'])->findOrFail($matchId);

        try {
            DB::transaction(function () use ($match, $request, $validated) {
                $photoPath = $request->file('handover_photo')->store('handover_photos', 'public');
                $claimedAt = now();

                $processedByName = auth()->user()->staff->name
                    ?? auth()->user()->username
                    ?? auth()->user()->name
                    ?? 'Authorized Staff';

                $claim = Claim::create([
                    'match_id' => $match->id,
                    'lostId' => $match->lostId,
                    'foundId' => $match->foundId,
                    'processedBy' => auth()->id(),
                    'processed_by_name' => $processedByName,
                    'claimerName' => $validated['claimerName'],
                    'claimerIcPassport' => $validated['claimerIcPassport'],
                    'claimerPhone' => $match->lostItem->passenger_phone ?? '-',
                    'handover_photo' => $photoPath,
                    'claimedAt' => $claimedAt,
                    'handover_notes' => filled($validated['handover_notes'] ?? null)
                        ? $validated['handover_notes']
                        : null,
                ]);

                $claim->update([
                    'receipt_no' => 'REF-' . $claim->id,
                ]);

                $match->update([
                    'status' => 'Claimed',
                    'verifiedAt' => $claimedAt,
                ]);

                $match->lostItem->update([
                    'status' => 'Claimed',
                ]);

                $match->foundItem->update([
                    'status' => 'Claimed',
                ]);

                $this->writeActionLog(
                    'ITEM_HANDOVER_SUCCESS',
                    "Lost Report #{$match->lostId} | Passenger: {$validated['claimerName']}",
                    "Handover confirmed with photo. IC: {$validated['claimerIcPassport']}"
                );
            });

            return redirect()
                ->route('staff.claims.index')
                ->with('success', 'Handover completed.');
        } catch (Throwable $e) {
            Log::error('Claim handover finalization failed.', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Handover failed. Please try again.');
        }
    }

    public function index(Request $request): View
    {
        $openClaimId = $request->query('open_claim');

        $query = Claim::query()->latest();
        $perPage = 10;

        if ($openClaimId) {
            $orderedIds = (clone $query)->pluck('id')->values();

            $position = $orderedIds->search(function ($id) use ($openClaimId) {
                return (string) $id === (string) $openClaimId;
            });

            if ($position !== false) {
                $targetPage = (int) floor($position / $perPage) + 1;

                \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($targetPage) {
                    return $targetPage;
                });
            }
        }

        $claims = $query->paginate($perPage)->appends($request->query());

        return view('staff.claims.index', compact('claims', 'openClaimId'));
    }

    public function showClaimReceipt(int $matchId): View
    {
        $data = $this->buildCaseContext($matchId, 'match_id');

        if (!$data) {
            abort(404, 'Receipt not found.');
        }

        $this->writeActionLog(
            'VIEW_RECEIPT',
            "Match #{$matchId}",
            "Admin {$this->getActorName()} viewed the official receipt/manifest for Match #{$matchId}."
        );

        return view('staff.claims.claim_receipt', $data);
    }

    public function renderTimelineHtml(int $lostId): string
    {
        $data = $this->buildCaseContext($lostId, 'lost_id');

        if (!$data) {
            return '<div class="p-6 text-center text-gray-500 font-bold">No history available.</div>';
        }

        return view('staff.claims.partials.timeline', $data)->render();
    }

    private function getActorName(): string
    {
        return auth()->user()->name
            ?: (auth()->user()->username ?: 'Staff');
    }

    private function writeActionLog(string $actionType, string $targetName, string $details): void
    {
        AdminActionLog::create([
            'admin_name' => $this->getActorName(),
            'action_type' => $actionType,
            'target_name' => $targetName,
            'details' => $details,
        ]);
    }
}