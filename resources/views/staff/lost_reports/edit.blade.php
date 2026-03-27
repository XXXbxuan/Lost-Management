<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            Edit Lost Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-md border border-red-200">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('staff.lost-items.update', $lostItem->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">🚨 Edit Lost Report</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Passenger Information --}}
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-bold text-red-500 uppercase tracking-wide border-b pb-2 mb-2">
                                    Passenger Information
                                </h4>
                            </div>

                            <div>
                                <label class="block font-bold text-sm text-gray-700">Passenger Name <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="passenger_name"
                                       value="{{ old('passenger_name', $lostItem->passenger_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       required>
                            </div>

                            <div>
                                <label class="block font-bold text-sm text-gray-700">Passenger Email <span class="text-red-500">*</span></label>
                                <input type="email"
                                       name="passenger_email"
                                       value="{{ old('passenger_email', $lostItem->passenger_email) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-bold text-sm text-gray-700">Passenger Phone <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="passenger_phone"
                                       value="{{ old('passenger_phone', $lostItem->passenger_phone) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       required>
                            </div>

                            {{-- Item Details --}}
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-bold text-red-500 uppercase tracking-wide border-b pb-2 mb-2 mt-2">
                                    Item Details
                                </h4>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-bold text-sm text-gray-700">Item Name <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="item_name"
                                       value="{{ old('item_name', $lostItem->item_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       required>
                            </div>

                            <div>
                                <label class="block font-bold text-sm text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select name="category"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required>
                                    @foreach(['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $lostItem->category) == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Brand (Optional)</label>
                                <input type="text"
                                       name="brand"
                                       value="{{ old('brand', $lostItem->brand) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Serial Number / Unique ID (Optional)</label>
                                <input type="text"
                                       name="serial_number"
                                       value="{{ old('serial_number', $lostItem->serial_number) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>

                            @php
                                $currentColor = old('color', $lostItem->color ?? '');
                                $currentSubColors = old('sub_colors', []);

                                if (empty($currentSubColors) && !empty($lostItem->color) && str_starts_with($lostItem->color, 'Multi-color (')) {
                                    $inside = trim(str_replace(['Multi-color (', ')'], '', $lostItem->color));
                                    $currentSubColors = array_map('trim', explode(',', $inside));
                                    $currentColor = 'Multi-color';
                                }
                            @endphp

                            <div class="md:col-span-2">
                                <label class="block font-bold text-sm text-gray-700">Color <span class="text-red-500">*</span></label>
                                <select id="mainColorSelect"
                                        name="color"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required>
                                    <option value="">Select Main Color</option>

                                    @foreach(['Black','White','Grey','Silver','Gold','Red','Blue','Brown','Green','Purple','Pink','Orange','Yellow'] as $color)
                                        <option value="{{ $color }}" {{ $currentColor === $color ? 'selected' : '' }}>
                                            {{ $color }}
                                        </option>
                                    @endforeach

                                    <option value="Multi-color" {{ $currentColor === 'Multi-color' ? 'selected' : '' }}>
                                        🎨 Multi-color (Select details below)
                                    </option>
                                </select>

                                <div id="multiColorOptions" class="{{ $currentColor === 'Multi-color' ? '' : 'hidden' }} mt-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Please tick all colors involved:</label>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        @foreach(['Black','White','Red','Blue','Green','Yellow','Orange','Purple','Pink','Grey','Brown','Silver','Gold'] as $subColor)
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                       name="sub_colors[]"
                                                       value="{{ $subColor }}"
                                                       class="rounded text-red-600 focus:ring-red-500"
                                                       {{ in_array($subColor, $currentSubColors) ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">{{ $subColor }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-bold text-sm text-gray-700">Photo (Optional)</label>
                                <input type="file"
                                       name="image"
                                       class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-50">
                            </div>

                            @if($lostItem->image_path)
                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700 mb-2">Current Image</label>
                                    <img src="{{ asset('storage/' . $lostItem->image_path) }}"
                                         alt="Current Image"
                                         class="w-40 h-40 object-cover rounded-lg border shadow-sm">
                                </div>
                            @endif

                            {{-- Lost Details --}}
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-bold text-red-500 uppercase tracking-wide border-b pb-2 mb-2 mt-2">
                                    Lost Details
                                </h4>
                            </div>

                            <div>
                                <label class="block font-bold text-sm text-gray-700">Lost Location <span class="text-red-500">*</span></label>
                                <select name="lost_location"
                                        id="lostLocationSelect"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required>
                                    <option value="">-- Select Location --</option>
                                    @foreach([
                                        'Terminal 1',
                                        'Terminal 2',
                                        'Check-in Counter',
                                        'Security Checkpoint',
                                        'Departure Hall',
                                        'Arrival Hall',
                                        'Boarding Gate',
                                        'Baggage Claim',
                                        'Restroom',
                                        'Restaurant/Shop',
                                        'Airplane Cabin',
                                        'Lounge',
                                        'Parking Lot'
                                    ] as $location)
                                        <option value="{{ $location }}"
                                            {{ old('lost_location', $lostItem->lost_location) === $location ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="flightInputDiv"
                                 class="{{ old('lost_location', $lostItem->lost_location) === 'Airplane Cabin' ? '' : 'hidden' }} bg-blue-50 p-2 rounded border border-blue-200">
                                <label class="block font-bold text-sm text-blue-800">
                                    Flight Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="flight_number"
                                       id="flightInput"
                                       value="{{ old('flight_number', $lostItem->flight_number) }}"
                                       class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="e.g. MH370">
                            </div>

                            <div>
                                <label class="block font-bold text-sm text-gray-700">Lost Time <span class="text-red-500">*</span></label>
                                <input type="datetime-local"
                                       name="lost_time"
                                       value="{{ old('lost_time', $lostItem->lost_time ? $lostItem->lost_time->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                       required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Description</label>
                                <textarea name="description"
                                          rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('description', $lostItem->description) }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-8">
                            <a href="{{ route('staff.lost-items.index') }}"
                               class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Save Changes
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMultiColor() {
            const mainColor = document.getElementById('mainColorSelect')?.value;
            const optionsDiv = document.getElementById('multiColorOptions');
            if (!optionsDiv) return;

            if (mainColor === 'Multi-color') {
                optionsDiv.classList.remove('hidden');
            } else {
                optionsDiv.classList.add('hidden');
            }
        }

        function toggleFlightInput() {
            const location = document.getElementById('lostLocationSelect')?.value;
            const flightDiv = document.getElementById('flightInputDiv');
            const flightInput = document.getElementById('flightInput');

            if (!flightDiv || !flightInput) return;

            if (location === 'Airplane Cabin') {
                flightDiv.classList.remove('hidden');
                flightInput.setAttribute('required', 'required');
            } else {
                flightDiv.classList.add('hidden');
                flightInput.removeAttribute('required');
                flightInput.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleMultiColor();
            toggleFlightInput();

            const colorSelect = document.getElementById('mainColorSelect');
            if (colorSelect) colorSelect.addEventListener('change', toggleMultiColor);

            const locationSelect = document.getElementById('lostLocationSelect');
            if (locationSelect) locationSelect.addEventListener('change', toggleFlightInput);
        });
    </script>
</x-app-layout>