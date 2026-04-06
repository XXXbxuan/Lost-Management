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

@if ($status === 'Removed')
    <div class="mb-6 w-full rounded-[3rem] border border-slate-200 bg-white py-10">
        <div class="relative flex items-start justify-center px-8">
            <div class="absolute left-[25%] right-[25%] top-5 -z-10 h-1 bg-gray-200"></div>

            <div class="relative flex w-1/2 flex-col items-center">
                <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-green-500 text-xl font-bold text-white shadow-md">
                    ✓
                </div>

                <h3 class="mt-2 text-sm font-bold text-gray-800">Item Found</h3>

                <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                    <p class="font-bold">{{ optional($item->created_at)->format('M d, h:i A') ?? 'N/A' }}</p>

                    <p class="mx-auto w-28 truncate font-medium text-indigo-500">
                        Loc: {{ $item->found_location ?? 'N/A' }}
                    </p>

                    <p class="break-words text-indigo-600 font-black uppercase italic">
                        {{ $item->item_name ?? 'N/A' }}
                    </p>

                    <p class="font-bold text-indigo-400">
                        By: {{ $item->registered_by_name ?? 'Staff' }}
                    </p>
                </div>
            </div>

            <div class="relative flex w-1/2 flex-col items-center">
                <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-red-500 text-xl font-bold text-white shadow-md">
                    ✓
                </div>

                <h3 class="mt-2 text-sm font-bold text-red-600">Removed</h3>

                <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                    <p class="font-black uppercase text-red-600">REMOVED</p>

                    @if ($latestRemoveWithLocation?->created_at)
                        <p class="font-bold">
                            {{ \Carbon\Carbon::parse($latestRemoveWithLocation->created_at)->format('M d, h:i A') }}
                        </p>
                    @elseif ($latestRemove?->created_at)
                        <p class="font-bold">
                            {{ \Carbon\Carbon::parse($latestRemove->created_at)->format('M d, h:i A') }}
                        </p>
                    @elseif (!empty($item->removed_at))
                        <p class="font-bold">
                            {{ \Carbon\Carbon::parse($item->removed_at)->format('M d, h:i A') }}
                        </p>
                    @endif

                    @if (!empty($removedFrom))
                        <p class="mx-auto w-36 break-words font-medium text-red-500">
                            Removed from: {{ $removedFrom }}
                        </p>
                    @endif

                    @if (!empty($removeReason))
                        <p class="mx-auto w-36 break-words font-medium text-red-500">
                            {{ $removeReason }}
                        </p>
                    @else
                        <p class="italic text-gray-400">No reason</p>
                    @endif

                    @if (!empty($removedBy))
                        <p class="font-medium text-red-500">
                            By: {{ $removedBy }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@elseif (!empty($existingMatch))
    <div class="mb-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white">
        @include('staff.claims.partials.timeline', [
            'match' => $existingMatch,
            'foundItem' => $item,
            'mode' => 'found_item',
        ])
    </div>
@else
    <div class="mb-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white">
        <div class="w-full rounded-[2rem] bg-white py-8">
            <div class="relative flex items-start justify-between px-6">
                <div class="absolute left-12 right-12 top-5 -z-10 h-1 bg-gray-200"></div>

                <div class="relative flex w-1/5 flex-col items-center">
                    <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-green-500 text-xl font-bold text-white shadow-md">
                        ✓
                    </div>

                    <h3 class="mt-2 text-sm font-bold text-gray-800">Item Found</h3>

                    <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                        <p class="font-bold">{{ optional($item->created_at)->format('M d, h:i A') ?? 'N/A' }}</p>

                        <p class="mx-auto w-24 truncate font-medium text-indigo-500">
                            Loc: {{ $item->found_location ?? 'N/A' }}
                        </p>

                        <p class="break-words text-indigo-600 font-black uppercase italic">
                            {{ $item->item_name ?? 'N/A' }}
                        </p>

                        <p class="font-bold text-indigo-400">
                            By: {{ $item->registered_by_name ?? 'Staff' }}
                        </p>
                    </div>
                </div>

                <div class="relative flex w-1/5 flex-col items-center">
                    <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-gray-300 text-xl font-bold text-white shadow-md">
                        2
                    </div>

                    <h3 class="mt-2 text-sm font-bold text-gray-400">Matched</h3>

                    <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                        <p class="italic text-gray-400">Awaiting match...</p>
                    </div>
                </div>

                <div class="relative flex w-1/5 flex-col items-center">
                    <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-gray-300 text-xl font-bold text-white shadow-md">
                        3
                    </div>

                    <h3 class="mt-2 text-sm font-bold text-gray-400">Appointment</h3>

                    <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                        <p class="italic text-gray-400">Awaiting schedule...</p>
                    </div>
                </div>

                <div class="relative flex w-1/5 flex-col items-center">
                    <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-gray-300 text-xl font-bold text-white shadow-md">
                        4
                    </div>

                    <h3 class="mt-2 text-sm font-bold text-gray-400">Confirmed</h3>

                    <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                        <p class="italic text-gray-400">Waiting for user...</p>
                    </div>
                </div>

                <div class="relative flex w-1/5 flex-col items-center">
                    <div class="z-10 flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-gray-300 text-xl font-bold text-white shadow-md">
                        5
                    </div>

                    <h3 class="mt-2 text-sm font-bold text-gray-400">Handover</h3>

                    <div class="mt-1 space-y-1 text-center text-[10px] text-gray-500">
                        <p class="italic uppercase text-gray-400">Awaiting scan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif