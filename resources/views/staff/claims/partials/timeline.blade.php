@php
    use Illuminate\Support\Str;

    // 1. 🌟 證據鏈抓取：從全量 Audit Logs 中鎖定關鍵動作
    $foundLog    = $auditLogs->where('action_type', 'REGISTER_FOUND_ITEM')->first();
    $matchLog    = $auditLogs->where('action_type', 'VERIFY_MATCH')->last();
    
    // 預約日誌：優先抓取「重新預約」，若無則抓取「首次預約」
    $apptLog     = $auditLogs->where('action_type', 'RESCHEDULE_APPOINTMENT')->last() 
                   ?? $auditLogs->where('action_type', 'SEND_APPOINTMENT')->first();
    
    $confirmLog  = $auditLogs->where('action_type', 'PASSENGER_CONFIRM')->first();
    
    // 🌟 新增：現場掃碼嘗試證據 (只拿最後一次)
    $scanLog     = $auditLogs->where('action_type', 'SCAN_QR_ATTEMPT')->last();
    
    $handoverLog = $auditLogs->where('action_type', 'ITEM_HANDOVER_SUCCESS')->first();
    
    // 2. 🌟 數據拆解：從日誌 details 裡提取關鍵資訊
    $parsedFoundLoc = $foundLog ? (Str::between($foundLog->details, 'Location: ', ',') ?: $foundItem->found_location) : $foundItem->found_location;
    $parsedScore    = $matchLog ? (Str::between($matchLog->details, 'Score: ', '%') ?: $match->similarityScore) : $match->similarityScore;
    $parsedVenue    = $apptLog ? (Str::between($apptLog->details, 'Venue: [', ']') ?: 'Admin Office') : 'Admin Office';
    $parsedIC       = $handoverLog ? (Str::after($handoverLog->details, 'IC: ') ?: 'Verified') : 'Verified';

    // 3. 狀態判斷
    $isScheduled = !is_null($match->appointment_at);
    $isConfirmed = (bool)$match->is_confirmed;
    $isClaimed   = ($foundItem->status === 'Claimed');
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
                <p class="font-bold">{{ $foundItem->created_at->format('M d, h:i A') }}</p>
                <p class="truncate w-24 mx-auto text-indigo-500 font-medium">Loc: {{ $parsedFoundLoc }}</p>
                <p class="text-indigo-600 font-black uppercase italic">{{ $foundItem->item_name }}</p>
                <p class="text-indigo-400 font-bold">By: {{ $foundLog->admin_name ?? 'Staff' }}</p>
            </div>
        </div>

        {{-- Step 2: Matched --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">✓</div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">Matched</h3>
            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                <p class="font-bold">{{ $match->created_at->format('M d, h:i A') }}</p>
                <p class="text-green-600 font-bold">Score: {{ $parsedScore }}%</p>
                <p class="text-indigo-400 font-bold italic">By: {{ $matchLog->admin_name ?? 'Admin' }}</p>
            </div>
        </div>

        {{-- Step 3: Appointment (連動 SEND / RESCHEDULE 日誌) --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isScheduled ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                {{ $isScheduled ? '✓' : '3' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isScheduled ? 'text-gray-800' : 'text-gray-400' }}">
                {{ $apptLog && $apptLog->action_type === 'RESCHEDULE_APPOINTMENT' ? 'Rescheduled' : 'Appointment' }}
            </h3>
            <div class="mt-1 text-[10px] text-center space-y-1">
                @if($isScheduled)
                    <p class="font-bold text-green-600">{{ \Carbon\Carbon::parse($match->appointment_at)->format('M d, h:i A') }}</p>
                    <p class="text-gray-500 italic">Loc: {{ $parsedVenue }}</p>
                    <p class="text-indigo-400 font-bold">Sent By: {{ $apptLog->admin_name ?? 'Staff' }}</p>
                @else
                    <p class="italic text-gray-400">Awaiting schedule...</p>
                @endif
            </div>
        </div>

        {{-- Step 4: Confirmed (連動 PASSENGER_CONFIRM + SCAN 證據) --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isConfirmed ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                {{ $isConfirmed ? '✓' : '4' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isConfirmed ? 'text-gray-800' : 'text-gray-400' }}">Confirmed</h3>
            <div class="mt-1 text-[10px] text-center space-y-1">
                @if($isConfirmed)
                    <p class="font-bold text-green-600">{{ \Carbon\Carbon::parse($match->confirmed_at)->format('M d, h:i A') }}</p>
                    <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded-md font-black text-[8px] uppercase">Ready</span>
                    
                    {{-- 🌟 現場掃碼證據區塊 --}}
                    @if($scanLog)
                        <div class="mt-2 p-1.5 bg-indigo-50 border border-indigo-200 rounded-lg animate-pulse">
                            <p class="text-indigo-700 font-black text-[7px] uppercase tracking-tighter">⚡ Scan Detected</p>
                            <p class="text-indigo-400 text-[7px] font-bold">By: {{ $scanLog->admin_name }}</p>
                        </div>
                    @endif
                @else
                    <p class="italic text-gray-400">Waiting for user...</p>
                @endif
            </div>
        </div>

        {{-- Step 5: Handover (來自 ITEM_HANDOVER_SUCCESS) --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isClaimed ? 'bg-black' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                {{ $isClaimed ? '🏁' : '5' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isClaimed ? 'text-gray-800' : 'text-gray-400' }}">Handover</h3>
            <div class="mt-1 text-[10px] text-center space-y-1">
                @if($isClaimed)
                    <p class="text-green-700 font-black uppercase">Completed</p>
                    <p class="text-gray-500 font-bold">ID: {{ $parsedIC }}</p>
                    <p class="text-slate-900 font-black text-[9px] uppercase mt-1 italic underline">Lead: {{ $handoverLog->admin_name ?? 'Admin' }}</p>
                @else
                    <p class="italic text-gray-400 font-bold uppercase text-[9px]">Awaiting Scan</p>
                @endif
            </div>
        </div>

    </div>
</div>