<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Found Items Management
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
                        <h3 class="text-lg font-bold text-gray-700">Found Items List</h3>

                        <div class="flex items-center gap-3">
                            <a
                                href="{{ route('staff.inventory.index') }}"
                                class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Inventory Map
                            </a>

                            <a
                                href="{{ route('staff.found-items.create') }}"
                                class="flex items-center gap-2 rounded-md bg-black px-4 py-2 text-sm font-bold text-white no-underline shadow-md transition hover:bg-gray-800"
                            >
                                <span>+</span>
                                Register New Item
                            </a>
                        </div>
                    </div>

                    <div class="mb-6 flex flex-wrap gap-2 border-b pb-4">
                        @php
                            $filters = ['All', 'Unclaimed', 'Matched', 'Claimed', 'Removed'];
                        @endphp

                        @foreach ($filters as $filter)
                            <a
                                href="{{ route('staff.found-items.index', ['status' => $filter]) }}"
                                class="rounded-full border px-5 py-2 text-xs font-bold no-underline shadow-sm transition duration-200
                                    {{ $status === $filter
                                        ? 'border-gray-800 bg-gray-800 text-white'
                                        : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}"
                            >
                                {{ $filter }}
                            </a>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 bg-white">
                            <thead>
                                <tr class="bg-gray-100 text-xs uppercase leading-normal text-gray-600">
                                    <th class="px-4 py-3 text-left">ID</th>
                                    <th class="px-6 py-3 text-left">Image</th>
                                    <th class="px-6 py-3 text-left">Item Details</th>
                                    <th class="px-6 py-3 text-left">Location</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="text-sm font-light text-gray-600">
                                @forelse ($foundItems as $item)
                                    <tr
                                        data-item-id="{{ $item->id }}"
                                        class="border-b border-gray-200 transition hover:bg-gray-50"
                                    >
                                        <td class="px-4 py-3 text-left align-top">
                                            <div class="text-sm font-bold text-slate-700">{{ $item->id }}</div>
                                        </td>

                                        <td class="px-6 py-3 text-left">
                                            @if ($item->image_path)
                                                <img
                                                    src="{{ asset('storage/' . $item->image_path) }}"
                                                    alt="Found Item Image"
                                                    class="h-16 w-16 rounded border object-cover shadow-sm"
                                                >
                                            @else
                                                <div class="flex h-16 w-16 items-center justify-center rounded border bg-gray-100 text-xs italic text-gray-400">
                                                    No Img
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-3 text-left">
                                            <div class="font-bold text-gray-800">{{ $item->item_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->category }} | {{ $item->color }}</div>
                                            @if ($item->brand)
                                                <div class="mt-0.5 text-xs text-gray-400">Brand: {{ $item->brand }}</div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-3 text-left">
                                            <div class="font-semibold text-gray-700">{{ $item->found_location }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->found_time }}</div>
                                            @if ($item->storage_location)
                                                <div class="mt-1 inline-block rounded border border-indigo-100 bg-indigo-50 px-1 text-[10px] font-bold text-indigo-500">
                                                    Store: {{ $item->storage_location }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-3 text-center">
                                            @php
                                                $statusClass = match ($item->status) {
                                                    'Matched' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'Claimed' => 'bg-green-100 text-green-800 border-green-200',
                                                    'Unclaimed' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                };
                                            @endphp

                                            <span class="{{ $statusClass }} rounded-full border px-3 py-1 text-[10px] font-black uppercase tracking-wider">
                                                {{ $item->status }}
                                            </span>
                                        </td>

                                        <td
                                            class="px-6 py-3 text-center"
                                            x-data="{ openMenu: false, openItem: {{ (string) $item->id === (string) ($openItemId ?? '') ? 'true' : 'false' }} }"
                                        >
                                            @php
                                                $existingMatch = null;

                                                if (in_array($item->status, ['Matched', 'Claimed'])) {
                                                    $existingMatch = \App\Models\MatchRecord::where('foundId', $item->id)
                                                        ->whereIn('status', ['Verified', 'Confirmed', 'Claimed', 'Reschedule Requested'])
                                                        ->latest('id')
                                                        ->first();
                                                }

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

                                            <div class="relative inline-block text-left">
                                                <button
                                                    type="button"
                                                    @click="openMenu = !openMenu"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-800"
                                                    title="Open actions"
                                                >
                                                    🔍
                                                </button>

                                                <div
                                                    x-show="openMenu"
                                                    x-cloak
                                                    @click.away="openMenu = false"
                                                    class="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
                                                    style="display: none;"
                                                >
                                                    <div class="flex flex-col gap-2">
                                                        @if ($item->status === 'Unclaimed')
                                                            <a
                                                                href="{{ route('staff.found-items.edit', $item->id) }}"
                                                                class="block w-full whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-2 text-left text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50"
                                                            >
                                                                ✏️ Edit
                                                            </a>

                                                            <button
                                                                type="button"
                                                                @click="openMenu = false; openItem = true"
                                                                class="w-full whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-2 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                📋 View Item
                                                            </button>

                                                            <form
                                                                method="POST"
                                                                action="{{ route('staff.found-items.destroy', $item->id) }}"
                                                                onsubmit="return confirm('Are you sure you want to delete this item?');"
                                                            >
                                                                @csrf
                                                                @method('DELETE')

                                                                <button
                                                                    type="submit"
                                                                    class="w-full whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-left text-sm font-semibold text-red-600 hover:bg-red-100"
                                                                >
                                                                    🗑 Delete
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if ($item->status === 'Matched' && $existingMatch)
                                                            <button
                                                                type="button"
                                                                @click="openMenu = false; openItem = true"
                                                                class="w-full whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-2 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                📋 View Item
                                                            </button>

                                                            <form
                                                                action="{{ route('staff.match.unmatch', $existingMatch->lostId) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Undo this match?');"
                                                            >
                                                                @csrf

                                                                <button
                                                                    type="submit"
                                                                    class="w-full whitespace-nowrap rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-left text-sm font-semibold text-amber-700 hover:bg-amber-100"
                                                                >
                                                                    ↩ Undo Match
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if (in_array($item->status, ['Claimed', 'Removed']))
                                                            <button
                                                                type="button"
                                                                @click="openMenu = false; openItem = true"
                                                                class="w-full whitespace-nowrap rounded-lg border border-slate-200 bg-white px-3 py-2 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                            >
                                                                📋 View Item
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                x-show="openItem"
                                                x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                                                style="display: none;"
                                            >
                                                <div
                                                    @click.away="openItem = false"
                                                    class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl"
                                                >
                                                    <div class="flex items-center justify-between border-b px-6 py-4">
                                                        <h3 class="text-lg font-bold text-slate-800">Found Item Details</h3>

                                                        <button
                                                            type="button"
                                                            @click="openItem = false"
                                                            class="border-0 bg-transparent text-xl font-bold text-slate-400 hover:text-slate-600"
                                                        >
                                                            ×
                                                        </button>
                                                    </div>

                                                    <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
                                                        @include('staff.found_items.partials.timeline', [
                                                            'item' => $item,
                                                            'existingMatch' => $existingMatch
                                                        ])

                                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-[260px_1fr]">
                                                            <div>
                                                                @if ($item->image_path)
                                                                    <img
                                                                        src="{{ asset('storage/' . $item->image_path) }}"
                                                                        alt="Found Item Detail Image"
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
                                                                    <div>{{ $item->id }}</div>
                                                                </div>

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

                                                                @if ($item->storage_location)
                                                                    <div>
                                                                        <div class="text-xs font-bold uppercase text-slate-400">Storage Location</div>
                                                                        <div>{{ $item->storage_location }}</div>
                                                                    </div>
                                                                @endif

                                                                @if ($item->status === 'Removed')
                                                                    <div>
                                                                        <div class="text-xs font-bold uppercase text-red-400">Removed From</div>
                                                                        <div class="font-medium text-red-600">
                                                                            {{ $removedFrom ?: '-' }}
                                                                        </div>
                                                                    </div>

                                                                    <div>
                                                                        <div class="text-xs font-bold uppercase text-red-400">Removal Reason</div>
                                                                        <div class="font-medium text-red-600">
                                                                            {{ $latestRemove?->remarks ?? $item->removal_reason ?? 'No removal reason recorded.' }}
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="md:col-span-2">
                                                                <div class="text-xs font-bold uppercase text-slate-400">Description</div>
                                                                <div class="mt-1 min-h-[80px] rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                                                    {{ $item->description ?: 'No description provided.' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-6 flex justify-end gap-3">
                                                            <button
                                                                type="button"
                                                                @click="openItem = false"
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
                                        <td colspan="6" class="bg-gray-50 py-10 text-center italic text-gray-400">
                                            No items found in this category.
                                            <br>
                                            <a
                                                href="{{ route('staff.found-items.create') }}"
                                                class="mt-2 inline-block text-xs font-bold text-blue-600 hover:underline"
                                            >
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

    @if (!empty($openItemId))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const targetRow = document.querySelector('[data-item-id="{{ $openItemId }}"]');

                if (targetRow) {
                    targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        </script>
    @endif
</x-app-layout>