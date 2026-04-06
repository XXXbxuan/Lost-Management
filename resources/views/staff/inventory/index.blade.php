<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-500">
                        Found Items
                    </p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">
                        Inventory Map
                    </h1>
                    <p class="mt-2 text-sm text-slate-500">
                        View storage slot availability and occupied locations.
                    </p>
                </div>

                <a
                    href="{{ route('staff.found-items.index') }}"
                    class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Back to Found Items
                </a>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap gap-3">
                        @foreach (['GEN' => 'Place 1', 'VAULT' => 'Place 2', 'BAG' => 'Place 3'] as $zoneCode => $zoneLabel)
                            <a
                                href="{{ route('staff.inventory.index', ['zone' => $zoneCode]) }}"
                                class="rounded-2xl px-5 py-2 text-sm font-semibold transition
                                    {{ $zone === $zoneCode
                                        ? 'bg-slate-900 text-white shadow-sm'
                                        : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                            >
                                {{ $zoneLabel }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-full bg-cyan-400"></span>
                            <span>Available</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-full bg-amber-400"></span>
                            <span>Service</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-full bg-rose-500"></span>
                            <span>Occupied</span>
                        </div>
                    </div>
                </div>

                <div class="mb-8 flex justify-center">
                    <div class="w-full max-w-3xl rounded-full border border-slate-200 bg-gradient-to-r from-slate-50 via-white to-slate-50 px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.28em] text-slate-700 shadow-inner">
                        {{ $zone }} Storage Zone
                    </div>
                </div>

                @php
                    $orderedShelves = ['S1', 'S2', 'S3'];
                @endphp

                <div class="overflow-x-auto pb-2">
                    <div class="grid min-w-[1100px] grid-cols-3 gap-6">
                        @foreach ($orderedShelves as $shelfCode)
                            @php
                                $shelfSlots = $groupedSlots->get($shelfCode, collect());
                                $leftSlots = $shelfSlots->take(5);
                                $rightSlots = $shelfSlots->slice(5, 5);
                            @endphp

                            <div class="rounded-[2rem] border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-6 shadow-sm transition hover:shadow-md">
                                <div class="mb-5 flex items-center justify-between">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white shadow-sm">
                                        {{ $shelfCode }}
                                    </div>

                                    <div class="text-[11px] font-semibold uppercase tracking-[0.35em] text-slate-400">
                                        Shelf {{ $shelfCode }}
                                    </div>

                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white shadow-sm">
                                        {{ $shelfCode }}
                                    </div>
                                </div>

                                <div class="flex items-start justify-center gap-10">
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach ($leftSlots as $slot)
                                            @php
                                                $item = $occupiedItems[$slot->full_code] ?? null;

                                                if ($slot->slot_status === 'Service') {
                                                    $seatClass = 'bg-amber-400 border-amber-300 text-slate-950 hover:bg-amber-300';
                                                } elseif ($item) {
                                                    $seatClass = 'bg-rose-500 border-rose-300 text-white hover:bg-rose-400';
                                                } else {
                                                    $seatClass = 'bg-cyan-400 border-cyan-300 text-slate-950 hover:bg-cyan-300';
                                                }
                                            @endphp

                                            <a
                                                href="{{ route('staff.inventory.show_slot', $slot->full_code) }}"
                                                title="{{ $slot->full_code }}"
                                                class="relative flex h-16 w-14 items-center justify-center rounded-t-[18px] rounded-b-[24px] border-2 shadow-md transition duration-150 hover:-translate-y-1 hover:shadow-lg {{ $seatClass }}"
                                            >
                                                <span class="absolute top-1.5 h-1 w-5 rounded-full bg-slate-900/10"></span>
                                                <span class="mt-1 text-[13px] font-extrabold tracking-wide">
                                                    {{ $slot->slot_code }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="w-6"></div>

                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach ($rightSlots as $slot)
                                            @php
                                                $item = $occupiedItems[$slot->full_code] ?? null;

                                                if ($slot->slot_status === 'Service') {
                                                    $seatClass = 'bg-amber-400 border-amber-300 text-slate-950 hover:bg-amber-300';
                                                } elseif ($item) {
                                                    $seatClass = 'bg-rose-500 border-rose-300 text-white hover:bg-rose-400';
                                                } else {
                                                    $seatClass = 'bg-cyan-400 border-cyan-300 text-slate-950 hover:bg-cyan-300';
                                                }
                                            @endphp

                                            <a
                                                href="{{ route('staff.inventory.show_slot', $slot->full_code) }}"
                                                title="{{ $slot->full_code }}"
                                                class="relative flex h-16 w-14 items-center justify-center rounded-t-[18px] rounded-b-[24px] border-2 shadow-md transition duration-150 hover:-translate-y-1 hover:shadow-lg {{ $seatClass }}"
                                            >
                                                <span class="absolute top-1.5 h-1 w-5 rounded-full bg-slate-900/10"></span>
                                                <span class="mt-1 text-[13px] font-extrabold tracking-wide">
                                                    {{ $slot->slot_code }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <p class="text-xs text-slate-400 underline underline-offset-4">
                        Click a slot to view item details
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>