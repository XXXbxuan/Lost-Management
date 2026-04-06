<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matching and Verification – Candidate Matches
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[95rem] mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 items-start lg:grid-cols-12">
                <div class="space-y-6 lg:col-span-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <h3 class="mb-3 border-b pb-2 text-xs font-bold uppercase tracking-wider text-gray-700">
                            Filter Criteria
                        </h3>

                        <form method="GET" action="{{ route('staff.lost-items.show', $lostItem->id) }}">
                            <input type="hidden" name="search" value="1">

                            <div class="mb-3">
                                <label class="mb-1 block text-[10px] font-extrabold uppercase text-gray-400">
                                    Category
                                </label>
                                <select
                                    name="category"
                                    class="w-full rounded border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">-- All --</option>
                                    <option value="Electronics" {{ request('category', $lostItem->category) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="Bag" {{ request('category', $lostItem->category) == 'Bag' ? 'selected' : '' }}>Bag</option>
                                    <option value="Wallet" {{ request('category', $lostItem->category) == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                                    <option value="Clothing" {{ request('category', $lostItem->category) == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="Document" {{ request('category', $lostItem->category) == 'Document' ? 'selected' : '' }}>Document</option>
                                    <option value="Others" {{ request('category', $lostItem->category) == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1 block text-[10px] font-extrabold uppercase text-gray-400">
                                    Found Location
                                </label>
                                <select
                                    name="location"
                                    class="w-full rounded border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">-- All --</option>
                                    <option value="Terminal 1" {{ request('location') == 'Terminal 1' ? 'selected' : '' }}>Terminal 1</option>
                                    <option value="Terminal 2" {{ request('location') == 'Terminal 2' ? 'selected' : '' }}>Terminal 2</option>
                                    <option value="Check-in Counter" {{ request('location') == 'Check-in Counter' ? 'selected' : '' }}>Check-in Counter</option>
                                    <option value="Security Checkpoint" {{ request('location') == 'Security Checkpoint' ? 'selected' : '' }}>Security Checkpoint</option>
                                    <option value="Departure Hall" {{ request('location') == 'Departure Hall' ? 'selected' : '' }}>Departure Hall</option>
                                    <option value="Arrival Hall" {{ request('location') == 'Arrival Hall' ? 'selected' : '' }}>Arrival Hall</option>
                                    <option value="Boarding Gate" {{ request('location') == 'Boarding Gate' ? 'selected' : '' }}>Boarding Gate (General)</option>
                                    <option value="Baggage Claim" {{ request('location') == 'Baggage Claim' ? 'selected' : '' }}>Baggage Claim</option>
                                    <option value="Restroom" {{ request('location') == 'Restroom' ? 'selected' : '' }}>Restroom / Toilet</option>
                                    <option value="Restaurant/Shop" {{ request('location') == 'Restaurant/Shop' ? 'selected' : '' }}>Restaurant / Duty Free Shop</option>
                                    <option value="Airplane Cabin" {{ request('location') == 'Airplane Cabin' ? 'selected' : '' }}>Airplane Cabin (On Board)</option>
                                    <option value="Lounge" {{ request('location') == 'Lounge' ? 'selected' : '' }}>VIP Lounge</option>
                                    <option value="Parking Lot" {{ request('location') == 'Parking Lot' ? 'selected' : '' }}>Parking Lot</option>
                                    <option value="Others" {{ request('location') == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1 block text-[10px] font-extrabold uppercase text-gray-400">
                                    Date Range
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        type="date"
                                        name="date_from"
                                        value="{{ request('date_from', optional($lostItem->lost_time)->format('Y-m-d')) }}"
                                        class="w-1/2 rounded border-gray-300 py-1.5 text-xs"
                                    >
                                    <input
                                        type="date"
                                        name="date_to"
                                        value="{{ request('date_to') }}"
                                        class="w-1/2 rounded border-gray-300 py-1.5 text-xs"
                                    >
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="mb-1 block text-[10px] font-extrabold uppercase text-gray-400">
                                    Keyword
                                </label>
                                <input
                                    type="text"
                                    name="keyword"
                                    value="{{ request('keyword') }}"
                                    placeholder="Search items..."
                                    class="w-full rounded border-gray-300 py-1.5 text-sm"
                                >
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded bg-blue-600 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                SEARCH / REFRESH
                            </button>
                        </form>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <h3 class="mb-3 border-b pb-2 text-xs font-bold uppercase tracking-wider text-gray-700">
                            Lost Report (Main)
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div class="rounded border border-gray-100 bg-gray-50 p-2">
                                <span class="block text-[10px] font-bold uppercase text-gray-400">
                                    Report ID
                                </span>
                                <span class="font-black text-red-600">#{{ $lostItem->id }}</span>
                            </div>

                            <p>
                                <span class="block text-[10px] font-bold uppercase text-gray-400">
                                    Passenger
                                </span>
                                <span class="font-semibold">{{ $lostItem->passenger_name }}</span>
                            </p>

                            <p>
                                <span class="block text-[10px] font-bold uppercase text-gray-400">
                                    Item Details
                                </span>
                                {{ $lostItem->item_name }} ({{ $lostItem->color }})
                            </p>

                            <p>
                                <span class="block text-[10px] font-bold uppercase text-gray-400">
                                    Lost At
                                </span>
                                {{ $lostItem->lost_location }}
                            </p>

                            <p>
                                <span class="block text-[10px] font-bold uppercase text-gray-400">
                                    Date
                                </span>
                                {{ $lostItem->lost_time->format('Y-m-d') }}
                            </p>

                            @if ($lostItem->image_path)
                                <div class="mt-2">
                                    <span class="mb-1 block text-[10px] font-bold uppercase text-gray-400">
                                        Reference Photo
                                    </span>
                                    <img
                                        src="{{ asset('storage/' . $lostItem->image_path) }}"
                                        alt="Lost Item Reference Photo"
                                        class="w-full rounded border shadow-sm"
                                    >
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-9">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-700">
                            Candidate Matches
                            <span class="ml-2 rounded-full border bg-gray-100 px-2 py-1 text-xs font-normal italic text-gray-500">
                                {{ $candidateMatches->count() }} high-similarity matches found
                            </span>
                        </h3>
                    </div>

                    @if ($candidateMatches->count() > 0)
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                            @foreach ($candidateMatches as $found)
                                <div class="group relative flex h-full flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow duration-300 hover:shadow-xl">
                                    <div class="relative h-32 w-full overflow-hidden border-b bg-gray-50">
                                        @if ($found->image_path)
                                            <img
                                                src="{{ asset('storage/' . $found->image_path) }}"
                                                alt="Candidate Found Item Image"
                                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                            >
                                        @else
                                            <div class="flex h-full flex-col items-center justify-center text-[10px] text-gray-300">
                                                <svg class="mb-1 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                No Photo
                                            </div>
                                        @endif

                                        <div class="absolute right-1 top-1 rounded bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-white">
                                            #{{ $found->id }}
                                        </div>
                                    </div>

                                    <div class="flex flex-grow flex-col justify-between p-2.5">
                                        <div>
                                            <h4 class="mb-1 truncate text-xs font-black text-gray-900" title="{{ $found->item_name }}">
                                                {{ $found->item_name }}
                                            </h4>

                                            <div class="space-y-0.5 text-[10px] text-gray-500">
                                                <p class="flex items-center">📅 {{ $found->found_time ? $found->found_time->format('Y-m-d') : 'N/A' }}</p>
                                                <p class="flex items-center truncate">📍 {{ $found->found_location }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="mb-1 flex justify-between text-[9px] font-black uppercase">
                                                <span class="text-gray-400">Match Score</span>
                                                <span class="{{ ($found->similarity_score ?? 0) >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ number_format($found->similarity_score ?? 0, 0) }}%
                                                </span>
                                            </div>

                                            <div class="h-1.5 w-full rounded-full border border-gray-50 bg-gray-100">
                                                <div
                                                    class="h-1.5 rounded-full shadow-inner {{ ($found->similarity_score ?? 0) >= 80 ? 'bg-green-500' : 'bg-yellow-500' }}"
                                                    style="width: {{ $found->similarity_score ?? 0 }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    <a
                                        href="{{ route('staff.match.verify', ['lost_id' => $lostItem->id, 'found_id' => $found->id, 'score' => $found->similarity_score]) }}"
                                        class="block w-full rounded-md border border-blue-200 bg-blue-50 py-2 text-center text-xs font-bold uppercase tracking-wider text-blue-600 no-underline transition hover:bg-blue-100"
                                    >
                                        Verify Match
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg border border-gray-200 bg-white p-16 text-center shadow-sm">
                            <p class="text-sm italic text-gray-400">
                                No matching unclaimed items found with current filters.
                            </p>
                        </div>
                    @endif

                    @if (isset($rejectedItems) && $rejectedItems->count() > 0)
                        <div class="mt-12 border-t pt-6">
                            <h4 class="mb-4 flex items-center text-[11px] font-black uppercase tracking-widest text-gray-400">
                                <span class="mr-2 rounded bg-gray-100 p-1">🚫</span>
                                Previously Rejected (Not Matched)
                            </h4>

                            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-6">
                                @foreach ($rejectedItems as $rejected)
                                    <div class="flex items-center gap-2 rounded border border-gray-200 bg-gray-50 p-2 opacity-60 grayscale transition hover:opacity-100 hover:grayscale-0">
                                        @if ($rejected->image_path)
                                            <img
                                                src="{{ asset('storage/' . $rejected->image_path) }}"
                                                alt="Rejected Candidate Image"
                                                class="h-8 w-8 rounded border object-cover shadow-xs"
                                            >
                                        @endif

                                        <div class="truncate text-[9px]">
                                            <p class="truncate font-bold text-gray-600">#{{ $rejected->id }}</p>
                                            <p class="italic text-gray-400">Rejected</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>