<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FoundItem;
use App\Models\Voucher;     // 👈 Import Voucher Model
use App\Models\Redemption;  // 👈 Import Redemption Model
use App\Models\User;        // 👈 Import User Model

class DashboardController extends Controller
{
    // ... (Keep your showReportForm and showFoundItems functions same as before) ...

    public function showReportForm()
    {
        return view('passenger.lost-items.create');
    }

    public function showFoundItems()
    {
        $foundItems = FoundItem::where('status', 'Found')->latest()->paginate(10);
        return view('passenger.found-items.index', compact('foundItems'));
    }

    // 🟢 UPDATED: Show Real Rewards Page
    public function showRewards()
    {
        $vouchers = Voucher::all(); // Get vouchers from DB
        
        // Get user's history
        $myRedemptions = Redemption::where('user_id', Auth::id())
                                   ->with('voucher')
                                   ->latest()
                                   ->get();

        return view('passenger.rewards', compact('vouchers', 'myRedemptions'));
    }

    // 🟢 NEW: Process the Redemption
    public function redeemVoucher(Request $request, $id)
    {
        $user = Auth::user();
        $voucher = Voucher::findOrFail($id);

        // 1. Check if user has enough points
        if ($user->points < $voucher->points) {
            return redirect()->back()->with('error', 'Not enough points to redeem this voucher!');
        }

        // 2. Deduct points
        $user->points = $user->points - $voucher->points;
        $user->save(); // Save new point balance to DB

        // 3. Create Record
        Redemption::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
        ]);

        return redirect()->back()->with('success', 'Redemption Successful! You spent ' . $voucher->points . ' points.');
    }
}