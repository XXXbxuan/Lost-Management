@php
    $isScheduled = !is_null($match->appointment_at);
    $isConfirmed = (bool) $match->is_confirmed;
    $isClaimed   = ($foundItem->status === 'Claimed');

    // ✅ Reject / Reschedule 狀態（用 match 欄位，不靠 audit）
    $isRescheduleRequested = ($match->status === 'Reschedule Requested') || !is_null($match->rejected_at);

    $suggest1 = $match->suggested_time_1 ? \Carbon\Carbon::parse($match->suggested_time_1) : null;
    $suggest2 = $match->suggested_time_2 ? \Carbon\Carbon::parse($match->suggested_time_2) : null;

    // 顯示用（避免 500）
    $foundAt  = optional($foundItem->created_at);
    $matchAt  = optional($match->created_at);
@endphp

<div class="w-full py-10 bg-white rounded-[3rem]">
    <div class="flex items-start justify-between relative px-8">

        {{-- 背景進度灰線 --}}
        <div class="absolute top-5 left-16 right-16 h-1 bg-gray-200 -z-10"></div>

        {{-- Step 1: Item Found --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">✓</div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">Item Found</h3>
            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                <p class="font-bold">{{ $foundAt?->format('M d, h:i A') ?? 'N/A' }}</p>
                <p class="truncate w-24 mx-auto text-indigo-500 font-medium">Loc: {{ $foundItem->found_location ?? 'N/A' }}</p>
                <p class="text-indigo-600 font-black uppercase italic">{{ $foundItem->item_name ?? 'N/A' }}</p>
                <p class="text-indigo-400 font-bold">By: {{ $foundItem->registered_by_name ?? 'Staff' }}</p>
            </div>
        </div>

        {{-- Step 2: Matched --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">✓</div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">Matched</h3>
            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                <p class="font-bold">{{ $matchAt?->format('M d, h:i A') ?? 'N/A' }}</p>
                <p class="text-green-600 font-bold">Score: {{ $match->similarityScore ?? '0' }}%</p>
                <p class="text-indigo-400 font-bold italic">By: {{ $match->verifier?->name ?? 'Staff' }}</p>
            </div>
        </div>

        {{-- Step 3: Appointment / Reschedule Requested --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isScheduled ? 'bg-green-500' : ($isRescheduleRequested ? 'bg-red-500' : 'bg-gray-300') }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                {{ $isScheduled ? '✓' : '3' }}
            </div>

            <h3 class="mt-2 text-sm font-bold {{ $isScheduled ? 'text-gray-800' : ($isRescheduleRequested ? 'text-red-600' : 'text-gray-400') }}">
                {{ $isRescheduleRequested ? 'Reschedule' : 'Appointment' }}
            </h3>

            <div class="mt-1 text-[10px] text-center space-y-1">
                @if($isScheduled)
                    <p class="font-bold text-green-600">{{ \Carbon\Carbon::parse($match->appointment_at)->format('M d, h:i A') }}</p>
                    <p class="text-gray-500 italic">Loc: {{ $match->appointment_venue ?? 'Admin Office' }}</p>
                    <p class="text-indigo-400 font-bold">Sent By: {{ $match->verifier?->name ?? 'Staff' }}</p>

                @elseif($isRescheduleRequested)
                    {{-- ✅ 你要的：Step 3 顯示「打岔 + Reject」 --}}
                    <span class="inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded-md font-black text-[8px] uppercase tracking-widest">
                        REJECTED
                    </span>

                    @if($suggest1)
                        <p class="font-black text-slate-800">
                            S1: {{ $suggest1->format('M d, H:i') }}
                        </p>
                    @endif
                    @if($suggest2)
                        <p class="font-black text-slate-800">
                            S2: {{ $suggest2->format('M d, H:i') }}
                        </p>
                    @endif

                    @if(!$suggest1 && !$suggest2)
                        <p class="italic text-gray-400">Awaiting passenger time...</p>
                    @endif
                @else
                    <p class="italic text-gray-400">Awaiting schedule...</p>
                @endif
            </div>
        </div>

        {{-- Step 4: Confirmed --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isConfirmed ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                {{ $isConfirmed ? '✓' : '4' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isConfirmed ? 'text-gray-800' : 'text-gray-400' }}">Confirmed</h3>
            <div class="mt-1 text-[10px] text-center space-y-1">
                @if($isConfirmed)
                    <p class="font-bold text-green-600">{{ optional($match->confirmed_at)->format('M d, h:i A') ?? 'N/A' }}</p>
                    <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded-md font-black text-[8px] uppercase">Ready</span>
                @else
                    <p class="italic text-gray-400">Waiting for user...</p>
                @endif
            </div>
        </div>

        {{-- Step 5: Handover --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isClaimed ? 'bg-black' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                {{ $isClaimed ? '🏁' : '5' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isClaimed ? 'text-gray-800' : 'text-gray-400' }}">Handover</h3>
            <div class="mt-1 text-[10px] text-center space-y-1">
                @if($isClaimed)
                    <p class="text-green-700 font-black uppercase">Completed</p>
                @else
                    <p class="italic text-gray-400 font-bold uppercase text-[9px]">Awaiting Scan</p>
                @endif
            </div>
        </div>

    </div>
</div>