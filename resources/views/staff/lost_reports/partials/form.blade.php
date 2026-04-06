@php
    $mode = $mode ?? 'create';
    $lostItem = $lostItem ?? null;

    $currentColor = old('color', $lostItem->color ?? '');
    $currentSubColors = old('sub_colors', []);

    if (empty($currentSubColors) && !empty($lostItem?->color) && str_starts_with($lostItem->color, 'Multi-color (')) {
        $inside = trim(str_replace(['Multi-color (', ')'], '', $lostItem->color));
        $currentSubColors = array_map('trim', explode(',', $inside));
        $currentColor = 'Multi-color';
    }
@endphp

<div class="mb-8">
    <div class="relative flex items-center justify-between">
        <div class="absolute left-0 top-1/2 h-1 w-full -translate-y-1/2 transform bg-gray-200 -z-10"></div>

        <div class="flex flex-col items-center">
            <div id="step-1-dot" class="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-sm font-bold text-white">
                1
            </div>
            <span id="step-1-label" class="mt-1 text-xs font-bold text-red-600">
                Contact
            </span>
        </div>

        <div class="flex flex-col items-center">
            <div id="step-2-dot" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-sm font-bold text-gray-500">
                2
            </div>
            <span id="step-2-label" class="mt-1 text-xs font-bold text-gray-500">
                Item
            </span>
        </div>

        <div class="flex flex-col items-center">
            <div id="step-3-dot" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-sm font-bold text-gray-500">
                3
            </div>
            <span id="step-3-label" class="mt-1 text-xs font-bold text-gray-500">
                Lost Info
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
        ? route('staff.lost-items.update', $lostItem->id)
        : route('staff.lost-items.store') }}"
    enctype="multipart/form-data"
>
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    <div id="step1" class="step-section">
        <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
            👤 Step 1: Passenger Contact
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-gray-700">Passenger Name *</label>
                <input
                    type="text"
                    name="passenger_name"
                    value="{{ old('passenger_name', $lostItem->passenger_name ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Phone Number *</label>
                <input
                    type="text"
                    name="passenger_phone"
                    value="{{ old('passenger_phone', $lostItem->passenger_phone ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Email Address *</label>
                <input
                    type="email"
                    name="passenger_email"
                    value="{{ old('passenger_email', $lostItem->passenger_email ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button
                type="button"
                onclick="nextStep(2)"
                class="rounded-md bg-red-600 px-6 py-2 font-bold text-white transition hover:bg-red-700"
            >
                Next ➡️
            </button>
        </div>
    </div>

    <div id="step2" class="step-section hidden">
        <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
            📦 Step 2: Item Details
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Item Name *</label>
                <input
                    type="text"
                    name="item_name"
                    value="{{ old('item_name', $lostItem->item_name ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Category *</label>
                <select
                    name="category"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
                    @foreach (['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $lostItem->category ?? '') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Brand (Optional)</label>
                <input
                    type="text"
                    name="brand"
                    value="{{ old('brand', $lostItem->brand ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Serial Number / Unique ID (Optional)</label>
                <input
                    type="text"
                    name="serial_number"
                    value="{{ old('serial_number', $lostItem->serial_number ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Color *</label>
                <select
                    id="mainColorSelect"
                    name="color"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
                    <option value="">Select Main Color</option>

                    @foreach (['Black', 'White', 'Grey', 'Silver', 'Gold', 'Red', 'Blue', 'Brown', 'Green', 'Purple', 'Pink', 'Orange', 'Yellow'] as $color)
                        <option value="{{ $color }}" {{ $currentColor === $color ? 'selected' : '' }}>
                            {{ $color }}
                        </option>
                    @endforeach

                    <option value="Multi-color" {{ $currentColor === 'Multi-color' ? 'selected' : '' }}>
                        🎨 Multi-color (Select details below)
                    </option>
                </select>

                <div
                    id="multiColorOptions"
                    class="{{ $currentColor === 'Multi-color' ? '' : 'hidden' }} mt-3 rounded-md border border-gray-200 bg-gray-50 p-3"
                >
                    <label class="mb-2 block text-xs font-bold uppercase text-gray-500">
                        Please tick all colors involved:
                    </label>

                    <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
                        @foreach (['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple', 'Pink', 'Grey', 'Brown', 'Silver', 'Gold'] as $subColor)
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="sub_colors[]"
                                    value="{{ $subColor }}"
                                    class="rounded text-red-600 focus:ring-red-500"
                                    {{ in_array($subColor, $currentSubColors) ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $subColor }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Photo (Optional)</label>
                <input
                    type="file"
                    name="image"
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 p-2"
                >
            </div>

            @if (!empty($lostItem?->image_path))
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Current Image</label>
                    <img
                        src="{{ asset('storage/' . $lostItem->image_path) }}"
                        alt="Current Image"
                        class="h-40 w-40 rounded-lg border object-cover shadow-sm"
                    >
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-between">
            <button
                type="button"
                onclick="prevStep(1)"
                class="rounded-md bg-gray-500 px-6 py-2 font-bold text-white transition hover:bg-gray-600"
            >
                ⬅️ Previous
            </button>

            <button
                type="button"
                onclick="nextStep(3)"
                class="rounded-md bg-red-600 px-6 py-2 font-bold text-white transition hover:bg-red-700"
            >
                Next ➡️
            </button>
        </div>
    </div>

    <div id="step3" class="step-section hidden">
        <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
            📍 Step 3: Where &amp; When?
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-gray-700">Where was it lost? (Location) *</label>
                <select
                    name="lost_location"
                    id="lostLocationSelect"
                    onchange="toggleFlightInput()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
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
                        'Parking Lot'
                    ] as $location)
                        <option
                            value="{{ $location }}"
                            {{ old('lost_location', $lostItem->lost_location ?? '') === $location ? 'selected' : '' }}
                        >
                            {{ $location === 'Boarding Gate' ? 'Boarding Gate (General)' : ($location === 'Restaurant/Shop' ? 'Restaurant / Duty Free Shop' : ($location === 'Airplane Cabin' ? 'Airplane Cabin (On Board)' : ($location === 'Restroom' ? 'Restroom / Toilet' : ($location === 'Lounge' ? 'VIP Lounge' : $location)))) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div
                id="flightNumberDiv"
                class="{{ old('lost_location', $lostItem->lost_location ?? '') === 'Airplane Cabin' ? '' : 'hidden' }}"
            >
                <label class="block text-sm font-bold text-red-600">Flight Number *</label>
                <input
                    type="text"
                    name="flight_number"
                    id="flightInput"
                    value="{{ old('flight_number', $lostItem->flight_number ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-red-500 focus:ring-red-500"
                    placeholder="e.g. MH370"
                >
                <p class="mt-1 text-xs text-gray-500">Required for items lost on board.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Approx. Lost Time *</label>
                <input
                    type="datetime-local"
                    name="lost_time"
                    value="{{ old('lost_time', isset($lostItem->lost_time) ? \Carbon\Carbon::parse($lostItem->lost_time)->format('Y-m-d\TH:i') : '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Description / Details</label>
                <textarea
                    name="description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                >{{ old('description', $lostItem->description ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-between border-t pt-4">
            <button
                type="button"
                onclick="prevStep(2)"
                class="rounded-md bg-gray-500 px-6 py-2 font-bold text-white transition hover:bg-gray-600"
            >
                ⬅️ Previous
            </button>

            <div class="flex gap-3">
                <a
                    href="{{ route('staff.lost-items.index') }}"
                    class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="transform rounded-md bg-red-600 px-8 py-3 font-bold text-white shadow-lg transition hover:scale-105 hover:bg-red-700"
                >
                    {{ $mode === 'edit' ? 'Save Changes' : '🚨 Submit Report' }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function showStep(step) {
        document.querySelectorAll('.step-section').forEach(el => el.classList.add('hidden'));
        document.getElementById('step' + step).classList.remove('hidden');

        for (let i = 1; i <= 3; i++) {
            const dot = document.getElementById('step-' + i + '-dot');
            const label = document.getElementById('step-' + i + '-label');

            if (i < step) {
                dot.className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm';
                dot.innerHTML = '✓';
                if (label) label.className = 'text-xs font-bold mt-1 text-green-600';
            } else if (i === step) {
                dot.className = 'w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-md scale-110';
                dot.innerHTML = i;
                if (label) label.className = 'text-xs font-bold mt-1 text-red-600';
            } else {
                dot.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm';
                dot.innerHTML = i;
                if (label) label.className = 'text-xs font-bold mt-1 text-gray-500';
            }
        }
    }

    function nextStep(s) {
        showStep(s);
    }

    function prevStep(s) {
        showStep(s);
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
        const location = document.getElementById('lostLocationSelect')?.value;
        const flightDiv = document.getElementById('flightNumberDiv');
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
        if (colorSelect) colorSelect.addEventListener('change', toggleMultiColor);

        const locationSelect = document.getElementById('lostLocationSelect');
        if (locationSelect) locationSelect.addEventListener('change', toggleFlightInput);
    });
</script>