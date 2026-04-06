@php
    $mode = $mode ?? 'claim';

    $isScheduled = !is_null($match->appointment_at);
    $isConfirmed = (bool) $match->is_confirmed;
    $isClaimed = $foundItem->status === 'Claimed';

    $isRescheduleRequested = ($match->status === 'Reschedule Requested') || !is_null($match->rejected_at);

    $suggest1 = $match->suggested_time_1 ? \Carbon\Carbon::parse($match->suggested_time_1) : null;
    $suggest2 = $match->suggested_time_2 ? \Carbon\Carbon::parse($match->suggested_time_2) : null;

    $foundAt = optional($foundItem->created_at);
    $matchAt = optional($match->created_at);

    $lostItem = $match->lostItem ?? null;

    if ($mode === 'found_item') {
        $step1Title = 'Item Found';
        $step1Time = $foundAt?->format('M d, h:i A') ?? 'N/A';
        $step1Location = $foundItem->found_location ?? 'N/A';
        $step1Name = $foundItem->item_name ?? 'N/A';
        $step1By = $foundItem->registered_by_name ?? 'Staff';
        $step1LocationLabel = 'Loc';
    } else {
        $step1Title = 'Report';
        $step1Time = optional($lostItem?->created_at)->format('M d, h:i A') ?? 'N/A';
        $step1Location = $lostItem->lost_location ?? 'N/A';
        $step1Name = $lostItem->item_name ?? 'N/A';

        $reportStaffName = null;

        if (!empty($lostItem?->staff_id)) {
            $reportStaff = \App\Models\Staff::where('staff_id', $lostItem->staff_id)->first();
            $reportStaffName = $reportStaff?->name;
        }

        $step1By = $reportStaffName ?? 'Staff';
        $step1LocationLabel = 'Lost';
    }
@endphp

<div class="w-full rounded-[3rem] bg-white py-10">
    <div class="relative flex items-start justify-between px-8">
        <div class="absolute left-16 right-16 top-5 -z-10 h-1 bg-gray-200"></div>

        <div class="group relative flex w-1/5 flex-col items-center">
            <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-green-500 text-xl font-bold text-white shadow-md">
                ✓
            </div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">{{ $step1Title }}</h3>
            <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                <p class="font-bold">{{ $step1Time }}</p>
                <p class="mx-auto w-24 truncate font-medium text-indigo-500">
                    {{ $step1LocationLabel }}: {{ $step1Location }}
                </p>
                <p class="break-words text-indigo-600 font-black uppercase italic">
                    {{ $step1Name }}
                </p>
                <p class="font-bold text-indigo-400">By: {{ $step1By }}</p>
            </div>
        </div>

        <div class="group relative flex w-1/5 flex-col items-center">
            <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-green-500 text-xl font-bold text-white shadow-md">
                ✓
            </div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">Matched</h3>
            <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                <p class="font-bold">{{ $matchAt?->format('M d, h:i A') ?? 'N/A' }}</p>
                <p class="font-bold text-green-600">Score: {{ $match->similarityScore ?? '0' }}%</p>
                <p class="font-bold italic text-indigo-400">By: {{ $match->verifier?->name ?? 'Staff' }}</p>
            </div>
        </div>

        <div class="group relative flex w-1/5 flex-col items-center">
            <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white text-xl font-bold text-white shadow-md {{ $isScheduled ? 'bg-green-500' : ($isRescheduleRequested ? 'bg-red-500' : 'bg-gray-300') }}">
                {{ $isScheduled ? '✓' : '3' }}
            </div>

            <h3 class="mt-2 text-sm font-bold {{ $isScheduled ? 'text-gray-800' : ($isRescheduleRequested ? 'text-red-600' : 'text-gray-400') }}">
                {{ $isRescheduleRequested ? 'Reschedule' : 'Appointment' }}
            </h3>

            <div class="mt-1 space-y-1 text-center text-[10px]">
                @if ($isScheduled)
                    <p class="font-bold text-green-600">
                        {{ \Carbon\Carbon::parse($match->appointment_at)->format('M d, h:i A') }}
                    </p>
                    <p class="italic text-gray-500">
                        Loc: {{ $match->appointment_venue ?? 'Admin Office' }}
                    </p>
                    <p class="font-bold text-indigo-400">
                        Sent By: {{ $match->verifier?->name ?? 'Staff' }}
                    </p>
                @elseif ($isRescheduleRequested)
                    <span class="inline-block rounded-md bg-red-100 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-red-700">
                        REJECTED
                    </span>

                    @if ($suggest1)
                        <p class="font-black text-slate-800">
                            S1: {{ $suggest1->format('M d, H:i') }}
                        </p>
                    @endif

                    @if ($suggest2)
                        <p class="font-black text-slate-800">
                            S2: {{ $suggest2->format('M d, H:i') }}
                        </p>
                    @endif

                    @if (!$suggest1 && !$suggest2)
                        <p class="italic text-gray-400">Awaiting passenger time...</p>
                    @endif
                @else
                    <p class="italic text-gray-400">Awaiting schedule...</p>
                @endif
            </div>
        </div>

        <div class="group relative flex w-1/5 flex-col items-center">
            <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white text-xl font-bold text-white shadow-md {{ $isConfirmed ? 'bg-green-500' : 'bg-gray-300' }}">
                {{ $isConfirmed ? '✓' : '4' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isConfirmed ? 'text-gray-800' : 'text-gray-400' }}">
                Confirmed
            </h3>
            <div class="mt-1 space-y-1 text-center text-[10px]">
                @if ($isConfirmed)
                    <p class="font-bold text-green-600">
                        {{ optional($match->confirmed_at)->format('M d, h:i A') ?? 'N/A' }}
                    </p>
                    <span class="inline-block rounded-md bg-green-100 px-2 py-0.5 text-[8px] font-black uppercase text-green-700">
                        Ready
                    </span>
                @else
                    <p class="italic text-gray-400">Waiting for user...</p>
                @endif
            </div>
        </div>

        <div class="group relative flex w-1/5 flex-col items-center">
            <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white text-xl font-bold text-white shadow-md {{ $isClaimed ? 'bg-black' : 'bg-gray-300' }}">
                {{ $isClaimed ? '🏁' : '5' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isClaimed ? 'text-gray-800' : 'text-gray-400' }}">
                Handover
            </h3>
            <div class="mt-1 space-y-1 text-center text-[10px]">
                @if ($isClaimed)
                    <p class="font-black uppercase text-green-700">Completed</p>
                @else
                    <p class="text-[9px] font-bold uppercase italic text-gray-400">Awaiting Scan</p>
                @endif
            </div>
        </div>
    </div>
</div>