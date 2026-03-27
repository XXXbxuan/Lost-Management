@php
    $status = $item->status ?? 'Unclaimed';

    $latestRemove = null;
    $latestRemoveWithLocation = null;
    $removedFrom = null;
    $removeReason = null;
    $removedBy = null;

    if ($status === 'Removed') {
        $latestRemove = \App\Models\InventoryMovement::where('found_item_id', $item->id)
            ->where('action_type', 'remove')
            ->latest('created_at')
            ->first();

        $latestRemoveWithLocation = \App\Models\InventoryMovement::where('found_item_id', $item->id)
            ->where('action_type', 'remove')
            ->whereNotNull('from_location')
            ->where('from_location', '!=', '')
            ->latest('created_at')
            ->first();

        $removedFrom = $latestRemoveWithLocation?->from_location
            ?? $latestRemove?->to_location
            ?? $item->storage_location
            ?? null;

        $removeReason = $latestRemove?->remarks ?? $item->removal_reason;

        $performedById = $latestRemove?->performed_by;
        if ($performedById) {
            $removedByUser = \App\Models\User::find($performedById);
            $removedBy = $removedByUser?->username ?? $removedByUser?->name ?? ('User #' . $performedById);
        }
    }
@endphp

@if($status === 'Removed')
    <div class="w-full py-10 bg-white rounded-[3rem] border border-slate-200 mb-6">
        <div class="flex items-start justify-center relative px-8">

            <div class="absolute top-5 left-[25%] right-[25%] h-1 bg-gray-200 -z-10"></div>

            <div class="flex flex-col items-center w-1/2 relative">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                    ✓
                </div>
                <h3 class="mt-2 text-sm font-bold text-gray-800">Item Found</h3>
                <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                    <p class="font-bold">{{ optional($item->created_at)->format('M d, h:i A') ?? 'N/A' }}</p>
                    <p class="truncate w-28 mx-auto text-indigo-500 font-medium">
                        Loc: {{ $item->found_location ?? 'N/A' }}
                    </p>
                    <p class="text-indigo-600 font-black uppercase italic break-words">
                        {{ $item->item_name ?? 'N/A' }}
                    </p>
                    <p class="text-indigo-400 font-bold">
                        By: {{ $item->registered_by_name ?? 'Staff' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col items-center w-1/2 relative">
                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                    ✓
                </div>
                <h3 class="mt-2 text-sm font-bold text-red-600">Removed</h3>
                <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                    <p class="text-red-600 font-black uppercase">REMOVED</p>

                    @if($latestRemoveWithLocation?->created_at)
                        <p class="font-bold">{{ \Carbon\Carbon::parse($latestRemoveWithLocation->created_at)->format('M d, h:i A') }}</p>
                    @elseif($latestRemove?->created_at)
                        <p class="font-bold">{{ \Carbon\Carbon::parse($latestRemove->created_at)->format('M d, h:i A') }}</p>
                    @elseif(!empty($item->removed_at))
                        <p class="font-bold">{{ \Carbon\Carbon::parse($item->removed_at)->format('M d, h:i A') }}</p>
                    @endif

                    @if(!empty($removedFrom))
                        <p class="text-red-500 font-medium break-words w-36 mx-auto">
                            Removed from: {{ $removedFrom }}
                        </p>
                    @endif

                    @if(!empty($removeReason))
                        <p class="text-red-500 font-medium w-36 mx-auto break-words">
                            {{ $removeReason }}
                        </p>
                    @else
                        <p class="italic text-gray-400">No reason</p>
                    @endif

                    @if(!empty($removedBy))
                        <p class="text-red-500 font-medium">
                            By: {{ $removedBy }}
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>

@elseif(!empty($existingMatch))
    <div class="border border-slate-200 rounded-[2rem] mb-6 overflow-hidden bg-white">
        @include('staff.claims.partials.timeline', [
            'match' => $existingMatch,
            'foundItem' => $item,
            'mode' => 'found_item'
        ])
    </div>

@else
    {{-- Unclaimed: claim-style grey timeline --}}
    <div class="border border-slate-200 rounded-[2rem] mb-6 overflow-hidden bg-white">
        <div class="w-full py-8 bg-white rounded-[2rem]">
            <div class="flex items-start justify-between relative px-6">

                <div class="absolute top-5 left-12 right-12 h-1 bg-gray-200 -z-10"></div>

                {{-- Item Found --}}
                <div class="flex flex-col items-center w-1/5 relative">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                        ✓
                    </div>
                    <h3 class="mt-2 text-sm font-bold text-gray-800">Item Found</h3>
                    <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                        <p class="font-bold">{{ optional($item->created_at)->format('M d, h:i A') ?? 'N/A' }}</p>
                        <p class="truncate w-24 mx-auto text-indigo-500 font-medium">
                            Loc: {{ $item->found_location ?? 'N/A' }}
                        </p>
                        <p class="text-indigo-600 font-black uppercase italic break-words">
                            {{ $item->item_name ?? 'N/A' }}
                        </p>
                        <p class="text-indigo-400 font-bold">
                            By: {{ $item->registered_by_name ?? 'Staff' }}
                        </p>
                    </div>
                </div>

                {{-- Matched --}}
                <div class="flex flex-col items-center w-1/5 relative">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                        2
                    </div>
                    <h3 class="mt-2 text-sm font-bold text-gray-400">Matched</h3>
                    <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                        <p class="italic text-gray-400">Awaiting match...</p>
                    </div>
                </div>

                {{-- Appointment --}}
                <div class="flex flex-col items-center w-1/5 relative">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                        3
                    </div>
                    <h3 class="mt-2 text-sm font-bold text-gray-400">Appointment</h3>
                    <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                        <p class="italic text-gray-400">Awaiting schedule...</p>
                    </div>
                </div>

                {{-- Confirmed --}}
                <div class="flex flex-col items-center w-1/5 relative">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                        4
                    </div>
                    <h3 class="mt-2 text-sm font-bold text-gray-400">Confirmed</h3>
                    <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                        <p class="italic text-gray-400">Waiting for user...</p>
                    </div>
                </div>

                {{-- Handover --}}
                <div class="flex flex-col items-center w-1/5 relative">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                        5
                    </div>
                    <h3 class="mt-2 text-sm font-bold text-gray-400">Handover</h3>
                    <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                        <p class="italic text-gray-400 uppercase">Awaiting scan</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endif