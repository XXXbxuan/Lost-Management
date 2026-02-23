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
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'points' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        Voucher::create($request->all());

        return redirect()->route('staff.vouchers.index')->with('success', 'Voucher created successfully!');
    }

    // 3. Delete Voucher
    public function destroy($id)
    {
        Voucher::findOrFail($id)->delete();
        return redirect()->route('staff.vouchers.index')->with('success', 'Voucher deleted.');
    }
}