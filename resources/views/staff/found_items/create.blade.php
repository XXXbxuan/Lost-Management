<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Register New Found Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Step Indicator --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>

                            <div class="flex flex-col items-center">
                                <div id="step-1-dot" class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                                <span class="text-xs font-bold mt-1 text-indigo-600">Details</span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-2-dot" class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">2</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Location</span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-3-dot" class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">3</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Storage</span>
                            </div>
                        </div>
                    </div>

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-md border border-red-200">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.found-items.store') }}" enctype="multipart/form-data" id="wizardForm">
                        @csrf

                        {{-- STEP 1 --}}
                        <div id="step1" class="step-section">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📦 Step 1: Item Details</h3>

                            {{-- Multi-color section is inside this component --}}
                            <x-item-details-form />

                            <div class="flex justify-end mt-6">
                                <button type="button" onclick="nextStep(2)"
                                    class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold hover:bg-indigo-700">
                                    Next ➡️
                                </button>
                            </div>
                        </div>

                        {{-- STEP 2 --}}
                        <div id="step2" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📍 Step 2: Location & Time</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">
                                        Found Location (Area) <span class="text-red-500">*</span>
                                    </label>

                                    <select id="locationSelect" name="found_location"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="toggleFlightInput()" required>
                                        <option value="">-- Select Location --</option>

                                        <option value="Terminal 1" {{ old('found_location') === 'Terminal 1' ? 'selected' : '' }}>Terminal 1</option>
                                        <option value="Terminal 2" {{ old('found_location') === 'Terminal 2' ? 'selected' : '' }}>Terminal 2</option>

                                        <option value="Check-in Counter" {{ old('found_location') === 'Check-in Counter' ? 'selected' : '' }}>Check-in Counter</option>
                                        <option value="Security Checkpoint" {{ old('found_location') === 'Security Checkpoint' ? 'selected' : '' }}>Security Checkpoint</option>
                                        <option value="Departure Hall" {{ old('found_location') === 'Departure Hall' ? 'selected' : '' }}>Departure Hall</option>
                                        <option value="Arrival Hall" {{ old('found_location') === 'Arrival Hall' ? 'selected' : '' }}>Arrival Hall</option>
                                        <option value="Boarding Gate" {{ old('found_location') === 'Boarding Gate' ? 'selected' : '' }}>Boarding Gate (General)</option>
                                        <option value="Baggage Claim" {{ old('found_location') === 'Baggage Claim' ? 'selected' : '' }}>Baggage Claim</option>
                                        <option value="Restroom" {{ old('found_location') === 'Restroom' ? 'selected' : '' }}>Restroom / Toilet</option>
                                        <option value="Restaurant/Shop" {{ old('found_location') === 'Restaurant/Shop' ? 'selected' : '' }}>Restaurant / Duty Free Shop</option>
                                        <option value="Airplane Cabin" {{ old('found_location') === 'Airplane Cabin' ? 'selected' : '' }}>✈️ Airplane Cabin (On Board)</option>
                                        <option value="Lounge" {{ old('found_location') === 'Lounge' ? 'selected' : '' }}>VIP Lounge</option>
                                        <option value="Parking Lot" {{ old('found_location') === 'Parking Lot' ? 'selected' : '' }}>Parking Lot</option>
                                    </select>
                                </div>

                                <div id="flightInputDiv" class="hidden bg-blue-50 p-2 rounded border border-blue-200">
                                    <label class="block font-bold text-sm text-blue-800">
                                        Flight Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="flight_number" id="flightInput"
                                           value="{{ old('flight_number') }}"
                                           class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="e.g. MH370">
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">
                                        Found Time <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="found_time"
                                           value="{{ old('found_time', now()->format('Y-m-d\TH:i')) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Specific Description</label>
                                    <textarea name="description" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                              placeholder="e.g. Under seat 12A">{{ old('description') }}</textarea>
                                </div>

                                <div class="md:col-span-2 mt-2 p-4 bg-green-50 border border-green-200 rounded-md">
                                    <label class="block font-bold text-sm text-green-800">🎁 Finder's Email (Optional - To award 100 points)</label>
                                    <input type="email" name="finder_email" value="{{ old('finder_email') }}"
                                           class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                           placeholder="e.g. passenger@gmail.com">
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="prevStep(1)"
                                        class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600">
                                    ⬅️ Previous
                                </button>
                                <button type="button" onclick="nextStep(3)"
                                        class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold hover:bg-indigo-700">
                                    Next ➡️
                                </button>
                            </div>
                        </div>

                        {{-- STEP 3 --}}
                        <div id="step3" class="step-section hidden"
                             x-data="{
                                zone: 'GEN',
                                shelf: 'S1',
                                slot: '01',
                                occupiedList: [],
                                checkSlots() {
                                    fetch(`{{ route('staff.check-slots') }}?zone=${this.zone}&shelf=${this.shelf}`)
                                        .then(res => res.json())
                                        .then(data => { this.occupiedList = data; })
                                        .catch(err => console.error(err));
                                }
                             }"
                             x-init="checkSlots()">

                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">🔐 Step 3: Storage Assignment (Internal)</h3>

                            <div class="bg-gray-50 p-6 rounded-md border border-gray-200 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Zone</label>
                                        <select name="zone" x-model="zone" @change="checkSlots()"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                            <option value="GEN">General (GEN)</option>
                                            <option value="VAULT">Vault (High Value)</option>
                                            <option value="BAG">Baggage Room</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Shelf</label>
                                        <select name="shelf" x-model="shelf" @change="checkSlots()"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                            <option value="S1">Shelf 1</option>
                                            <option value="S2">Shelf 2</option>
                                            <option value="S3">Shelf 3</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Slot</label>
                                        <select name="slot" x-model="slot"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                            @for ($i = 1; $i <= 10; $i++)
                                                @php $slotVal = sprintf('%02d', $i); @endphp
                                                <option value="{{ $slotVal }}"
                                                        x-bind:disabled="occupiedList.includes('{{ $slotVal }}')"
                                                        x-text="occupiedList.includes('{{ $slotVal }}') ? 'Slot {{ $slotVal }} (Occupied)' : 'Slot {{ $slotVal }}'">
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-6 text-center">
                                    <span class="text-xs text-gray-500">Assigned Storage ID:</span>
                                    <div class="text-2xl font-mono font-bold text-indigo-600 tracking-wider mt-1"
                                         x-text="`${zone}-${shelf}-${slot}`">GEN-S1-01</div>
                                    <input type="hidden" name="storage_location" x-bind:value="`${zone}-${shelf}-${slot}`">
                                </div>
                            </div>

                            <div class="flex justify-between mt-8 pt-4 border-t">
                                <button type="button" onclick="prevStep(2)"
                                        class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600">
                                    ⬅️ Previous
                                </button>

                                <button type="submit"
                                        class="bg-black text-white px-8 py-3 rounded-md font-bold hover:bg-gray-800 shadow-lg transform hover:scale-105 transition">
                                    ✅ Submit Record
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function showStep(stepNumber) {
            document.querySelectorAll('.step-section').forEach(el => el.classList.add('hidden'));
            const el = document.getElementById('step' + stepNumber);
            if (el) el.classList.remove('hidden');
            updateIndicators(stepNumber);
        }

        function nextStep(step) { showStep(step); }
        function prevStep(step) { showStep(step); }

        function updateIndicators(currentStep) {
            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('step-' + i + '-dot');
                if (!dot) continue;

                if (i < currentStep) {
                    dot.className = "w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm";
                    dot.innerHTML = "✓";
                } else if (i === currentStep) {
                    dot.className = "w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm";
                    dot.innerHTML = i;
                } else {
                    dot.className = "w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm";
                    dot.innerHTML = i;
                }
            }
        }

        function toggleMultiColor() {
            const mainColor = document.getElementById('mainColorSelect')?.value;
            const optionsDiv = document.getElementById('multiColorOptions');
            if (!optionsDiv) return;

            if (mainColor === 'Multi-color') optionsDiv.classList.remove('hidden');
            else optionsDiv.classList.add('hidden');
        }

        function toggleFlightInput() {
            const location = document.getElementById('locationSelect')?.value;
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
            showStep(1);
            toggleMultiColor();
            toggleFlightInput();

            // when user changes the color dropdown
            const colorSelect = document.getElementById('mainColorSelect');
            if (colorSelect) colorSelect.addEventListener('change', toggleMultiColor);

            // when user changes the location dropdown
            const locationSelect = document.getElementById('locationSelect');
            if (locationSelect) locationSelect.addEventListener('change', toggleFlightInput);
        });
    </script>
</x-app-layout>