<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pickup Receipt
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="p-8">
                    <div class="mb-8 rounded-2xl border border-green-200 bg-green-50 px-6 py-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-xl font-bold text-green-800">Item Successfully Collected</h3>
                                <p class="mt-1 text-sm text-green-700">
                                    This pickup has already been completed successfully. The verification QR code is no longer active.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <h4 class="mb-4 text-lg font-bold text-gray-800">Claim Information</h4>

                            <div class="space-y-3 text-sm text-gray-700">
                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Claim ID</div>
                                    <div>{{ $claim->id ?? '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Receipt No</div>
                                    <div>{{ $claim->receipt_no ?? '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Claim Status</div>
                                    <div class="font-semibold text-green-700">{{ $match->status ?? 'Claimed' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Collected At</div>
                                    <div>
                                        {{ $claim->created_at ? $claim->created_at->format('d M Y, h:i A') : '-' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Processed By</div>
                                    <div>{{ $claim->processed_by_name ?? $claim->processedBy ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                            <h4 class="mb-4 text-lg font-bold text-gray-800">Item Information</h4>

                            <div class="space-y-3 text-sm text-gray-700">
                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Item Name</div>
                                    <div>{{ $foundItem->item_name ?? '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Category</div>
                                    <div>{{ $foundItem->category ?? '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Brand</div>
                                    <div>{{ $foundItem->brand ?: '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Color</div>
                                    <div>{{ $foundItem->color ?: '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Found Location</div>
                                    <div>{{ $foundItem->found_location ?? '-' }}</div>
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase text-gray-400">Storage Location</div>
                                    <div>{{ $foundItem->storage_location ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!empty($claim?->handover_notes))
                        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6">
                            <div class="mb-2 text-xs font-bold uppercase text-gray-400">Handover Notes</div>
                            <div class="whitespace-pre-line text-sm text-gray-700">
                                {{ $claim->handover_notes }}
                            </div>
                        </div>
                    @endif

                    @if (!empty($claim?->handover_photo))
                        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6">
                            <div class="mb-3 text-xs font-bold uppercase text-gray-400">Handover Photo</div>
                            <img
                                src="{{ asset('storage/' . $claim->handover_photo) }}"
                                alt="Handover Photo"
                                class="w-full max-w-md rounded-xl border shadow-sm"
                            >
                        </div>
                    @endif

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white no-underline hover:bg-slate-800"
                        >
                            Back to Dashboard
                        </a>

                        <a
                            href="{{ route('passenger.found_items') }}"
                            class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 no-underline hover:bg-gray-50"
                        >
                            Browse Found Items
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>