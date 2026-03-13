<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FoundItem;
use App\Models\Voucher;     // 👈 Import Voucher Model
use App\Models\Redemption;  // 👈 Import Redemption Model
use App\Models\User;        // 👈 Import User Model
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //Show the "Report Lost Item" Form
    public function showReportForm()
    {
        return view('passenger.lost-items.create');
    }

    //Show the "Browse Found Items" Page
    public function showFoundItems()
    {
        $foundItems = FoundItem::latest()->paginate(10);

        return view('passenger.found-items.index', compact('foundItems'));
    }

    //Show Real Rewards Page
    public function showRewards()
    {
        $vouchers = Voucher::all();
        
        $myRedemptions = Redemption::where('user_id', Auth::id())
                                   ->with('voucher')
                                   ->latest()
                                   ->get();

        return view('passenger.rewards', compact('vouchers', 'myRedemptions'));
    }

    public function redeemVoucher(Request $request, $id)
    {
        $user = Auth::user();
        $voucher = Voucher::findOrFail($id);

        if ($user->points < $voucher->points) {
            return redirect()->back()->with('error', 'Not enough points to redeem this voucher!');
        }

        $user->points = $user->points - $voucher->points;
        $user->save();

        Redemption::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
        ]);

        return redirect()->back()->with('success', 'Redemption Successful! You spent ' . $voucher->points . ' points.');
    }

    public function useVoucher($id)
    {
        $redemption = Redemption::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $redemption->update(['status' => 'Used']); 

        return back()->with('success', 'Voucher applied successfully! Enjoy your reward.');
    }
}