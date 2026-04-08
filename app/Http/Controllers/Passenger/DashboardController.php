<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\LostItemReport;
use App\Models\Redemption;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function createLostReport(): View
    {
        return view('passenger.lost_items.index');
    }

    public function storeLostReport(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'passenger_name' => ['required', 'string', 'max:255'],
            'passenger_email' => ['required', 'email', 'max:255'],
            'passenger_phone' => ['required', 'string', 'max:30'],

            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:255'],
            'sub_colors' => ['nullable', 'array'],
            'sub_colors.*' => ['string', 'max:50'],
            'image' => ['nullable', 'image', 'max:2048'],

            'lost_location' => ['required', 'string', 'max:255'],
            'flight_number' => ['nullable', 'string', 'max:50'],
            'lost_time' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        if (($validated['color'] ?? '') === 'Multi-color') {
            $subColors = $request->input('sub_colors', []);

            if (empty($subColors)) {
                return back()
                    ->withErrors(['color' => 'Please select at least one sub-color for Multi-color.'])
                    ->withInput();
            }

            $validated['color'] = 'Multi-color (' . implode(', ', $subColors) . ')';
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('lost_reports', 'public');
        }

        LostItemReport::create([
            'staff_id' => null,
            'passenger_name' => $validated['passenger_name'],
            'passenger_email' => $validated['passenger_email'],
            'passenger_phone' => $validated['passenger_phone'],
            'item_name' => $validated['item_name'],
            'category' => $validated['category'],
            'brand' => $validated['brand'] ?? null,
            'color' => $validated['color'],
            'serial_number' => $validated['serial_number'] ?? null,
            'image_path' => $imagePath,
            'lost_location' => $validated['lost_location'],
            'flight_number' => $validated['flight_number'] ?? null,
            'lost_time' => $validated['lost_time'],
            'description' => $validated['description'] ?? null,
            'status' => 'LOST',
        ]);

        return redirect()
            ->route('passenger.report')
            ->with('success', 'Lost report submitted successfully.');
    }

    public function browseFoundItems(): View
    {
        $foundItems = FoundItem::latest()->paginate(10);

        return view('passenger.found_items.index', compact('foundItems'));
    }

    public function showRewardsCenter(): View
    {
        $vouchers = Voucher::all();

        $myRedemptions = Redemption::where('user_id', Auth::id())
            ->with('voucher')
            ->latest()
            ->get();

        return view('passenger.rewards', compact('vouchers', 'myRedemptions'));
    }

    public function redeemVoucher(int $id): RedirectResponse
    {
        $voucher = Voucher::findOrFail($id);

        return DB::transaction(function () use ($voucher) {
            $user = User::findOrFail(Auth::id());

            if ($user->points < $voucher->points) {
                return back()->with('error', 'Not enough points to redeem this voucher!');
            }

            $user->points = $user->points - $voucher->points;
            $user->save();

            Redemption::create([
                'user_id' => $user->id,
                'voucher_id' => $voucher->id,
            ]);

            return back()->with('success', 'Redemption successful! You spent ' . $voucher->points . ' points.');
        });
    }

    public function markVoucherAsUsed(int $id): RedirectResponse
    {
        $redemption = Redemption::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($redemption->status === 'Used') {
            return back()->with('error', 'This voucher has already been used.');
        }

        $redemption->update([
            'status' => 'Used',
        ]);

        return back()->with('success', 'Voucher applied successfully! Enjoy your reward.');
    }
}