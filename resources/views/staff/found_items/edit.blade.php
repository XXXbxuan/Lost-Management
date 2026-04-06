<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Found Item
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-red-600">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('staff.found-items.update', $foundItem->id) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700">
                                    Item Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="item_name"
                                    value="{{ old('item_name', $foundItem->item_name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    @foreach (['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $foundItem->category) == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Brand (Optional)
                                </label>
                                <input
                                    type="text"
                                    name="brand"
                                    value="{{ old('brand', $foundItem->brand) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Serial Number / Unique ID (Optional)
                                </label>
                                <input
                                    type="text"
                                    name="serial_number"
                                    value="{{ old('serial_number', $foundItem->serial_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="color"
                                    value="{{ old('color', $foundItem->color) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">
                                    Found Location <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="found_location"
                                    value="{{ old('found_location', $foundItem->found_location) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Flight Number
                                </label>
                                <input
                                    type="text"
                                    name="flight_number"
                                    value="{{ old('flight_number', $foundItem->flight_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">
                                    Found Time <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    name="found_time"
                                    value="{{ old('found_time', $foundItem->found_time ? $foundItem->found_time->format('Y-m-d\TH:i') : '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">
                                    Photo (Optional)
                                </label>
                                <input
                                    type="file"
                                    name="image"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 p-2"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >{{ old('description', $foundItem->description) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <a
                                href="{{ route('staff.found-items.index') }}"
                                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>