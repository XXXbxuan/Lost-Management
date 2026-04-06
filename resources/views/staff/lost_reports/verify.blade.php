<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matching and Verification – Record Verification Result
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a
                    href="{{ route('staff.lost-items.show', $lostItem->id) }}"
                    class="flex items-center text-sm text-gray-500 hover:text-gray-700"
                >
                    &larr; Back to Candidate List
                </a>
            </div>

            <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-3">
                <div class="rounded-lg border-t-4 border-red-500 bg-white p-6 shadow">
                    <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                        Main Record Details (Lost)
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div class="h-48 overflow-hidden rounded border bg-gray-100">
                            @if ($lostItem->image_path)
                                <img
                                    src="{{ asset('storage/' . $lostItem->image_path) }}"
                                    alt="Lost item image"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-gray-400">
                                    No Image
                                </div>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Lost ID</span>
                            <span class="font-bold text-gray-900">#{{ $lostItem->id }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Item Name</span>
                            <span class="text-gray-900">{{ $lostItem->item_name }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Category</span>
                            <span class="text-gray-900">{{ $lostItem->category }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Brand</span>
                            <span class="text-gray-900">{{ $lostItem->brand ?: '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Serial Number</span>
                            <span class="text-gray-900">{{ $lostItem->serial_number ?: '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Color</span>
                            <span class="text-gray-900">{{ $lostItem->color }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Location</span>
                            <span class="text-gray-900">{{ $lostItem->lost_location }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Date</span>
                            <span class="text-gray-900">{{ $lostItem->lost_time->format('Y-m-d') }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Description</span>
                            <p class="mt-1 rounded border bg-gray-50 p-2 text-gray-600">
                                {{ $lostItem->description ?? 'No description provided.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border-t-4 border-blue-500 bg-white p-6 shadow">
                    <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                        Candidate Record Details (Found)
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div class="relative h-48 overflow-hidden rounded border bg-gray-100">
                            @if ($foundItem->image_path)
                                <img
                                    src="{{ asset('storage/' . $foundItem->image_path) }}"
                                    alt="Found item image"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-gray-400">
                                    No Image
                                </div>
                            @endif

                            <span class="absolute right-2 top-2 rounded bg-blue-100 px-2 py-1 text-xs font-bold text-blue-800">
                                Found
                            </span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Found ID</span>
                            <span class="font-bold text-gray-900">#{{ $foundItem->id }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Item Name</span>
                            <span class="text-gray-900">{{ $foundItem->item_name }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Category</span>
                            <span class="text-gray-900">{{ $foundItem->category }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Brand</span>
                            <span class="text-gray-900">{{ $foundItem->brand ?: '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Serial Number</span>
                            <span class="text-gray-900">{{ $foundItem->serial_number ?: '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Color</span>
                            <span class="text-gray-900">{{ $foundItem->color }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Location</span>
                            <span class="text-gray-900">{{ $foundItem->found_location }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Date</span>
                            <span class="text-gray-900">{{ $foundItem->found_time->format('Y-m-d') }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500">Description</span>
                            <p class="mt-1 rounded border bg-gray-50 p-2 text-gray-600">
                                {{ $foundItem->description ?? 'No description provided.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
                    <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                        Verification Result
                    </h3>

                    <form action="{{ route('staff.match.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="lost_id" value="{{ $lostItem->id }}">
                        <input type="hidden" name="found_id" value="{{ $foundItem->id }}">
                        <input type="hidden" name="similarity_score" value="{{ $score }}">

                        <div class="mb-6" x-data="{ selection: 'matched' }">
                            <span class="mb-2 block text-sm font-bold text-gray-700">Outcome</span>

                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="outcome"
                                        value="matched"
                                        class="sr-only"
                                        x-model="selection"
                                    >
                                    <div
                                        :class="selection === 'matched' ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-500 border-gray-200'"
                                        class="rounded-md border-2 py-3 text-center font-bold shadow-sm transition-all duration-200"
                                    >
                                        Matched
                                    </div>
                                </label>

                                <label class="flex-1 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="outcome"
                                        value="not_matched"
                                        class="sr-only"
                                        x-model="selection"
                                    >
                                    <div
                                        :class="selection === 'not_matched' ? 'bg-red-500 text-white border-red-600' : 'bg-white text-gray-500 border-gray-200'"
                                        class="rounded-md border-2 py-3 text-center font-bold shadow-sm transition-all duration-200"
                                    >
                                        Not Matched
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-bold text-gray-700">
                                Verification Notes
                            </label>
                            <textarea
                                name="notes"
                                rows="6"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter verification details (e.g. brand confirmed, serial number check)..."
                                required
                            >{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3 border-t pt-4">
                            <a
                                href="{{ route('staff.lost-items.show', $lostItem->id) }}"
                                class="px-4 py-2 text-sm font-bold text-gray-600 hover:underline"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="transform rounded-md bg-black px-6 py-2 text-sm font-bold text-white shadow-lg transition hover:bg-gray-800 active:scale-95"
                            >
                                Save Verification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>