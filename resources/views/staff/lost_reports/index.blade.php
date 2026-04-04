<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('🚨 Passenger Lost Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">All Lost Reports</h3>
                        
                        <a href="{{ route('staff.lost-items.create') }}" 
                           class="bg-red-600 text-white px-4 py-2 rounded-md font-bold hover:bg-red-700 transition shadow-md no-underline flex items-center gap-2">
                           <span class="text-lg">+</span> Create Lost Report
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6 border-b pb-4">
                        @php
                            $filters = ['All', 'LOST', 'Matched', 'Claimed'];
                        @endphp

                        @foreach($filters as $filter)
                            <a href="{{ route('staff.lost-items.index', ['status' => $filter]) }}"
                               class="px-5 py-2 rounded-full text-xs font-bold border transition duration-200 no-underline shadow-sm
                               {{ ($status ?? 'All') === $filter 
                                   ? 'bg-red-600 text-white border-red-600'
                                   : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                               }}">
                               {{ $filter === 'LOST' ? 'Lost (Unsolved)' : $filter }}
                            </a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal border-b">
                                    <th class="py-4 px-4 text-left font-bold">ID</th>
                                    <th class="py-4 px-6 text-left font-bold">Item Details</th>
                                    <th class="py-4 px-6 text-left font-bold">Passenger Info</th>
                                    <th class="py-4 px-6 text-left font-bold">Location</th>
                                    <th class="py-4 px-6 text-center font-bold">Status</th>
                                    <th class="py-4 px-6 text-center font-bold">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm">
                                @forelse($lostItems as $lostItem)
                                    <tr data-report-id="{{ $lostItem->id }}" class="border-b border-gray-100 hover:bg-gray-50 transition">
                                        <td class="py-4 px-4 text-left align-top">
                                            <div class="text-sm font-bold text-slate-700">{{ $lostItem->id }}</div>
                                        </td>

                                        <td class="py-4 px-6 text-left">
                                            <div class="flex items-center">
                                                <div class="mr-3 flex-shrink-0">
                                                    @if($lostItem->image_path)
                                                        <img src="{{ asset('storage/' . $lostItem->image_path) }}" class="w-12 h-12 rounded shadow-sm border object-cover">
                                                    @else
                                                        <div class="w-12 h-12 rounded border bg-gray-50 flex items-center justify-center text-[10px] text-gray-300 italic">No Pic</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="font-bold block text-gray-800">{{ $lostItem->item_name }}</span>
                                                    <span class="text-[11px] text-gray-500">{{ $lostItem->category }} | {{ $lostItem->color }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-4 px-6 text-left">
                                            <div class="font-bold text-gray-700">{{ $lostItem->passenger_name }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $lostItem->passenger_phone }}</div>
                                        </td>

                                        <td class="py-4 px-6 text-left">
                                            <div class="font-semibold text-gray-700">{{ $lostItem->lost_location }}</div>
                                            <div class="text-xs text-gray-400">
                                                {{ optional($lostItem->lost_time)->format('Y-m-d H:i:s') ?? $lostItem->lost_time }}
                                            </div>
                                            @if($lostItem->flight_number)
                                                <div class="text-[11px] text-blue-500 font-semibold bg-blue-50 px-1.5 py-0.5 rounded inline-block mt-1">
                                                    Flight: {{ $lostItem->flight_number }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-4 px-6 text-center">
                                            @if($lostItem->status == 'Matched' || $lostItem->status == 'Reschedule Requested')
                                                <span class="bg-emerald-100 text-emerald-700 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                                                    {{ $lostItem->status == 'Reschedule Requested' ? 'Reschedule' : 'Matched' }}
                                                </span>
                                            @elseif($lostItem->status == 'Claimed')
                                                <span class="bg-gray-100 text-gray-500 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-gray-200">
                                                    Claimed
                                                </span>
                                            @else
                                                <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-red-200">
                                                    LOST
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-4 px-6 text-center"
                                            x-data="{ openMenu: false, openReport: {{ (string)$lostItem->id === (string)($openReportId ?? '') ? 'true' : 'false' }} }">
                                            @php
    $existingMatch = null;

    if (in_array($lostItem->status, ['Matched', 'Claimed', 'Reschedule Requested'])) {
        $existingMatch = \App\Models\MatchRecord::where('lostId', $lostItem->id)
            ->whereIn('status', ['Verified', 'Confirmed', 'Claimed', 'Reschedule Requested'])
            ->latest('id')
            ->first();
    }
@endphp

                                            <div class="relative inline-block text-left">
                                                <button type="button"
                                                        @click="openMenu = !openMenu"
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-800 shadow-sm transition"
                                                        title="Open actions">
                                                    ✈️
                                                </button>

                                                <div x-show="openMenu"
                                                     x-cloak
                                                     @click.away="openMenu = false"
                                                     class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white border border-slate-200 shadow-xl z-50 p-2"
                                                     style="display: none;">

                                                    <div class="flex flex-col gap-2">
                                                        @if($lostItem->status === 'LOST' || $lostItem->status === 'Lost')
                                                        <a href="{{ route('staff.lost-items.show', $lostItem->id) }}"
                                                               class="w-full text-left whitespace-nowrap rounded-xl border border-blue-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-100 no-underline block">
                                                                ⚡ Match
                                                            </a>
                                                            <a href="{{ route('staff.lost-items.edit', $lostItem->id) }}"
                                                               class="w-full text-left whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline block">
                                                                ✏️ Edit
                                                            </a>
                                                            <button type="button"
                                                                    @click="openMenu = false; openReport = true"
                                                                    class="w-full text-left whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                                📋 View Report
                                                            </button>

                                                            

                                                            <form method="POST"
                                                                  action="{{ route('staff.lost-items.destroy', $lostItem->id) }}"
                                                                  onsubmit="return confirm('Are you sure you want to delete this lost report?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="w-full text-left whitespace-nowrap rounded-xl border border-red-200 bg-red-50 px-3 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100">
                                                                    🗑 Delete
                                                                </button>
                                                            </form>

                                                            
                                                        @endif

                                                        @if(in_array($lostItem->status, ['Matched', 'Reschedule Requested']))
                                                            

                                                            @if($existingMatch)
                                                                <a href="{{ route('staff.claims.process', $existingMatch->id) }}"
                                                                   class="w-full text-left whitespace-nowrap rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 no-underline block">
                                                                    🛠 Manage Claim
                                                                </a>
                                                            @else
                                                                <a href="{{ route('staff.claims.create', ['lost_id' => $lostItem->id]) }}"
                                                                   class="w-full text-left whitespace-nowrap rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 no-underline block">
                                                                    🛠 Manage Claim
                                                                </a>
                                                            @endif
                                                            <button type="button"
                                                                    @click="openMenu = false; openReport = true"
                                                                    class="w-full text-left whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                                📋 View Report
                                                            </button>

                                                            <form action="{{ route('staff.match.unmatch', $lostItem->id) }}"
                                                                  method="POST"
                                                                  onsubmit="return confirm('Undo this match?');">
                                                                @csrf
                                                                <button type="submit"
                                                                        class="w-full text-left whitespace-nowrap rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-100">
                                                                    ↩ Undo Match
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($lostItem->status === 'Claimed')
                                                            <button type="button"
                                                                    @click="openMenu = false; openReport = true"
                                                                    class="w-full text-left whitespace-nowrap rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                                📋 View Report
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div x-show="openReport"
                                                 x-cloak
                                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                                 style="display: none;">

                                                <div @click.away="openReport = false"
                                                     class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl overflow-hidden">

                                                    <div class="flex items-center justify-between border-b px-6 py-4">
                                                        <h3 class="text-lg font-bold text-slate-800">Lost Report Details</h3>
                                                        <button type="button"
                                                                @click="openReport = false"
                                                                class="text-slate-400 hover:text-slate-600 text-xl font-bold bg-transparent border-0">
                                                            ×
                                                        </button>
                                                    </div>

                                                    <div class="px-6 py-5 max-h-[75vh] overflow-y-auto">
                                                        @if($existingMatch && $existingMatch->foundItem)
                                                            <div class="mb-6 border border-slate-200 rounded-[2rem] overflow-hidden bg-white">
                                                                @include('staff.claims.partials.timeline', [
                                                                    'match' => $existingMatch,
                                                                    'foundItem' => $existingMatch->foundItem,
                                                                    'mode' => 'claim'
                                                                ])
                                                            </div>
                                                        @else
                                                            <div class="w-full py-10 bg-white rounded-[3rem] border border-slate-200 mb-6">
                                                                <div class="flex items-start justify-center relative px-8">
                                                                    <div class="flex items-center justify-center w-full max-w-5xl mx-auto relative">
                                                                        
                                                                        <div class="absolute top-5 left-[17%] right-[17%] h-[2px] bg-gray-200 z-0"></div>

                                                                        <div class="flex flex-col items-center w-1/5 relative z-10">
                                                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md border-4 border-white">
                                                                                ✓
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-800">Report</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-500 space-y-1">
                                                                                <p class="font-bold">{{ optional($lostItem->created_at)->format('M d, h:i A') ?? 'N/A' }}</p>
                                                                                <p class="truncate w-28 mx-auto text-indigo-500 font-medium">
                                                                                    Lost: {{ $lostItem->lost_location ?? 'N/A' }}
                                                                                </p>
                                                                                <p class="text-indigo-600 font-black uppercase italic break-words">
                                                                                    {{ $lostItem->item_name ?? 'N/A' }}
                                                                                </p>
                                                                                <p class="text-indigo-400 font-bold">
                                                                                    By: {{ $lostItem->staff?->name ?? 'Staff' }}
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative z-10">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md border-4 border-white">
                                                                                2
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Matched</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-400 italic">
                                                                                Awaiting match
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative z-10">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md border-4 border-white">
                                                                                3
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Appointment</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-400 italic">
                                                                                Pending
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative z-10">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md border-4 border-white">
                                                                                4
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Confirmed</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-400 italic">
                                                                                Pending
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex flex-col items-center w-1/5 relative z-10">
                                                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md border-4 border-white">
                                                                                5
                                                                            </div>
                                                                            <h3 class="mt-2 text-sm font-bold text-gray-400">Claimed</h3>
                                                                            <div class="mt-1 text-[10px] text-center text-gray-400 italic">
                                                                                Pending
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="grid grid-cols-1 md:grid-cols-[260px_1fr] gap-6">
                                                            <div>
                                                                @if($lostItem->image_path)
                                                                    <img src="{{ asset('storage/' . $lostItem->image_path) }}"
                                                                         class="w-full h-64 object-cover rounded-xl border shadow-sm">
                                                                @else
                                                                    <div class="w-full h-64 bg-gray-100 text-gray-400 flex items-center justify-center text-sm rounded-xl border italic">
                                                                        No Image
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm text-slate-700">
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
                                                                <div class="mt-1 rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-700 min-h-[80px]">
                                                                    {{ $lostItem->description ?: 'No description provided.' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-6 flex justify-end gap-3">
                                                            <button type="button"
                                                                    @click="openReport = false"
                                                                    class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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
                                        <td colspan="6" class="py-10 text-center text-gray-400 italic">
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

    @if(!empty($openReportId))
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