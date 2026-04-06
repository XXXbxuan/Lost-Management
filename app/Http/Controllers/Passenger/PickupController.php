<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\Passenger\SubmitRescheduleProposalRequest;
use App\Mail\PickupPassMail;
use App\Models\MatchRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class PickupController extends Controller
{
    public function showPickupConfirmation(string $token): RedirectResponse|View
    {
        $match = MatchRecord::where('verification_token', $token)
            ->with(['foundItem', 'lostItem'])
            ->firstOrFail();

        if ($match->is_confirmed) {
            return redirect()->route('pickup.verify', ['token' => $token]);
        }

        return view('passenger.claims.pickup_confirmation', compact('match'));
    }

    public function confirmPickup(string $token): RedirectResponse
    {
        $match = MatchRecord::where('verification_token', $token)
            ->with(['foundItem', 'lostItem'])
            ->firstOrFail();

        if (!in_array($match->status, ['Verified', 'Matched'])) {
            return back()->with('error', 'This record is not ready for confirmation. Please contact airport staff.');
        }

        try {
            $match->update([
                'is_confirmed' => true,
                'confirmed_at' => now(),
                'status' => 'Confirmed',
            ]);

            $verifyLink = route('pickup.verify', ['token' => $token]);

            $qrRaw = (string) QrCode::format('png')
                ->size(400)
                ->margin(1)
                ->color(15, 23, 42)
                ->generate($verifyLink);

            $passengerEmail = $match->lostItem->passenger_email ?? 'chiabx-wp22@student.tarc.edu.my';

            Mail::to($passengerEmail)->send(new PickupPassMail($match, $qrRaw));

            return redirect()
                ->route('pickup.verify', ['token' => $token])
                ->with('success', 'Thank you! Your pickup has been confirmed and a digital pass was sent to your email.');
        } catch (Throwable $e) {
            Log::error('Pickup confirmation error: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while sending your pass. Please try again or check your history.');
        }
    }

    public function showAppointmentRejection(string $token): RedirectResponse|View
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        if ($match->is_confirmed) {
            return redirect()
                ->route('pickup.verify', ['token' => $token])
                ->with('info', 'This appointment is already confirmed. You can view your pickup pass below.');
        }

        return view('passenger.claims.appointment_rejection', compact('match'));
    }

    public function submitRescheduleRequest(SubmitRescheduleProposalRequest $request, string $token): View|RedirectResponse
    {
        $validated = $request->validated();

        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        if ($match->is_confirmed) {
            return redirect()
                ->route('pickup.verify', ['token' => $token])
                ->with('error', 'Confirmed appointments cannot be modified.');
        }

        $match->update([
            'appointment_at' => null,
            'appointment_venue' => null,
            'status' => 'Reschedule Requested',
            'is_confirmed' => false,
            'suggested_time_1' => $validated['suggested_time_1'],
            'suggested_time_2' => $validated['suggested_time_2'] ?? null,
            'suggested_remarks' => $validated['suggested_remarks'] ?? null,
            'rejected_at' => now(),
            'verification_token' => Str::random(40),
        ]);

        return view('passenger.claims.appointment_rejection', [
            'match' => $match,
            'success' => true,
        ]);
    }
}