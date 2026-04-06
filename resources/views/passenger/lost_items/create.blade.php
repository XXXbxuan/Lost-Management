<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Report a Lost Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- WIZARD STEPS INDICATOR --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between relative">
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                            
                            <div class="flex flex-col items-center">
                                <div id="step-1-dot" class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                                <span class="text-xs font-bold mt-1 text-red-600">My Info</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div id="step-2-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">2</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Item</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div id="step-3-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">3</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Details</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('staff.lost-items.store') }}" enctype="multipart/form-data">
                        @csrf
                        {{-- HIDDEN FIELDS FOR LOGIC --}}
                        <input type="hidden" name="status" value="Lost">
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        
                        {{-- STEP 1: CONTACT (Editable Now) --}}
                        <div id="step1" class="step-section">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">👤 Step 1: Confirm Contact Info</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">My Name</label>
                                    {{-- 🟢 FIXED: Removed 'readonly' and gray styles --}}
                                    <input type="text" name="passenger_name" value="{{ Auth::user()->name }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                </div>
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Email Address</label>
                                    {{-- 🟢 FIXED: Removed 'readonly' and gray styles --}}
                                    <input type="email" name="passenger_email" value="{{ Auth::user()->email }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Phone Number *</label>
                                    <input type="text" name="passenger_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required placeholder="Enter your mobile number">
                                </div>
                            </div>
                            <div class="flex justify-end mt-6">
                                <button type="button" onclick="nextStep(2)" class="bg-red-600 text-white px-6 py-2 rounded-md font-bold hover:bg-red-700 transition">Next ➡️</button>
                            </div>
                        </div>

                        {{-- STEP 2: ITEM DETAILS --}}
                        <div id="step2" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📦 Step 2: What did you lose?</h3>
                            <x-item-details-form />
                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="prevStep(1)" class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600 transition">⬅️ Previous</button>
                                <button type="button" onclick="nextStep(3)" class="bg-red-600 text-white px-6 py-2 rounded-md font-bold hover:bg-red-700 transition">Next ➡️</button>
                            </div>
                        </div>

                        {{-- STEP 3: LOCATION & TIME --}}
                        <div id="step3" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📍 Step 3: Where & When?</h3>
                            
                            {{-- (PASTED FROM YOUR FILE - Simplified) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Where was it lost? *</label>
                                    <select name="lost_location" id="lostLocationSelect" onchange="toggleFlightInput()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">-- Select Location --</option>
                                        <option value="Check-in Counter">Check-in Counter</option>
                                        <option value="Security Checkpoint">Security Checkpoint</option>
                                        <option value="Departure Hall">Departure Hall</option>
                                        <option value="Arrival Hall">Arrival Hall</option>
                                        <option value="Boarding Gate">Boarding Gate</option>
                                        <option value="Airplane Cabin">Airplane Cabin (On Board)</option>
                                        <option value="Restroom">Restroom</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>

                                <div id="flightNumberDiv" class="hidden">
                                    <label class="block font-bold text-sm text-gray-700 text-red-600">Flight Number *</label>
                                    <input type="text" name="flight_number" id="flightInput" class="mt-1 block w-full rounded-md border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="e.g. MH370">
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Approx. Lost Time *</label>
                                    <input type="datetime-local" name="lost_time" class="mt-1 block w-full rounded-md border-gray-300" required>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Additional Description</label>
                                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300" placeholder="Any special marks, scratches, or contents inside?"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-between mt-8 pt-4 border-t">
                                <button type="button" onclick="prevStep(2)" class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600 transition">⬅️ Previous</button>
                                <button type="submit" class="bg-red-600 text-white px-8 py-3 rounded-md font-bold hover:bg-red-700 shadow-lg transform hover:scale-105 transition">🚨 Submit Report</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- JAVASCRIPT FOR WIZARD --}}
    <script>
        function showStep(step) {
            document.querySelectorAll('.step-section').forEach(el => el.classList.add('hidden'));
            document.getElementById('step' + step).classList.remove('hidden');
            
            // Update dots
            for(let i=1; i<=3; i++) {
                const dot = document.getElementById('step-'+i+'-dot');
                if(i < step) { 
                    dot.className = "w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm"; 
                    dot.innerHTML = "✓"; 
                } else if(i === step) { 
                    dot.className = "w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-md scale-110"; 
                    dot.innerHTML = i; 
                } else { 
                    dot.className = "w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm"; 
                    dot.innerHTML = i; 
                }
            }
        }
        function nextStep(s) { showStep(s); }
        function prevStep(s) { showStep(s); }

        function toggleFlightInput() {
            const location = document.getElementById('lostLocationSelect').value;
            const flightDiv = document.getElementById('flightNumberDiv');
            const flightInput = document.getElementById('flightInput');
            if (location === 'Airplane Cabin') {
                flightDiv.classList.remove('hidden');
                flightInput.setAttribute('required', 'required');
            } else {
                flightDiv.classList.add('hidden');
                flightInput.removeAttribute('required');
            }
        }
    </script>
</x-app-layout>