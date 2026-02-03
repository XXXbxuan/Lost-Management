<div class="w-full py-6">
    <div class="flex items-start justify-between relative px-4">
        
        {{-- 背景灰线 --}}
        <div class="absolute top-5 left-10 right-10 h-1 bg-gray-200 -z-10"></div>

        {{-- Step 1: Item Found --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold shadow-md z-10 border-4 border-white">
                ✓
            </div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">Item Found</h3>
            <div class="mt-1 text-xs text-center text-gray-500 space-y-1">
                <p>{{ $foundItem->created_at->format('M d, h:i A') }}</p>
                <p>Loc: {{ $foundItem->found_location }}</p>
                <p class="text-indigo-600 font-semibold truncate w-24 text-center">{{ $foundItem->item_name }}</p>
            </div>
        </div>

        {{-- Step 2: Matched --}}
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold shadow-md z-10 border-4 border-white">
                ✓
            </div>
            <h3 class="mt-2 text-sm font-bold text-gray-800">Matched</h3>
            <div class="mt-1 text-xs text-center text-gray-500 space-y-1">
                <p>{{ $match->updated_at->format('M d, h:i A') }}</p>
                <p>Score: <span class="font-mono text-green-600">{{ $match->similarityScore ?? 0 }}%</span></p> 
                <p class="text-green-600 font-bold">Status: Verified</p>
            </div>
        </div>

        {{-- Step 3: Appointment (已修改为绿色) --}}
        @php
            $isScheduled = !is_null($match->appointment_at);
        @endphp
        <div class="flex flex-col items-center w-1/5 relative group">
            {{-- 背景色：改为 bg-green-500 --}}
            <div class="w-10 h-10 {{ $isScheduled ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white font-bold shadow-md z-10 border-4 border-white transition-colors">
                {{-- 图标：改为打勾 --}}
                {{ $isScheduled ? '✓' : '3' }}
            </div>
            {{-- 标题颜色 --}}
            <h3 class="mt-2 text-sm font-bold {{ $isScheduled ? 'text-gray-800' : 'text-gray-400' }}">Appointment</h3>
            <div class="mt-1 text-xs text-center text-gray-500 space-y-1">
                @if($isScheduled)
                    {{-- 字体颜色：改为绿色 --}}
                    <p class="font-bold text-green-600">{{ \Carbon\Carbon::parse($match->appointment_at)->format('M d, h:i A') }}</p>
                    <p>Loc: Admin Office</p>
                    <p>SMS: <span class="text-green-500">Sent</span></p>
                @else
                    <p class="italic text-gray-400">Not scheduled yet</p>
                @endif
            </div>
        </div>

        {{-- Step 4: Confirmed --}}
        @php
            $isConfirmed = $match->is_confirmed;
        @endphp
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isConfirmed ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white font-bold shadow-md z-10 border-4 border-white transition-colors">
                {{ $isConfirmed ? '✓' : '4' }}
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isConfirmed ? 'text-gray-800' : 'text-gray-400' }}">Confirmed</h3>
            <div class="mt-1 text-xs text-center text-gray-500 space-y-1">
                @if($isConfirmed)
                    <p class="font-bold text-green-600">{{ \Carbon\Carbon::parse($match->confirmed_at)->format('M d, h:i A') }}</p>
                    <p>By: User (Mobile)</p>
                    <p class="bg-green-100 text-green-800 px-1 rounded inline-block">Ready</p>
                @else
                    <p class="italic text-gray-400">Waiting for user...</p>
                @endif
            </div>
        </div>

        {{-- Step 5: Handover --}}
        @php
            $isClaimed = $foundItem->status === 'Claimed';
        @endphp
        <div class="flex flex-col items-center w-1/5 relative group">
            <div class="w-10 h-10 {{ $isClaimed ? 'bg-green-800' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white font-bold shadow-md z-10 border-4 border-white transition-colors">
                🏁
            </div>
            <h3 class="mt-2 text-sm font-bold {{ $isClaimed ? 'text-gray-800' : 'text-gray-400' }}">Handover</h3>
            <div class="mt-1 text-xs text-center text-gray-500 space-y-1">
                @if($isClaimed)
                    <p class="font-bold text-green-700">Completed</p>
                    <p>Check: IC Verified</p>
                @else
                    <p class="italic text-gray-400">Pending</p>
                @endif
            </div>
        </div>
    </div>
</div>