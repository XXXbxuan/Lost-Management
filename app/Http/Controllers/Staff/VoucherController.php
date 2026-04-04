<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\AdminActionLog;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
            'description' => 'required|string',
        ]);

        if ((int) $validated['points'] < 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: Voucher cost cannot be a negative number!');
        }

        $voucher = Voucher::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'points' => $validated['points'],
            'description' => $validated['description'],
        ]);

        AdminActionLog::create([
            'admin_name' => auth()->user()->name ?: (auth()->user()->username ?: 'Staff'),
            'action_type' => 'CREATE_VOUCHER',
            'target_name' => "Voucher #{$voucher->id}",
            'details' => "Created voucher: {$voucher->name}, Category: {$voucher->category}, Points: {$voucher->points}.",
        ]);

        return redirect()->route('staff.vouchers.index')
            ->with('success', 'New reward voucher created successfully!');
    }

    // 3. Delete Voucher
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);

        AdminActionLog::create([
            'admin_name' => auth()->user()->name ?: (auth()->user()->username ?: 'Staff'),
            'action_type' => 'DELETE_VOUCHER',
            'target_name' => "Voucher #{$voucher->id}",
            'details' => "Deleted voucher: {$voucher->name}, Category: {$voucher->category}, Points: {$voucher->points}.",
        ]);

        $voucher->delete();

        return redirect()->route('staff.vouchers.index')->with('success', 'Voucher deleted.');
    }
}