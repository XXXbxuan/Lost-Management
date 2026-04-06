<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\Redemption;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function createLostReport(): View
    {
        return view('passenger.lost_items.create');
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