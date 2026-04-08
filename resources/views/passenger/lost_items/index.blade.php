<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Report a Lost Item') }}
        </h2>
    </x-slot>

    @php
        $currentColor = old('color', '');
        $currentSubColors = old('sub_colors', []);

        if (empty($currentSubColors) && !empty(old('color')) && str_starts_with(old('color'), 'Multi-color (')) {
            $inside = trim(str_replace(['Multi-color (', ')'], '', old('color')));
            $currentSubColors = array_map('trim', explode(',', $inside));
            $currentColor = 'Multi-color';
        }

        $currentStep = 1;

        if ($errors->any()) {
            if (
                $errors->has('item_name') ||
                $errors->has('category') ||
                $errors->has('brand') ||
                $errors->has('serial_number') ||
                $errors->has('color') ||
                $errors->has('sub_colors') ||
                $errors->has('image')
            ) {
                $currentStep = 2;
            }

            if (
                $errors->has('lost_location') ||
                $errors->has('flight_number') ||
                $errors->has('lost_time') ||
                $errors->has('description')
            ) {
                $currentStep = 3;
            }
        } elseif (
            old('lost_location') ||
            old('lost_time') ||
            old('description')
        ) {
            $currentStep = 3;
        } elseif (
            old('item_name') ||
            old('category') ||
            old('brand') ||
            old('serial_number') ||
            old('color')
        ) {
            $currentStep = 2;
        }
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-600 shadow-sm">
                    <div class="font-bold mb-2">Please check the form:</div>
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-8">
                        <div class="flex items-center justify-between relative">
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>

                            <div class="flex flex-col items-center">
                                <div id="step-1-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">1</div>
                                <span id="step-1-label" class="text-xs font-bold mt-1 text-gray-500">My Info</span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-2-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">2</div>
                                <span id="step-2-label" class="text-xs font-bold mt-1 text-gray-500">Item</span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-3-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">3</div>
                                <span id="step-3-label" class="text-xs font-bold mt-1 text-gray-500">Details</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('passenger.report.store') }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="status" value="Lost">
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                        <div id="step1" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">👤 Step 1: Confirm Contact Info</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">My Name *</label>
                                    <input
                                        type="text"
                                        name="passenger_name"
                                        value="{{ old('passenger_name', Auth::user()->name) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Email Address *</label>
                                    <input
                                        type="email"
                                        name="passenger_email"
                                        value="{{ old('passenger_email', Auth::user()->email) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Phone Number *</label>
                                    <input
                                        type="text"
                                        name="passenger_phone"
                                        value="{{ old('passenger_phone') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                        placeholder="Enter your mobile number"
                                    >
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button
                                    type="button"
                                    onclick="nextStep(2)"
                                    class="bg-red-600 text-white px-6 py-2 rounded-md font-bold hover:bg-red-700 transition"
                                >
                                    Next ➡️
                                </button>
                            </div>
                        </div>

                        <div id="step2" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📦 Step 2: What did you lose?</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Item Name *</label>
                                    <input
                                        type="text"
                                        name="item_name"
                                        value="{{ old('item_name') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Category *</label>
                                    <select
                                        name="category"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                        <option value="">-- Select Category --</option>
                                        @foreach(['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Brand (Optional)</label>
                                    <input
                                        type="text"
                                        name="brand"
                                        value="{{ old('brand') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Serial Number / Unique ID (Optional)</label>
                                    <input
                                        type="text"
                                        name="serial_number"
                                        value="{{ old('serial_number') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Unique IDs help match items with better accuracy.</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Color *</label>
                                    <select
                                        id="mainColorSelect"
                                        name="color"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
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
                                    <label class="block font-bold text-sm text-gray-700">Photo (Optional)</label>
                                    <input
                                        type="file"
                                        name="image"
                                        class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 p-2"
                                    >
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button
                                    type="button"
                                    onclick="prevStep(1)"
                                    class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600 transition"
                                >
                                    ⬅️ Previous
                                </button>

                                <button
                                    type="button"
                                    onclick="nextStep(3)"
                                    class="bg-red-600 text-white px-6 py-2 rounded-md font-bold hover:bg-red-700 transition"
                                >
                                    Next ➡️
                                </button>
                            </div>
                        </div>

                        <div id="step3" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📍 Step 3: Where & When?</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Where was it lost? *</label>
                                    <select
                                        name="lost_location"
                                        id="lostLocationSelect"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
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
                                            'Parking Lot',
                                            'Others'
                                        ] as $location)
                                            <option value="{{ $location }}" {{ old('lost_location') === $location ? 'selected' : '' }}>
                                                {{ $location === 'Boarding Gate' ? 'Boarding Gate (General)' : ($location === 'Restaurant/Shop' ? 'Restaurant / Duty Free Shop' : ($location === 'Airplane Cabin' ? 'Airplane Cabin (On Board)' : ($location === 'Restroom' ? 'Restroom / Toilet' : ($location === 'Lounge' ? 'VIP Lounge' : $location)))) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="flightNumberDiv" class="{{ old('lost_location') === 'Airplane Cabin' ? '' : 'hidden' }}">
                                    <label class="block font-bold text-sm text-gray-700 text-red-600">Flight Number *</label>
                                    <input
                                        type="text"
                                        name="flight_number"
                                        id="flightInput"
                                        value="{{ old('flight_number') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 focus:border-red-500 focus:ring-red-500"
                                        placeholder="e.g. MH370"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Required for items lost on board.</p>
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Approx. Lost Time *</label>
                                    <input
                                        type="datetime-local"
                                        name="lost_time"
                                        value="{{ old('lost_time') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Additional Description</label>
                                    <textarea
                                        name="description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Any special marks, scratches, or contents inside?"
                                    >{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="flex justify-between mt-8 pt-4 border-t">
                                <button
                                    type="button"
                                    onclick="prevStep(2)"
                                    class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600 transition"
                                >
                                    ⬅️ Previous
                                </button>

                                <button
                                    type="submit"
                                    class="bg-red-600 text-white px-8 py-3 rounded-md font-bold hover:bg-red-700 shadow-lg transform hover:scale-105 transition"
                                >
                                    🚨 Submit Report
                                </button>
                            </div>
                        </div>
                    </form>

                    
                </div>
            </div>
        </div>
    </div>

    <script>
        function showStep(step) {
            document.querySelectorAll('.step-section').forEach(el => el.classList.add('hidden'));

            const section = document.getElementById('step' + step);
            if (section) {
                section.classList.remove('hidden');
            }

            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('step-' + i + '-dot');
                const label = document.getElementById('step-' + i + '-label');

                if (!dot || !label) continue;

                if (i < step) {
                    dot.className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm';
                    dot.innerHTML = '✓';
                    label.className = 'text-xs font-bold mt-1 text-green-600';
                } else if (i === step) {
                    dot.className = 'w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-md scale-110';
                    dot.innerHTML = i;
                    label.className = 'text-xs font-bold mt-1 text-red-600';
                } else {
                    dot.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm';
                    dot.innerHTML = i;
                    label.className = 'text-xs font-bold mt-1 text-gray-500';
                }
            }
        }

        function nextStep(step) {
            showStep(step);
        }

        function prevStep(step) {
            showStep(step);
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
            showStep({{ $currentStep }});
            toggleMultiColor();
            toggleFlightInput();

            const colorSelect = document.getElementById('mainColorSelect');
            if (colorSelect) {
                colorSelect.addEventListener('change', toggleMultiColor);
            }

            const locationSelect = document.getElementById('lostLocationSelect');
            if (locationSelect) {
                locationSelect.addEventListener('change', toggleFlightInput);
            }
        });
    </script>
</x-app-layout>