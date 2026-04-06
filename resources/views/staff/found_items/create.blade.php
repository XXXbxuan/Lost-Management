@php
    $mode = $mode ?? 'create';
    $foundItem = $foundItem ?? null;

    $storageValue = old('storage_location', $foundItem->storage_location ?? 'GEN-S1-01');
    $parts = explode('-', $storageValue);
    $initialZone = $parts[0] ?? 'GEN';
    $initialShelf = $parts[1] ?? 'S1';
    $initialSlot = $parts[2] ?? '01';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $mode === 'edit' ? 'Edit Found Item' : __('Register New Found Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <div class="relative flex items-center justify-between">
                            <div class="absolute left-0 top-1/2 -z-10 h-1 w-full -translate-y-1/2 bg-gray-200"></div>

                            <div class="flex flex-col items-center">
                                <div id="step-1-dot" class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                                    1
                                </div>
                                <span id="step-1-label" class="mt-1 text-xs font-bold text-indigo-600">
                                    Details
                                </span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-2-dot" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-400">
                                    2
                                </div>
                                <span id="step-2-label" class="mt-1 text-xs font-bold text-gray-500">
                                    Location
                                </span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-3-dot" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-400">
                                    3
                                </div>
                                <span id="step-3-label" class="mt-1 text-xs font-bold text-gray-500">
                                    Storage
                                </span>
                            </div>
                        </div>
                    </div>

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
                        action="{{ $mode === 'edit'
                            ? route('staff.found-items.update', $foundItem->id)
                            : route('staff.found-items.store') }}"
                        enctype="multipart/form-data"
                        id="wizardForm"
                    >
                        @csrf
                        @if ($mode === 'edit')
                            @method('PUT')
                        @endif

                        <div id="step1" class="step-section">
                            <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                                📦 Step 1: Item Details
                            </h3>

                            <x-item-details-form :foundItem="$foundItem" />

                            <div class="mt-6 flex justify-end">
                                <button
                                    type="button"
                                    onclick="nextStep(2)"
                                    class="rounded-md bg-indigo-600 px-6 py-2 font-bold text-white hover:bg-indigo-700"
                                >
                                    Next ➡️
                                </button>
                            </div>
                        </div>

                        <div id="step2" class="step-section hidden">
                            <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                                📍 Step 2: Location & Time
                            </h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">
                                        Found Location (Area) <span class="text-red-500">*</span>
                                    </label>

                                    <select
                                        id="locationSelect"
                                        name="found_location"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        onchange="toggleFlightInput()"
                                        required
                                    >
                                        <option value="">-- Select Location --</option>

                                        @foreach ([
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
                                            'Parking Lot',
                                        ] as $location)
                                            <option
                                                value="{{ $location }}"
                                                {{ old('found_location', $foundItem->found_location ?? '') === $location ? 'selected' : '' }}
                                            >
                                                {{ $location === 'Boarding Gate'
                                                    ? 'Boarding Gate (General)'
                                                    : ($location === 'Restaurant/Shop'
                                                        ? 'Restaurant / Duty Free Shop'
                                                        : ($location === 'Airplane Cabin'
                                                            ? '✈️ Airplane Cabin (On Board)'
                                                            : ($location === 'Restroom'
                                                                ? 'Restroom / Toilet'
                                                                : ($location === 'Lounge'
                                                                    ? 'VIP Lounge'
                                                                    : $location)))) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div
                                    id="flightInputDiv"
                                    class="{{ old('found_location', $foundItem->found_location ?? '') === 'Airplane Cabin' ? '' : 'hidden' }} rounded border border-blue-200 bg-blue-50 p-2"
                                >
                                    <label class="block text-sm font-bold text-blue-800">
                                        Flight Number <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="flight_number"
                                        id="flightInput"
                                        value="{{ old('flight_number', $foundItem->flight_number ?? '') }}"
                                        class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="e.g. MH370"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700">
                                        Found Time <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="datetime-local"
                                        name="found_time"
                                        value="{{ old('found_time', isset($foundItem) && $foundItem?->found_time ? \Carbon\Carbon::parse($foundItem->found_time)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Specific Description
                                    </label>
                                    <textarea
                                        name="description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g. Under seat 12A"
                                    >{{ old('description', $foundItem->description ?? '') }}</textarea>
                                </div>

                                @if ($mode === 'create')
                                    <div class="md:col-span-2 mt-2 rounded-md border border-green-200 bg-green-50 p-4">
                                        <label class="block text-sm font-bold text-green-800">
                                            🎁 Finder's Email (Optional - To award 100 points)
                                        </label>
                                        <input
                                            type="email"
                                            name="finder_email"
                                            value="{{ old('finder_email') }}"
                                            class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                            placeholder="e.g. passenger@gmail.com"
                                        >
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 flex justify-between">
                                <button
                                    type="button"
                                    onclick="prevStep(1)"
                                    class="rounded-md bg-gray-500 px-6 py-2 font-bold text-white hover:bg-gray-600"
                                >
                                    ⬅️ Previous
                                </button>

                                <button
                                    type="button"
                                    onclick="nextStep(3)"
                                    class="rounded-md bg-indigo-600 px-6 py-2 font-bold text-white hover:bg-indigo-700"
                                >
                                    Next ➡️
                                </button>
                            </div>
                        </div>

                        <div
                            id="step3"
                            class="step-section hidden"
                            x-data="{
                                zone: '{{ old('zone', $initialZone) }}',
                                shelf: '{{ old('shelf', $initialShelf) }}',
                                slot: '{{ old('slot', $initialSlot) }}',
                                currentStorage: '{{ old('storage_location', $foundItem->storage_location ?? '') }}',
                                occupiedList: [],
                                serviceList: [],

                                isCurrentSlot(slotCode) {
                                    return slotCode === (this.currentStorage.split('-')[2] || '');
                                },

                                async checkSlots() {
                                    try {
                                        const response = await fetch(`{{ route('staff.check-slots') }}?zone=${this.zone}&shelf=${this.shelf}`, {
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json'
                                            }
                                        });

                                        if (!response.ok) {
                                            console.error('checkSlots response not OK:', response.status);
                                            this.occupiedList = [];
                                            this.serviceList = [];
                                            return;
                                        }

                                        const data = await response.json();

                                        this.occupiedList = Array.isArray(data.occupied) ? data.occupied : [];
                                        this.serviceList = Array.isArray(data.service) ? data.service : [];

                                        if (
                                            this.slot &&
                                            !this.isCurrentSlot(this.slot) &&
                                            (this.occupiedList.includes(this.slot) || this.serviceList.includes(this.slot))
                                        ) {
                                            const firstAvailable = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10']
                                                .find(s =>
                                                    !this.occupiedList.includes(s) &&
                                                    !this.serviceList.includes(s)
                                                );

                                            if (firstAvailable) {
                                                this.slot = firstAvailable;
                                            }
                                        }
                                    } catch (error) {
                                        console.error('checkSlots error:', error);
                                        this.occupiedList = [];
                                        this.serviceList = [];
                                    }
                                },

                                slotLabel(slotCode) {
                                    if (!this.isCurrentSlot(slotCode) && this.serviceList.includes(slotCode)) {
                                        return `Slot ${slotCode} (Service)`;
                                    }

                                    if (!this.isCurrentSlot(slotCode) && this.occupiedList.includes(slotCode)) {
                                        return `Slot ${slotCode} (Occupied)`;
                                    }

                                    return `Slot ${slotCode}`;
                                },

                                slotDisabled(slotCode) {
                                    return !this.isCurrentSlot(slotCode) &&
                                        (this.occupiedList.includes(slotCode) || this.serviceList.includes(slotCode));
                                }
                            }"
                            x-init="checkSlots()"
                        >
                            <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                                🔐 Step 3: Storage Assignment (Internal)
                            </h3>

                            <div class="mb-6 rounded-md border border-gray-200 bg-gray-50 p-6">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-gray-500">
                                            Zone
                                        </label>
                                        <select
                                            name="zone"
                                            x-model="zone"
                                            @change="checkSlots()"
                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                        >
                                            <option value="GEN">General (GEN)</option>
                                            <option value="VAULT">Vault (High Value)</option>
                                            <option value="BAG">Baggage Room</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold uppercase text-gray-500">
                                            Shelf
                                        </label>
                                        <select
                                            name="shelf"
                                            x-model="shelf"
                                            @change="checkSlots()"
                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                        >
                                            <option value="S1">Shelf 1</option>
                                            <option value="S2">Shelf 2</option>
                                            <option value="S3">Shelf 3</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold uppercase text-gray-500">
                                            Slot
                                        </label>
                                        <select
                                            name="slot"
                                            x-model="slot"
                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                        >
                                            @for ($i = 1; $i <= 10; $i++)
                                                @php
                                                    $slotVal = sprintf('%02d', $i);
                                                @endphp
                                                <option
                                                    value="{{ $slotVal }}"
                                                    x-bind:disabled="slotDisabled('{{ $slotVal }}')"
                                                    x-text="slotLabel('{{ $slotVal }}')"
                                                >
                                                    Slot {{ $slotVal }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-6 text-center">
                                    <span class="text-xs text-gray-500">Assigned Storage ID:</span>
                                    <div
                                        class="mt-1 text-2xl font-mono font-bold tracking-wider text-indigo-600"
                                        x-text="`${zone}-${shelf}-${slot}`"
                                    >
                                        GEN-S1-01
                                    </div>
                                    <input
                                        type="hidden"
                                        name="storage_location"
                                        x-bind:value="`${zone}-${shelf}-${slot}`"
                                    >
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between border-t pt-4">
                                <button
                                    type="button"
                                    onclick="prevStep(2)"
                                    class="rounded-md bg-gray-500 px-6 py-2 font-bold text-white hover:bg-gray-600"
                                >
                                    ⬅️ Previous
                                </button>

                                <button
                                    type="submit"
                                    class="transform rounded-md bg-black px-8 py-3 font-bold text-white shadow-lg transition hover:scale-105 hover:bg-gray-800"
                                >
                                    {{ $mode === 'edit' ? '✅ Save Changes' : '✅ Submit Record' }}
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

            const currentSection = document.getElementById('step' + stepNumber);
            if (currentSection) {
                currentSection.classList.remove('hidden');
            }

            updateIndicators(stepNumber);
        }

        function nextStep(step) {
            showStep(step);
        }

        function prevStep(step) {
            showStep(step);
        }

        function updateIndicators(currentStep) {
            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('step-' + i + '-dot');
                const label = document.getElementById('step-' + i + '-label');

                if (!dot || !label) continue;

                if (i < currentStep) {
                    dot.className = 'flex h-8 w-8 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white';
                    dot.innerHTML = '✓';
                    label.className = 'mt-1 text-xs font-bold text-green-600';
                } else if (i === currentStep) {
                    dot.className = 'flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white';
                    dot.innerHTML = i;
                    label.className = 'mt-1 text-xs font-bold text-indigo-600';
                } else {
                    dot.className = 'flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-400';
                    dot.innerHTML = i;
                    label.className = 'mt-1 text-xs font-bold text-gray-500';
                }
            }
        }

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

            const colorSelect = document.getElementById('mainColorSelect');
            if (colorSelect) {
                colorSelect.addEventListener('change', toggleMultiColor);
            }

            const locationSelect = document.getElementById('locationSelect');
            if (locationSelect) {
                locationSelect.addEventListener('change', toggleFlightInput);
            }
        });
    </script>
</x-app-layout>