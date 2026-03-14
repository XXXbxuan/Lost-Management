<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    // 1. Show List & Create Form
    public function index()
    {
        $vouchers = Voucher::latest()->get();
        return view('staff.vouchers.index', compact('vouchers'));
    }

    // 2. Store New Voucher
    public function store(Request $request)
    {
        if ($request->input('points') < 0) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error: Voucher cost cannot be a negative number!');
        }

        return redirect()->route('staff.vouchers.index')
                         ->with('success', 'New reward voucher created successfully!');
    }

    // 3. Delete Voucher
    public function destroy($id)
    {
        Voucher::findOrFail($id)->delete();
        return redirect()->route('staff.vouchers.index')->with('success', 'Voucher deleted.');
    }
}