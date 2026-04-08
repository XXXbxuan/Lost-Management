<x-app-layout>
    @php
        $handoverLog = $auditLogs->where('action_type', 'ITEM_HANDOVER_SUCCESS')->first();
        $matchLog = $auditLogs->where('action_type', 'VERIFY_MATCH')->last();
        $foundLog = $auditLogs->where('action_type', 'REGISTER_FOUND_ITEM')->first();
        $scanLog = $auditLogs->where('action_type', 'QR_SCAN_SUCCESS')->first();

        $handoverDate = $match->claim->claimedAt ?? ($handoverLog ? $handoverLog->created_at : now());

        $qrStatus = ($scanLog || $match->claim) ? 'SCAN CONFIRMED' : 'PENDING SCAN';

        $claimDisplayNo = $match->claim ? ('CLM-' . $match->claim->id) : '-';
        $handoverNotes = filled($match->claim->handover_notes ?? null) ? $match->claim->handover_notes : '-';

        $foundStaffName = $match->foundItem->staff->name ?? '-';
        $foundStaffId = $match->foundItem->staff->staff_id ?? ($match->foundItem->staff_id ?? '-');

        $lostRegistryName = $match->lostItem->staff->name
            ?? $match->lostItem->passenger_name
            ?? '-';

        $lostRegistryId = $match->lostItem->staff->staff_id
            ?? ($match->lostItem->staff_id ?? null);

        $lostRegistryMeta = $match->lostItem->staff
            ? 'Staff ID: #' . $lostRegistryId
            : 'Passenger Submission';

        $verifyStaffName = $match->verifier->staff->name ?? $match->verifier->name ?? 'ADMIN';
        $verifyStaffId = $match->verifier->staff->staff_id ?? '-';

        $finalHandoverStaffName = $match->claim->processed_by_name
            ?? $match->claim->handler->staff->name
            ?? $match->claim->handler->name
            ?? '-';

        $finalHandoverStaffId = $match->claim->handler->staff->staff_id ?? '-';
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap');

        .boarding-pass {
            font-family: 'Space Mono', monospace;
            background: white;
            border: 3px solid #0f172a;
            border-radius: 2rem;
            position: relative;
            overflow: hidden;
            display: flex;
            min-height: 440px;
            margin-bottom: 2.5rem;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.1));
        }

        .pass-main {
            flex: 1;
            padding: 2.5rem 3rem;
            position: relative;
        }

        .perforation {
            width: 170px;
            border-left: 3px dashed #cbd5e1;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .perforation::before,
        .perforation::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 50px;
            background: #f1f5f9;
            border: 3px solid #0f172a;
            border-radius: 50%;
            left: -27px;
        }

        .perforation::before {
            top: -27px;
        }

        .perforation::after {
            bottom: -27px;
        }

        .staff-stamp {
            position: absolute;
            bottom: 2rem;
            right: 3rem;
            text-align: right;
            min-width: 250px;
            border-top: 1.5px solid #e2e8f0;
            padding-top: 0.8rem;
        }

        label {
            font-size: 11px !important;
            letter-spacing: 0.12em;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }

        @media print {
            nav,
            aside,
            footer,
            header,
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .boarding-pass {
                border: 3px solid #000 !important;
                break-inside: avoid;
            }
        }
    </style>

    <div class="min-h-screen bg-slate-100 py-12">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="no-print mb-10 flex items-end justify-between px-4">
                <h1 class="text-6xl font-black uppercase leading-none tracking-tighter text-slate-900">
                    RECEIPT
                </h1>

                <button
                    onclick="window.print()"
                    class="rounded-2xl bg-indigo-600 px-12 py-4 text-sm font-black uppercase tracking-widest text-white shadow-2xl"
                >
                    Print
                </button>
            </div>

            <div class="space-y-4">
                <div class="boarding-pass">
                    <div class="pass-main">
                        <p class="mb-10 text-[12px] font-black uppercase tracking-[0.5em] text-indigo-600">
                            01. Found Asset Registry
                        </p>

                        <div class="grid grid-cols-2 gap-x-12 gap-y-10">
                            <div>
                                <label>Asset Name</label>
                                <p class="text-3xl font-black uppercase leading-tight text-slate-900">
                                    {{ $match->foundItem->item_name }}
                                </p>
                            </div>

                            <div>
                                <label>Internal Storage ID</label>
                                <p class="text-xl font-black uppercase text-indigo-600">
                                    {{ $match->foundItem->storage_location ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="col-span-2 border-t border-slate-50 pt-6">
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label>Category</label>
                                        <p class="text-sm font-black uppercase text-slate-800">
                                            {{ $match->foundItem->category ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Color</label>
                                        <p class="text-sm font-black uppercase text-slate-800">
                                            {{ $match->foundItem->color ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Found Time</label>
                                        <p class="text-sm font-black uppercase text-slate-800">
                                            {{ optional($match->foundItem->found_time)->format('Y-m-d') ?? '-' }}
                                        </p>
                                        <p class="mt-1 text-[11px] font-bold uppercase text-slate-500">
                                            {{ optional($match->foundItem->found_time)->format('H:i') ?? '' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-3 gap-4">
                                    <div>
                                        <label>Brand</label>
                                        <p class="text-sm font-black uppercase text-slate-800">
                                            {{ $match->foundItem->brand ?: '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Serial Number</label>
                                        <p class="text-sm font-black uppercase text-slate-800">
                                            {{ $match->foundItem->serial_number ?: '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Location Found</label>
                                        <p class="text-sm font-black uppercase text-slate-800">
                                            {{ $match->foundItem->found_location ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                        Found Item Description
                                    </label>
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 text-sm italic leading-relaxed text-slate-600 shadow-inner">
                                        "{{ $match->foundItem->description ?: '-' }}"
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>

                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold uppercase text-slate-400">Action: Registry Registry</p>
                            <p class="mt-1 text-sm font-black uppercase leading-none text-slate-900">
                                {{ $foundStaffName }}
                            </p>
                            <p class="text-[11px] font-bold uppercase text-indigo-500">
                                Staff ID: #{{ $foundStaffId }}
                            </p>
                        </div>
                    </div>

                    <div class="perforation">
                        <p class="whitespace-nowrap text-[11px] font-black uppercase text-slate-300">
                            Asset Stub
                        </p>
                        <p class="text-2xl font-black italic text-slate-900">#F-{{ $match->foundId }}</p>
                        <p class="mt-2 text-[10px] font-bold text-slate-400">
                            {{ optional($match->foundItem->created_at)->format('H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="boarding-pass" style="min-height: 480px;">
                    <div class="pass-main">
                        <p class="mb-10 text-[12px] font-black uppercase tracking-[0.5em] text-indigo-600">
                            02. Associated Lost Report
                        </p>

                        <div class="mb-19.5 grid grid-cols-2 gap-x-12 gap-y-10">
                            <div class="mt-2 space-y-1">
                                <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                    Passenger Details (Contact)
                                </label>
                                <p class="text-2xl font-black uppercase leading-none text-slate-900">
                                    {{ $match->lostItem->passenger_name ?? '-' }}
                                </p>
                                <p class="text-[11px] font-bold italic leading-tight text-slate-500">
                                    {{ $match->lostItem->passenger_email ?? '-' }}
                                </p>
                                <p class="text-[11px] font-bold italic leading-tight text-slate-500">
                                    {{ $match->lostItem->passenger_phone ?? '-' }}
                                </p>
                            </div>

                            <div class="text-right">
                                <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                    Report Metadata
                                </label>
                                <p class="mt-2 text-sm font-black uppercase text-slate-900">
                                    Lost At: {{ $match->lostItem->lost_location }}
                                </p>
                                <p class="mt-1 text-[10px] font-bold uppercase text-slate-500">
                                    Flight: {{ $match->lostItem->flight_number ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="col-span-2 border-t border-slate-50 pt-5">
                                <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                    Lost Item Details
                                </label>

                                <p class="mt-3 text-3xl font-black uppercase leading-tight text-slate-900">
                                    {{ $match->lostItem->item_name ?? '-' }}
                                </p>

                                <div class="mt-4 grid grid-cols-3 gap-x-12 gap-y-6">
                                    <div class="space-y-3">
                                        <div>
                                            <label>Category</label>
                                            <p class="text-sm font-black uppercase text-slate-800">
                                                {{ $match->lostItem->category ?? '-' }}
                                            </p>
                                        </div>

                                        <div>
                                            <label>Brand</label>
                                            <p class="text-sm font-black uppercase text-slate-800">
                                                {{ $match->lostItem->brand ?: '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <label>Color</label>
                                            <p class="text-sm font-black uppercase text-slate-800">
                                                {{ $match->lostItem->color ?? '-' }}
                                            </p>
                                        </div>

                                        <div>
                                            <label>Serial Number</label>
                                            <p class="text-sm font-black uppercase text-slate-800">
                                                {{ $match->lostItem->serial_number ?: '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <label>Lost Time</label>
                                            <p class="text-sm font-black uppercase text-slate-800">
                                                {{ optional($match->lostItem->lost_time)->format('Y-m-d') ?? '-' }}
                                            </p>
                                            <p class="mt-1 text-[11px] font-bold uppercase text-slate-500">
                                                {{ optional($match->lostItem->lost_time)->format('H:i') ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                        Original Statement / Description
                                    </label>
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 text-sm italic leading-relaxed text-slate-600 shadow-inner">
                                        "{{ $match->lostItem->description ?: '-' }}"
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>
                        <br>
                        <br>

                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                Action: Assisted Registry
                            </p>
                            <p class="mt-1 text-base font-black uppercase leading-none text-slate-900">
                                {{ $lostRegistryName }}
                            </p>
                            <p class="text-[11px] font-bold uppercase text-indigo-500">
                                {{ $lostRegistryMeta }}
                            </p>
                        </div>
                    </div>

                    <div class="perforation flex flex-col items-center justify-center p-4 text-center">
                        <p class="mb-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">
                            Report Stub
                        </p>

                        <p class="text-2xl font-black italic leading-none text-slate-900">
                            #L-{{ $match->lostId }}
                        </p>

                        <p class="mt-3 font-mono text-[10px] font-bold text-slate-400">
                            {{ optional($match->lostItem->created_at)->format('H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="boarding-pass">
                    <div class="pass-main">
                        <p class="mb-10 text-[12px] font-black uppercase tracking-[0.5em] text-indigo-600">
                            03. Verification Protocol
                        </p>

                        <div class="mb-20 grid grid-cols-2 gap-x-16 gap-y-10">
                            <div class="space-y-8">
                                <div>
                                    <label>Appointment Venue</label>
                                    <p class="text-xl font-black uppercase leading-tight text-slate-900">
                                        {{ $match->appointment_venue ?? 'LOST & FOUND CENTRE (ADMIN OFFICE)' }}
                                    </p>
                                </div>

                                <div>
                                    <label>Scheduled Pickup Time</label>
                                    <p class="text-base font-black uppercase text-slate-800">
                                        {{ optional($match->appointment_at)->format('M d, Y / H:i A') ?? 'MAR 07, 2026 / 22:45 PM' }}
                                    </p>
                                </div>

                                <div class="mt-8">
                                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                        Verification Notes
                                    </label>
                                    <div class="w-full max-w-[520px] rounded-2xl border border-slate-100 bg-slate-50 p-5 text-sm italic leading-relaxed text-slate-600 shadow-inner">
                                        "{{ $match->notes ?? '-' }}"
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col justify-between text-right">
                                <div class="space-y-6">
                                    <div class="flex flex-col items-end">
                                        <label>Security Auth Status</label>
                                        <div class="mt-2 inline-block rounded-xl bg-slate-900 px-6 py-2">
                                            <p class="m-0 text-[12px] font-black uppercase tracking-widest text-emerald-500">
                                                {{ $qrStatus }}
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label>Similarity Score</label>
                                        <p class="text-4xl font-black text-indigo-600">
                                            {{ $match->similarityScore ?? '70' }}% MATCH
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-8 space-y-2 border-t border-slate-100 pt-6">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold uppercase tracking-tighter text-slate-400">Confirm Appt:</span>
                                        <span class="font-black text-slate-900">
                                            {{ optional($match->confirmed_at)->format('Y-m-d H:i') ?? 'N/A' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold uppercase tracking-tighter text-slate-400">Scan QR Time:</span>
                                        <span class="font-black text-slate-900">
                                            {{ optional($match->verifiedAt)->format('Y-m-d H:i') ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                Action: Match Verification
                            </p>
                            <p class="mt-1 text-base font-black uppercase leading-none text-slate-900">
                                {{ $verifyStaffName }}
                            </p>
                            <p class="text-[11px] font-bold uppercase text-indigo-500">
                                Staff ID: #{{ $verifyStaffId }}
                            </p>
                        </div>
                    </div>

                    <div class="perforation">
                        <p class="whitespace-nowrap text-[10px] font-black uppercase text-slate-300">
                            Verification Stub
                        </p>
                        <p class="text-2xl font-black italic text-slate-900">#M-{{ $match->id }}</p>
                        <p class="mt-2 text-[10px] font-bold text-slate-400">
                            {{ optional($match->verifiedAt)->format('H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="boarding-pass">
                    <div class="pass-main">
                        <p class="mb-10 text-[12px] font-black uppercase tracking-[0.5em] text-red-600">
                            04. Final Physical Handover
                        </p>

                        <div class="mb-20 grid grid-cols-2 gap-x-12 gap-y-8">
                            <div class="space-y-10">
                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                        Recipient Endorsement
                                    </label>
                                    <p class="mt-2 text-3xl font-black text-slate-900 underline decoration-4 decoration-indigo-200 underline-offset-8">
                                        {{ $match->claim->claimerName ?? 'CHIA BING XUAN' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                        Verified ID Reference
                                    </label>
                                    <p class="mt-1 text-base font-black uppercase tracking-widest text-red-600">
                                        {{ $match->claim->claimerIcPassport ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                        Transfer Completion Time
                                    </label>
                                    <p class="text-sm font-black uppercase italic text-slate-900">
                                        {{ $handoverDate->format('Y-m-d / H:i:s') }}
                                    </p>
                                </div>

                                <div class="mt-8">
                                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                        Handover Notes
                                    </label>
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 text-sm italic leading-relaxed text-slate-600 shadow-inner">
                                        "{{ $handoverNotes }}"
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-end justify-start pr-2 text-right">
                                <label class="mb-6 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                    Physical Evidence
                                </label>

                                @if ($match->claim && $match->claim->handover_photo)
                                    <a
                                        href="{{ asset('storage/' . $match->claim->handover_photo) }}"
                                        target="_blank"
                                        class="group flex w-full max-w-[200px] items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-slate-600 shadow-sm transition-all hover:border-slate-400 hover:bg-slate-100"
                                    >
                                        <svg class="h-5 w-5 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest">View Photo Proof</span>
                                    </a>
                                @else
                                    <div class="flex w-full max-w-[200px] items-center justify-center rounded-2xl border-2 border-dotted border-slate-200 bg-slate-50 px-4 py-4">
                                        <span class="text-[10px] font-bold uppercase italic text-slate-300">No Attachment</span>
                                    </div>
                                @endif

                                <p class="mt-6 text-[9px] font-bold italic text-slate-400">
                                    Digitally witnessed & confirmed.
                                </p>
                            </div>
                        </div>

                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                Action: Witness Final Handover
                            </p>
                            <p class="mt-1 text-base font-black uppercase leading-none text-slate-900">
                                {{ $finalHandoverStaffName }}
                            </p>
                            <p class="text-[11px] font-bold uppercase text-indigo-500">
                                Staff ID: #{{ $finalHandoverStaffId }}
                            </p>
                        </div>
                    </div>

                    <div class="perforation">
                        <p class="whitespace-nowrap text-[10px] font-black uppercase text-slate-300">
                            Release Stub
                        </p>
                        <p class="text-2xl font-black italic text-slate-900">{{ $claimDisplayNo }}</p>
                        <p class="mt-2 text-[10px] font-bold text-slate-400">
                            {{ $handoverDate->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>