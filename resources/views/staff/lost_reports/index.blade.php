<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('🚨 Passenger Lost Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700 shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700 shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="overflow-hidden border border-gray-200 bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-700">All Lost Reports</h3>

                        <a
                            href="{{ route('staff.lost-items.create') }}"
                            class="flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 font-bold text-white no-underline shadow-md transition hover:bg-red-700"
                        >
                            <span class="text-lg">+</span>
                            Create Lost Report
                        </a>
                    </div>

                    <div class="mb-6 flex flex-wrap gap-2 border-b pb-4">
                        @php
                            $filters = ['All', 'LOST', 'Matched', 'Claimed'];
                        @endphp

                        @foreach ($filters as $filter)
                            @php
                                $filterClass = match ($filter) {
                                    'All' => ($status ?? 'All') === $filter
                                        ? 'border-gray-800 bg-gray-800 text-white'
                                        : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50',

                                    'LOST' => ($status ?? 'All') === $filter
                                        ? 'border-red-200 bg-red-100 text-red-700'
                                        : 'border-red-100 bg-white text-red-500 hover:bg-red-50',

                                    'Matched' => ($status ?? 'All') === $filter
                                        ? 'border-yellow-200 bg-yellow-100 text-yellow-700'
                                        : 'border-yellow-100 bg-white text-yellow-600 hover:bg-yellow-50',

                                    'Claimed' => ($status ?? 'All') === $filter
                                        ? 'border-cyan-200 bg-cyan-100 text-cyan-700'
                                        : 'border-cyan-100 bg-white text-cyan-600 hover:bg-cyan-50',

                                    default => 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50',
                                };
                            @endphp

                            <a
                                href="{{ route('staff.lost-items.index', ['status' => $filter]) }}"
                                class="rounded-full border px-5 py-2 text-xs font-bold no-underline shadow-sm transition duration-200 {{ $filterClass }}"
                            >
                                {{ $filter === 'LOST' ? 'Lost (Unsolved)' : $filter }}
                            </a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse bg-white">
                            <thead>
                                <tr class="border-b bg-gray-50 text-xs uppercase leading-normal text-gray-600">
                                    <th class="px-4 py-4 text-left font-bold">ID</th>
                                    <th class="px-6 py-4 text-left font-bold">Item Details</th>
                                    <th class="px-6 py-4 text-left font-bold">Passenger Info</th>
                                    <th class="px-6 py-4 text-left font-bold">Location</th>
                                    <th class="px-6 py-4 text-center font-bold">Status</th>
                                    <th class="px-6 py-4 text-center font-bold">Action</th>
                                </tr>
                            </thead>

                            <tbody class="text-sm text-gray-600">
                                @forelse ($lostItems as $lostItem)
                                    <tr
                                        data-report-id="{{ $lostItem->id }}"
                                        class="border-b border-gray-100 transition hover:bg-gray-50"
                                    >
                                        <td class="px-4 py-4 text-left align-top">
                                            <div class="text-sm font-bold text-slate-700">{{ $lostItem->id }}</div>
                                        </td>

                                        <td class="px-6 py-4 text-left">
                                            <div class="flex items-center">
                                                <div class="mr-3 shrink-0">
                                                    @if ($lostItem->image_path)
                                                        <img
                                                            src="{{ asset('storage/' . $lostItem->image_path) }}"
                                                            alt="Lost Item Image"
                                                            class="h-12 w-12 rounded border object-cover shadow-sm"
                                                        >
                                                    @else
                                                        <div class="flex h-12 w-12 items-center justify-center rounded border bg-gray-50 text-[10px] italic text-gray-300">
                                                            No Pic
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <span class="block font-bold text-gray-800">
                                                        {{ $lostItem->item_name }}
                                                    </span>
                                                    <span class="text-[11px] text-gray-500">
                                                        {{ $lostItem->category }} | {{ $lostItem->color }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-left">
                                            <div class="font-bold text-gray-700">{{ $lostItem->passenger_name }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $lostItem->passenger_phone }}</div>
                                        </td>

                                        <td class="px-6 py-4 text-left">
                                            <div class="font-semibold text-gray-700">{{ $lostItem->lost_location }}</div>
                                            <div class="text-xs text-gray-400">
                                                {{ optional($lostItem->lost_time)->format('Y-m-d H:i:s') ?? $lostItem->lost_time }}
                                            </div>

                                            @if ($lostItem->flight_number)
                                                <div class="mt-1 inline-block rounded bg-blue-50 px-1.5 py-0.5 text-[11px] font-semibold text-blue-500">
                                                    Flight: {{ $lostItem->flight_number }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusClass = match ($lostItem->status) {
                                                    'Matched', 'Reschedule Requested' => 'border-yellow-200 bg-yellow-100 text-yellow-700',
                                                    'Claimed' => 'border-cyan-200 bg-cyan-100 text-cyan-700',
                                                    'LOST', 'Lost' => 'border-red-200 bg-red-100 text-red-700',
                                                    default => 'border-gray-200 bg-gray-100 text-gray-700',
                                                };

                                                $statusLabel = match ($lostItem->status) {
                                                    'Reschedule Requested' => 'Matched',
                                                    'LOST', 'Lost' => 'LOST',
                                                    default => $lostItem->status,
                                                };
                                            @endphp

                                            <span class="{{ $statusClass }} rounded-full border px-3 py-1 text-[10px] font-black uppercase tracking-wider">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        <td
                                            class="px-6 py-4 text-center"
                                            x-data="{ openMenu: false, openReport: {{ (string) $lostItem->id === (string) ($openReportId ?? '') ? 'true' : 'false' }} }"
                                        >
                                            @php
                                                $existingMatch = null;

                                                if (in_array($lostItem->status, ['Matched', 'Claimed', 'Reschedule Requested'])) {
                                                    $existingMatch = \App\Models\MatchRecord::where('lostId', $lostItem->id)
                                                        ->whereIn('status', ['Verified', 'Confirmed', 'Claimed', 'Reschedule Requested'])
                                                        ->latest('id')
                                                        ->first();
                                                }

                                                $actionButtonClass = match ($lostItem->status) {
                                                    'LOST', 'Lost' => 'border-red-300 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800',
                                                    'Matched', 'Reschedule Requested' => 'border-yellow-300 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800',
                                                    'Claimed' => 'border-cyan-300 bg-cyan-100 text-cyan-700 hover:bg-cyan-200 hover:text-cyan-800',
                                                    default => 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-800',
                                                };
                                            @endphp

                                            <div class="relative inline-block text-left">
                                                <button
                                                    type="button"
                                                    @click="openMenu = !openMenu"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border shadow-sm transition {{ $actionButtonClass }}"
                                                    title="Open actions"
                                                >
                                                    ✈️
                                                </button>

                                                <div
                                                    x-show="openMenu"
                                                    x-cloak
                                                    @click.away="openMenu = false"
                                                    class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
                                                    style="display: none;"
                                                >
                                                    <div class="flex flex-col gap-2">
                                                        @if ($lostItem->status === 'LOST' || $lostItem->status === 'Lost')
                                                            <a
                                                                href="{{ route('staff.lost-items.show', $lostItem->id) }}"
                                                                class="block w-full whitespace-nowrap rounded-xl border border-blue-200 bg-blue-50 px-3 py-2.5 text-left text-sm font-semibold text-blue-700 no-underline hover:bg-blue-100"
                                                            >
                                                                ⚡ Match
                                                            </a>

                                                            <a
                                                                href="{{ route('staff.lost-items.edit', $lostItem->id) }}"
                                                                class="block w-full whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50"
                                                            >
                                                                ✏️ Edit
                                                            </a>

                                                            <button
                                                                type="button"
                                                                @click="openMenu = false; openReport = true"
                                                                class="w-full whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                📋 View Report
                                                            </button>

                                                            <form
                                                                method="POST"
                                                                action="{{ route('staff.lost-items.destroy', $lostItem->id) }}"
                                                                onsubmit="return confirm('Are you sure you want to delete this lost report?');"
                                                            >
                                                                @csrf
                                                                @method('DELETE')

                                                                <button
                                                                    type="submit"
                                                                    class="w-full whitespace-nowrap rounded-xl border border-red-200 bg-red-50 px-3 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-100"
                                                                >
                                                                    🗑 Delete
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if (in_array($lostItem->status, ['Matched', 'Reschedule Requested']))
                                                            @if ($existingMatch)
                                                                <a
                                                                    href="{{ route('staff.claims.process', $existingMatch->id) }}"
                                                                    class="block w-full whitespace-nowrap rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-left text-sm font-semibold text-emerald-700 no-underline hover:bg-emerald-100"
                                                                >
                                                                    🛠 Manage Claim
                                                                </a>
                                                            @else
                                                                <a
                                                                    href="{{ route('staff.claims.create', ['lost_id' => $lostItem->id]) }}"
                                                                    class="block w-full whitespace-nowrap rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-left text-sm font-semibold text-emerald-700 no-underline hover:bg-emerald-100"
                                                                >
                                                                    🛠 Manage Claim
                                                                </a>
                                                            @endif

                                                            <button
                                                                type="button"
                                                                @click="openMenu = false; openReport = true"
                                                                class="w-full whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                📋 View Report
                                                            </button>

                                                            <form
                                                                action="{{ route('staff.match.unmatch', $lostItem->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Undo this match?');"
                                                            >
                                                                @csrf

                                                                <button
                                                                    type="submit"
                                                                    class="w-full whitespace-nowrap rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-left text-sm font-semibold text-amber-700 hover:bg-amber-100"
                                                                >
                                                                    ↩ Undo Match
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if ($lostItem->status === 'Claimed')
                                                            <button
                                                                type="button"
                                                                @click="openMenu = false; openReport = true"
                                                                class="w-full whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                📋 View Report
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                x-show="openReport"
                                                x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                                style="display: none;"
                                            >
                                                <div
                                                    @click.away="openReport = false"
                                                    class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl"
                                                >
                                                    <div class="flex items-center justify-between border-b px-6 py-4">
                                                        <h3 class="text-lg font-bold text-slate-800">Lost Report Details</h3>

                                                        <button
                                                            type="button"
                                                            @click="openReport = false"
                                                            class="border-0 bg-transparent text-xl font-bold text-slate-400 hover:text-slate-600"
                                                        >
                                                            ×
                                                        </button>
                                                    </div>

                                                    <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
                                                        @if ($existingMatch && $existingMatch->foundItem)
                                                            <div class="mb-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white">
                                                                @include('staff.claims.partials.timeline', [
                                                                    'match' => $existingMatch,
                                                                    'foundItem' => $existingMatch->foundItem,
                                                                    'mode' => 'claim',
                                                                ])
                                                            </div>
                                                        @else
                                                            <div class="mb-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white">
                                                                <div class="w-full py-10 bg-white rounded-[3rem]">
                                                                    <div class="flex items-start justify-between relative px-8">
                                                                        <div class="absolute top-5 left-16 right-16 h-1 bg-gray-200 -z-10"></div>

                                                                        <div class="flex flex-col items-center w-1/5 relative group">
                                                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                                                                                ✓
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-800">Report</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                                                                                <p class="font-bold">{{ optional($lostItem->created_at)->format('M d, h:i A') ?? 'N/A' }}</p>
                                                                                <p class="truncate w-24 mx-auto text-indigo-500 font-medium">
                                                                                    Lost: {{ $lostItem->lost_location ?? 'N/A' }}
                                                                                </p>
                                                                                <p class="text-indigo-600 font-black uppercase italic break-words">
                                                                                    {{ $lostItem->item_name ?? 'N/A' }}
                                                                                </p>
                                                                                <p class="text-indigo-400 font-bold">
                                                                                    By: {{ $lostItem->staff?->name ?? $lostItem->passenger_name ?? 'Passenger' }}
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative group">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                                                                                2
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Matched</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                                                                                <p class="italic text-gray-400">Awaiting match...</p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative group">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                                                                                3
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Appointment</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                                                                                <p class="italic text-gray-400">Awaiting schedule...</p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative group">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                                                                                4
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Confirmed</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                                                                                <p class="italic text-gray-400">Waiting for user...</p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative group">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md z-10 border-4 border-white">
                                                                                5
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Handover</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                                                                                <p class="italic text-gray-400 font-bold uppercase text-[9px]">Awaiting Scan</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-[260px_1fr]">
                                                            <div>
                                                                @if ($lostItem->image_path)
                                                                    <img
                                                                        src="{{ asset('storage/' . $lostItem->image_path) }}"
                                                                        alt="Lost Report Image"
                                                                        class="h-64 w-full rounded-xl border object-cover shadow-sm"
                                                                    >
                                                                @else
                                                                    <div class="flex h-64 w-full items-center justify-center rounded-xl border bg-gray-100 text-sm italic text-gray-400">
                                                                        No Image
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="grid grid-cols-1 gap-x-8 gap-y-4 text-sm text-slate-700 md:grid-cols-2">
                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">ID</div>
                                                                    <div>{{ $lostItem->id }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Item Name</div>
                                                                    <div class="font-semibold text-slate-800">{{ $lostItem->item_name }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Category</div>
                                                                    <div>{{ $lostItem->category }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Color</div>
                                                                    <div>{{ $lostItem->color }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Brand</div>
                                                                    <div>{{ $lostItem->brand ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Serial Number</div>
                                                                    <div>{{ $lostItem->serial_number ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Lost Location</div>
                                                                    <div>{{ $lostItem->lost_location }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Lost Time</div>
                                                                    <div>{{ $lostItem->lost_time }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Flight Number</div>
                                                                    <div>{{ $lostItem->flight_number ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Passenger Name</div>
                                                                    <div>{{ $lostItem->passenger_name }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Passenger Email</div>
                                                                    <div>{{ $lostItem->passenger_email ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Passenger Phone</div>
                                                                    <div>{{ $lostItem->passenger_phone }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Status</div>
                                                                    <div>{{ $lostItem->status }}</div>
                                                                </div>
                                                            </div>

                                                            <div class="md:col-span-2">
                                                                <div class="text-xs font-bold uppercase text-slate-400">Description</div>
                                                                <div class="mt-1 min-h-[80px] rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                                                    {{ $lostItem->description ?: 'No description provided.' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-6 flex justify-end gap-3">
                                                            <button
                                                                type="button"
                                                                @click="openReport = false"
                                                                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center italic text-gray-400">
                                            No lost items found in this category.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $lostItems->appends(['status' => $status ?? 'All'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($openReportId))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const targetRow = document.querySelector('[data-report-id="{{ $openReportId }}"]');

                if (targetRow) {
                    targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        </script>
    @endif
</x-app-layout>