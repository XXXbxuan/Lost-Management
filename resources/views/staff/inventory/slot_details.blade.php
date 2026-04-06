<x-app-layout>
    <div x-data="slotDetailState(@js($moveSlots ?? []), '{{ $item->storage_location ?? '' }}')">
        <div class="py-8">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-500">
                            Inventory Slot
                        </p>
                        <h1 class="mt-2 text-4xl font-bold tracking-tight text-slate-900">
                            Slot {{ $slot->slot_code }}
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">
                            Full Location:
                            <span class="font-semibold text-slate-700">{{ $slot->full_code }}</span>
                        </p>
                    </div>

                    <a
                        href="{{ route('staff.inventory.index', ['zone' => $slot->zone_code]) }}"
                        class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Back to Inventory Map
                    </a>
                </div>

                <div class="grid items-start gap-6 lg:grid-cols-2">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                        <div class="mb-6 flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">
                            <div>
                                <h2 class="text-2xl font-bold text-slate-900">Slot Information</h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Detailed overview of this storage slot.
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                @if ($displaySlotStatus === 'Service')
                                    <button
                                        type="button"
                                        @click="showRestoreForm = true"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-2.4-2.4 3-3Z" />
                                        </svg>
                                        Restore Slot
                                    </button>
                                @elseif (!$item)
                                    <button
                                        type="button"
                                        @click="showServiceForm = true"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-2.4-2.4 3-3Z" />
                                        </svg>
                                        Mark as Service
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex cursor-not-allowed items-center gap-2 rounded-2xl bg-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-500"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-2.4-2.4 3-3Z" />
                                        </svg>
                                        Move/remove item first
                                    </button>
                                @endif

                                <span
                                    class="rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.25em]
                                    {{ $displaySlotStatus === 'Occupied'
                                        ? 'bg-rose-100 text-rose-700'
                                        : ($displaySlotStatus === 'Service'
                                            ? 'bg-amber-100 text-amber-700'
                                            : 'bg-cyan-100 text-cyan-700') }}"
                                >
                                    {{ $displaySlotStatus }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-4 text-sm text-slate-700 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Zone</div>
                                <div class="mt-1">{{ $slot->zone_code }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Shelf</div>
                                <div class="mt-1">{{ $slot->shelf_code }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Slot</div>
                                <div class="mt-1">{{ $slot->slot_code }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Full Code</div>
                                <div class="mt-1 break-all">{{ $slot->full_code }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Slot Status</div>
                                <div class="mt-1">{{ $displaySlotStatus }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Remark</div>
                                <div class="mt-1">{{ $slot->remark ?? 'N/A' }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Stored Duration</div>
                                <div class="mt-1">{{ $storedDays !== null ? round($storedDays) . ' days' : 'N/A' }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                <div class="font-semibold text-slate-900">Auto Remove Eligible</div>
                                <div class="mt-1">{{ $autoRemoveEligible ? 'Yes' : 'No' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                        <div class="mb-6 border-b border-slate-200 pb-5">
                            <div>
                                <h2 class="text-2xl font-bold text-slate-900">Current Item</h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Detailed information for the item stored in this slot.
                                </p>
                            </div>

                            @if ($item)
                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    <button
                                        type="button"
                                        @click="showRemoveForm = true"
                                        class="rounded-2xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700"
                                    >
                                        Remove Item
                                    </button>

                                    <button
                                        type="button"
                                        @click="showMoveForm = true; selectedZone = 'GEN'; selectedLocation = ''"
                                        class="rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                    >
                                        Move Item
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($item)
                            <div class="grid gap-4 text-sm text-slate-700 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Item Name</div>
                                    <div class="mt-1">{{ $item->item_name ?? $item->name ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Category</div>
                                    <div class="mt-1">{{ $item->category ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Status</div>
                                    <div class="mt-1">{{ $item->status ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Storage Location</div>
                                    <div class="mt-1 break-all">{{ $item->storage_location ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Found At</div>
                                    <div class="mt-1">{{ $item->found_time ?? ($item->date ?? 'N/A') }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Found Location</div>
                                    <div class="mt-1">{{ $item->location ?? $item->found_location ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Color</div>
                                    <div class="mt-1">{{ $item->color ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Brand</div>
                                    <div class="mt-1">{{ $item->brand ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Serial Number</div>
                                    <div class="mt-1">{{ $item->serial_number ?? 'N/A' }}</div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Created At</div>
                                    <div class="mt-1">
                                        {{ $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-6 py-4 leading-7">
                                    <div class="font-semibold text-slate-900">Updated At</div>
                                    <div class="mt-1">
                                        {{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : 'N/A' }}
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <div class="font-semibold text-slate-900">Description</div>
                                    <div class="mt-2 rounded-2xl bg-slate-50 px-5 py-4 leading-7 text-slate-600">
                                        {{ $item->description ?? 'N/A' }}
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <div class="font-semibold text-slate-900">Photo</div>
                                    <div class="mt-2">
                                        @if (!empty($item->photo))
                                            <img
                                                src="{{ asset('storage/' . $item->photo) }}"
                                                alt="Found item photo"
                                                class="max-h-72 rounded-2xl border border-slate-200 shadow-sm"
                                            >
                                        @elseif (!empty($item->photo_path))
                                            <img
                                                src="{{ asset('storage/' . $item->photo_path) }}"
                                                alt="Found item photo"
                                                class="max-h-72 rounded-2xl border border-slate-200 shadow-sm"
                                            >
                                        @elseif (!empty($item->image))
                                            <img
                                                src="{{ asset('storage/' . $item->image) }}"
                                                alt="Found item photo"
                                                class="max-h-72 rounded-2xl border border-slate-200 shadow-sm"
                                            >
                                        @else
                                            <div class="rounded-2xl bg-slate-50 px-5 py-4 leading-7 text-slate-600">
                                                No photo available
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl bg-slate-50 px-5 py-4 text-sm text-slate-600">
                                No active item is currently stored in this slot.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div
            x-show="showRestoreForm"
            x-transition.opacity
            x-cloak
            @keydown.escape.window="showRestoreForm = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6"
            style="display: none;"
        >
            <div
                @click.away="showRestoreForm = false"
                class="w-full max-w-3xl rounded-[2rem] bg-white p-6 shadow-2xl"
            >
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Restore Slot</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Restore this service slot back to available.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="showRestoreForm = false"
                        class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('staff.inventory.restore_slot', $slot->full_code) }}" class="mt-6">
                    @csrf
                    @method('PATCH')

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="space-y-2 text-sm text-slate-700">
                            <div><span class="font-semibold">Full Code:</span> {{ $slot->full_code }}</div>
                            <div><span class="font-semibold">Current Status:</span> {{ $displaySlotStatus }}</div>
                            <div><span class="font-semibold">Remark:</span> {{ $slot->remark ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-5">
                        <button
                            type="button"
                            @click="showRestoreForm = false"
                            class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-2.4-2.4 3-3Z" />
                            </svg>
                            Restore Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            x-show="showServiceForm"
            x-transition.opacity
            x-cloak
            @keydown.escape.window="showServiceForm = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6"
            style="display: none;"
        >
            <div
                @click.away="showServiceForm = false"
                class="w-full max-w-3xl rounded-[2rem] bg-white p-6 shadow-2xl"
            >
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Mark Slot as Service</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Temporarily block this slot for maintenance or service.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="showServiceForm = false"
                        class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('staff.inventory.mark_service', $slot->full_code) }}" class="mt-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <h4 class="text-sm font-bold text-slate-900">Slot Information</h4>

                            <div class="mt-4 space-y-2 text-sm text-slate-700">
                                <div><span class="font-semibold">Zone:</span> {{ $slot->zone_code }}</div>
                                <div><span class="font-semibold">Shelf:</span> {{ $slot->shelf_code }}</div>
                                <div><span class="font-semibold">Slot:</span> {{ $slot->slot_code }}</div>
                                <div><span class="font-semibold">Full Code:</span> {{ $slot->full_code }}</div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <h4 class="text-sm font-bold text-slate-900">Service Remark</h4>

                            <div class="mt-4">
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Remark</label>
                                <textarea
                                    name="service_remark"
                                    rows="4"
                                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-slate-500 focus:ring-slate-500"
                                    placeholder="Example: Shelf damaged / Cleaning in progress / Temporarily blocked"
                                    required
                                >{{ old('service_remark') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-5">
                        <button
                            type="button"
                            @click="showServiceForm = false"
                            class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-3 3-2.4-2.4 3-3Z" />
                            </svg>
                            Mark as Service
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($item)
            <div
                x-show="showMoveForm"
                x-transition.opacity
                x-cloak
                @keydown.escape.window="showMoveForm = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6"
                style="display: none;"
            >
                <div
                    @click.away="showMoveForm = false"
                    class="w-full max-w-4xl rounded-[2rem] bg-white p-6 shadow-2xl"
                >
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Move Item</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Choose a new available slot for this item.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="showMoveForm = false"
                            class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('staff.inventory.move', $item->id) }}" class="mt-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <h4 class="text-sm font-bold text-slate-900">Current Location</h4>

                                <div class="mt-4 space-y-2 text-sm text-slate-700">
                                    <div><span class="font-semibold">Zone:</span> {{ $slot->zone_code }}</div>
                                    <div><span class="font-semibold">Shelf:</span> {{ $slot->shelf_code }}</div>
                                    <div><span class="font-semibold">Slot:</span> {{ $slot->slot_code }}</div>
                                    <div><span class="font-semibold">Full Code:</span> {{ $slot->full_code }}</div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <h4 class="text-sm font-bold text-slate-900">New Location</h4>

                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">Place</label>
                                        <select
                                            x-model="selectedZone"
                                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-slate-500 focus:ring-slate-500"
                                        >
                                            <option value="GEN">Place 1 (GEN)</option>
                                            <option value="VAULT">Place 2 (VAULT)</option>
                                            <option value="BAG">Place 3 (BAG)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">Slot</label>
                                        <select
                                            name="new_location"
                                            x-model="selectedLocation"
                                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-slate-500 focus:ring-slate-500"
                                            required
                                        >
                                            <option value="">Choose an available slot</option>

                                            <template x-for="slotOption in filteredSlots()" :key="slotOption.full_code">
                                                <option
                                                    :value="slotOption.full_code"
                                                    :disabled="slotOption.disabled"
                                                    x-text="slotOption.label"
                                                ></option>
                                            </template>
                                        </select>

                                        @error('new_location')
                                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-5">
                            <button
                                type="button"
                                @click="showMoveForm = false"
                                class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="rounded-2xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
                            >
                                Move
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div
                x-show="showRemoveForm"
                x-transition.opacity
                x-cloak
                @keydown.escape.window="showRemoveForm = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6"
                style="display: none;"
            >
                <div
                    @click.away="showRemoveForm = false"
                    class="w-full max-w-3xl rounded-[2rem] bg-white p-6 shadow-2xl"
                >
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Remove Item</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Remove this item from the inventory slot.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="showRemoveForm = false"
                            class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('staff.inventory.remove', $item->id) }}" class="mt-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <h4 class="text-sm font-bold text-slate-900">Item to Remove</h4>

                                <div class="mt-4 space-y-2 text-sm text-slate-700">
                                    <div><span class="font-semibold">Item Name:</span> {{ $item->item_name ?? $item->name ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">Status:</span> {{ $item->status ?? 'N/A' }}</div>
                                    <div><span class="font-semibold">Current Location:</span> {{ $slot->full_code }}</div>
                                    <div><span class="font-semibold">Stored Duration:</span> {{ $storedDays !== null ? round($storedDays) . ' days' : 'N/A' }}</div>
                                    <div><span class="font-semibold">Auto Remove Eligible:</span> {{ $autoRemoveEligible ? 'Yes' : 'No' }}</div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <h4 class="text-sm font-bold text-slate-900">Removal Reason</h4>

                                <div class="mt-4 space-y-4">
                                    @if ($autoRemoveEligible)
                                        <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                                            This item meets the 90-day unclaimed rule and can be removed.
                                        </div>
                                    @elseif (auth()->check() && auth()->user()->role === 'Admin')
                                        <div class="rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                            Admin override: you can remove this item before the 90-day rule is met.
                                        </div>
                                    @else
                                        <div class="rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                            Only Admin can remove this item before the 90-day unclaimed rule is met.
                                        </div>
                                    @endif

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700">Reason</label>
                                        <textarea
                                            name="removal_reason"
                                            rows="4"
                                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-slate-500 focus:ring-slate-500"
                                            placeholder="Enter removal reason..."
                                            required
                                        >{{ old('removal_reason') }}</textarea>

                                        @error('removal_reason')
                                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-5">
                            <button
                                type="button"
                                @click="showRemoveForm = false"
                                class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="rounded-2xl bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-700"
                            >
                                Remove Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <script>
            function slotDetailState(slots, currentLocation) {
                return {
                    showMoveForm: false,
                    showRemoveForm: false,
                    showServiceForm: false,
                    showRestoreForm: false,
                    selectedZone: 'GEN',
                    selectedLocation: '',
                    slots: slots,
                    currentLocation: currentLocation,

                    filteredSlots() {
                        return this.slots
                            .filter(slot => slot.zone_code === this.selectedZone)
                            .map(slot => {
                                let label = slot.full_code;
                                let disabled = false;

                                if (slot.full_code === this.currentLocation) {
                                    label += ' (Current Slot)';
                                    disabled = true;
                                } else if (slot.slot_status === 'Service') {
                                    label += ' (Service)';
                                    disabled = true;
                                } else if (slot.is_occupied) {
                                    label += ' (Occupied)';
                                    disabled = true;
                                }

                                return {
                                    ...slot,
                                    label,
                                    disabled
                                };
                            });
                    }
                }
            }
        </script>
    </div>
</x-app-layout>