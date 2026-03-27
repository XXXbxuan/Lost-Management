<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Found Items Management
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
                        <h3 class="text-lg font-bold text-gray-700">Found Items List</h3>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('staff.inventory.index') }}"
                               class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Inventory Map
                            </a>

                            <a href="{{ route('staff.found-items.create') }}" 
                               class="bg-black text-white px-4 py-2 rounded-md font-bold hover:bg-gray-800 transition shadow-md no-underline flex items-center gap-2 text-sm">
                                <span>+</span> Register New Item
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6 border-b pb-4">
                        @php
                            $filters = ['All', 'Unclaimed', 'Matched', 'Claimed'];
                        @endphp

                        @foreach($filters as $filter)
                            <a href="{{ route('staff.found-items.index', ['status' => $filter]) }}"
                               class="px-5 py-2 rounded-full text-xs font-bold border transition duration-200 no-underline shadow-sm
                               {{ $status === $filter 
                                   ? 'bg-gray-800 text-white border-gray-800'
                                   : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                               }}">
                               {{ $filter }}
                            </a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                                    <th class="py-3 px-6 text-left">Image</th>
                                    <th class="py-3 px-6 text-left">Item Details</th>
                                    <th class="py-3 px-6 text-left">Location</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($foundItems as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="py-3 px-6 text-left">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" class="w-16 h-16 object-cover border rounded shadow-sm">
                                            @else
                                                <div class="w-16 h-16 bg-gray-100 text-gray-400 flex items-center justify-center text-xs rounded border italic">
                                                    No Img
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-3 px-6 text-left">
                                            <div class="font-bold text-gray-800">{{ $item->item_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->category }} | {{ $item->color }}</div>
                                            @if($item->brand)
                                                <div class="text-xs text-gray-400 mt-0.5">Brand: {{ $item->brand }}</div>
                                            @endif
                                        </td>

                                        <td class="py-3 px-6 text-left">
                                            <div class="font-semibold text-gray-700">{{ $item->found_location }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->found_time }}</div>
                                            @if($item->storage_location)
                                                <div class="text-[10px] text-indigo-500 font-bold mt-1 bg-indigo-50 inline-block px-1 rounded border border-indigo-100">
                                                    Store: {{ $item->storage_location }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-3 px-6 text-center">
                                            @php
                                                $statusClass = match($item->status) {
                                                    'Matched' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'Claimed' => 'bg-green-100 text-green-800 border-green-200',
                                                    'Unclaimed' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }} py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border">
                                                {{ $item->status }}
                                            </span>
                                        </td>

                                        <td class="py-3 px-6 text-center" x-data="{ openModal: false }">
                                            @php
    $existingMatch = \App\Models\MatchRecord::where('foundId', $item->id)->first();

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
@endphp

                                            <button type="button"
                                                    @click="openModal = true"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-800 shadow-sm transition"
                                                    title="View details">
                                                👁
                                            </button>

                                            <div x-show="openModal"
                                                 x-cloak
                                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                                 style="display: none;">

                                                <div @click.away="openModal = false"
                                                     class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">

                                                    <div class="flex items-center justify-between border-b px-6 py-4">
                                                        <h3 class="text-lg font-bold text-slate-800">Found Item Details</h3>
                                                        <button type="button"
                                                                @click="openModal = false"
                                                                class="text-slate-400 hover:text-slate-600 text-xl font-bold bg-transparent border-0">
                                                            ×
                                                        </button>
                                                    </div>

                                                    <div class="px-6 py-5 max-h-[75vh] overflow-y-auto">

                                                        @include('staff.found_items.partials.timeline', [
                                                            'item' => $item,
                                                            'existingMatch' => $existingMatch
                                                        ])

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                            <div>
                                                                @if($item->image_path)
                                                                    <img src="{{ asset('storage/' . $item->image_path) }}"
                                                                         class="w-full h-64 object-cover rounded-xl border shadow-sm">
                                                                @else
                                                                    <div class="w-full h-64 bg-gray-100 text-gray-400 flex items-center justify-center text-sm rounded-xl border italic">
                                                                        No Image
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="space-y-3 text-sm text-slate-700">
                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Item Name</div>
                                                                    <div class="font-semibold text-slate-800">{{ $item->item_name }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Category</div>
                                                                    <div>{{ $item->category }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Color</div>
                                                                    <div>{{ $item->color }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Brand</div>
                                                                    <div>{{ $item->brand ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Serial Number</div>
                                                                    <div>{{ $item->serial_number ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Found Location</div>
                                                                    <div>{{ $item->found_location }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Found Time</div>
                                                                    <div>{{ $item->found_time }}</div>
                                                                </div>

                                                                

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Flight Number</div>
                                                                    <div>{{ $item->flight_number ?: '-' }}</div>
                                                                </div>

                                                                <div>
                                                                    <div class="text-xs font-bold uppercase text-slate-400">Status</div>
                                                                    <div>{{ $item->status }}</div>
                                                                </div>

                                                                @if($item->status === 'Removed')
    <div>
        <div class="text-xs font-bold uppercase text-red-400">Removed From</div>
        <div class="text-red-600 font-medium">
            {{ $removedFrom ?: '-' }}
        </div>
    </div>

    <div>
        <div class="text-xs font-bold uppercase text-red-400">Removal Reason</div>
        <div class="text-red-600 font-medium">
            {{ $latestRemove?->remarks ?? $item->removal_reason ?? 'No removal reason recorded.' }}
        </div>
    </div>
@endif
                                                            </div>

                                                            <div class="md:col-span-2">
                                                                <div class="text-xs font-bold uppercase text-slate-400">Description</div>
                                                                <div class="mt-1 rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-700 min-h-[80px]">
                                                                    {{ $item->description ?: 'No description provided.' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-6 flex justify-end gap-3">
                                                            @if($item->status === 'Unclaimed')
                                                                <a href="{{ route('staff.found-items.edit', $item->id) }}"
                                                                   class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 no-underline">
                                                                    Edit
                                                                </a>

                                                                <form method="POST"
                                                                      action="{{ route('staff.found-items.destroy', $item->id) }}"
                                                                      onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            @elseif($item->status === 'Matched' && $existingMatch)
                                                                <form action="{{ route('staff.match.unmatch', $existingMatch->lostId) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Undo this match?');">
                                                                    @csrf
                                                                    <button type="submit"
                                                                            class="inline-flex items-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                                                                        Undo Match
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <button type="button"
                                                                    @click="openModal = false"
                                                                    class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-gray-400 italic bg-gray-50">
                                            No items found in this category.
                                            <br>
                                            <a href="{{ route('staff.found-items.create') }}" class="text-blue-600 hover:underline font-bold text-xs mt-2 inline-block">
                                                Register a new item?
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $foundItems->appends(['status' => $status])->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>