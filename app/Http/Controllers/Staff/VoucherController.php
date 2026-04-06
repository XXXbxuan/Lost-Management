<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreVoucherRequest;
use App\Models\AdminActionLog;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::latest()->get();

        return view('staff.vouchers.index', compact('vouchers'));
    }

    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $voucher = Voucher::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'points' => $validated['points'],
            'description' => $validated['description'],
        ]);

        $this->writeActionLog(
            'CREATE_VOUCHER',
            "Voucher #{$voucher->id}",
            "Created voucher: {$voucher->name}, Category: {$voucher->category}, Points: {$voucher->points}."
        );

        return redirect()
            ->route('staff.vouchers.index')
            ->with('success', 'New reward voucher created successfully.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $this->writeActionLog(
            'DELETE_VOUCHER',
            "Voucher #{$voucher->id}",
            "Deleted voucher: {$voucher->name}, Category: {$voucher->category}, Points: {$voucher->points}."
        );

        $voucher->delete();

        return redirect()
            ->route('staff.vouchers.index')
            ->with('success', 'Voucher deleted.');
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