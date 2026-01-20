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
                    
                    <div class="mb-8">
                        <div class="flex items-center justify-between relative">
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                            
                            <div class="flex flex-col items-center">
                                <div id="step-1-dot" class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm transition-colors duration-300">1</div>
                                <span class="text-xs font-bold mt-1 text-indigo-600">Details</span>
                            </div>
                            
                            <div class="flex flex-col items-center">
                                <div id="step-2-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm transition-colors duration-300">2</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Location</span>
                            </div>

                            <div class="flex flex-col items-center">
                                <div id="step-3-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm transition-colors duration-300">3</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Storage</span>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-md border border-red-200">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.lost-items.store') }}" enctype="multipart/form-data" id="wizardForm">
                        @csrf
                        
                        <div id="step1" class="step-section">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📦 Step 1: Item Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Item Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="item_name" value="{{ old('item_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Category <span class="text-red-500">*</span></label>
                                    <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach(['Electronics', 'Wallet', 'Identification', 'Baggage', 'Clothing', 'Jewelry', 'Keys', 'Others'] as $cat)
                                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Brand (Optional)</label>
                                    <input type="text" name="brand" value="{{ old('brand') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Apple, Nike">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Serial Number / Unique ID (Optional)</label>
                                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. IMEI for phones, Card Number for IDs">
                                    <p class="text-xs text-gray-500 mt-1">Unique IDs help match items with 100% accuracy.</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Color <span class="text-red-500">*</span></label>
                                    <select name="color" id="mainColorSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="toggleMultiColor()">
                                        <option value="">Select Main Color</option>
                                        @foreach(['Black', 'White', 'Grey', 'Silver', 'Gold', 'Red', 'Blue', 'Brown', 'Green', 'Purple', 'Pink', 'Orange', 'Yellow'] as $color)
                                            <option value="{{ $color }}">{{ $color }}</option>
                                        @endforeach
                                        <option value="Multi-color">🎨 Multi-color (Select details below)</option>
                                        <option value="Others">Others</option>
                                    </select>

                                    <div id="multiColorOptions" class="hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Please tick all colors involved:</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            @foreach(['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple', 'Pink', 'Grey', 'Brown', 'Silver', 'Gold'] as $subColor)
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="sub_colors[]" value="{{ $subColor }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $subColor }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Photo (Optional)</label>
                                    <input type="file" name="image" class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-50">
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="button" onclick="nextStep(2)" class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold hover:bg-indigo-700">Next ➡️</button>
                            </div>
                        </div>

                        <div id="step2" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📍 Step 2: Location & Time</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Found Location (Area) <span class="text-red-500">*</span></label>
                                    <select id="locationSelect" name="found_location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="toggleFlightInput()">
                                        <option value="">Select Area</option>
                                        <option value="Airplane Cabin">✈️ Airplane Cabin (In-Flight)</option>
                                        <option value="Terminal 1">Terminal 1</option>
                                        <option value="Terminal 2">Terminal 2</option>
                                        <option value="Departure Hall">Departure Hall</option>
                                        <option value="Arrival Hall">Arrival Hall</option>
                                        <option value="Security Check">Security Check</option>
                                        <option value="Restroom">Restroom</option>
                                        <option value="Food Court">Food Court</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>

                                <div id="flightInputDiv" class="hidden bg-blue-50 p-2 rounded border border-blue-200">
                                    <label class="block font-bold text-sm text-blue-800">Flight Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="flight_number" class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. MH370">
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Found Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="found_time" value="{{ old('found_time', now()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Specific Description</label>
                                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Under seat 12A"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="prevStep(1)" class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600">⬅️ Previous</button>
                                <button type="button" onclick="nextStep(3)" class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold hover:bg-indigo-700">Next ➡️</button>
                            </div>
                        </div>

                        <div id="step3" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">🔐 Step 3: Storage Assignment (Internal)</h3>
                            
                            <div class="bg-gray-50 p-6 rounded-md border border-gray-200 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Zone</label>
                                        <select id="storageZone" class="mt-1 block w-full rounded-md border-gray-300 text-sm" onchange="updateStoragePreview()">
                                            <option value="GEN">General (GEN)</option>
                                            <option value="VAULT">Vault (High Value)</option>
                                            <option value="BAG">Baggage Room</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Shelf</label>
                                        <select id="storageShelf" class="mt-1 block w-full rounded-md border-gray-300 text-sm" onchange="updateStoragePreview()">
                                            <option value="S1">Shelf 1</option>
                                            <option value="S2">Shelf 2</option>
                                            <option value="S3">Shelf 3</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Slot</label>
                                        <select id="storageSlot" class="mt-1 block w-full rounded-md border-gray-300 text-sm" onchange="updateStoragePreview()">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}">Slot {{ sprintf('%02d', $i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-6 text-center">
                                    <span class="text-xs text-gray-500">Assigned Storage ID:</span>
                                    <div class="text-2xl font-mono font-bold text-indigo-600 tracking-wider mt-1" id="storagePreview">GEN-S1-01</div>
                                    <input type="hidden" name="storage_location" id="finalStorageInput" value="GEN-S1-01">
                                </div>
                            </div>
                            
                            <div class="flex justify-between mt-8 pt-4 border-t">
                                <button type="button" onclick="prevStep(2)" class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600">⬅️ Previous</button>
                                
                                <button type="submit" class="bg-black text-white px-8 py-3 rounded-md font-bold hover:bg-gray-800 shadow-lg transform hover:scale-105 transition">
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
            document.getElementById('step' + stepNumber).classList.remove('hidden');
            updateIndicators(stepNumber);
        }

        function nextStep(targetStep) {
            showStep(targetStep);
        }

        function prevStep(targetStep) {
            showStep(targetStep);
        }

        function updateIndicators(currentStep) {
            for(let i=1; i<=3; i++) {
                const dot = document.getElementById('step-'+i+'-dot');
                if(i < currentStep) {
                    dot.className = "w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm";
                    dot.innerHTML = "✓";
                } else if(i === currentStep) {
                    dot.className = "w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md scale-110";
                    dot.innerHTML = i;
                } else {
                    dot.className = "w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm";
                    dot.innerHTML = i;
                }
            }
        }

        function toggleMultiColor() {
            const mainColor = document.getElementById('mainColorSelect').value;
            const optionsDiv = document.getElementById('multiColorOptions');
            if (mainColor === 'Multi-color') {
                optionsDiv.classList.remove('hidden');
            } else {
                optionsDiv.classList.add('hidden');
            }
        }

        function toggleFlightInput() {
            const location = document.getElementById('locationSelect').value;
            const flightDiv = document.getElementById('flightInputDiv');
            if (location === 'Airplane Cabin') {
                flightDiv.classList.remove('hidden');
            } else {
                flightDiv.classList.add('hidden');
            }
        }

        function updateStoragePreview() {
            const zone = document.getElementById('storageZone').value;
            const shelf = document.getElementById('storageShelf').value;
            const slot = document.getElementById('storageSlot').value;
            const finalID = `${zone}-${shelf}-${slot}`;
            document.getElementById('storagePreview').innerText = finalID;
            document.getElementById('finalStorageInput').value = finalID;
        }

        updateStoragePreview();
    </script>
</x-app-layout>