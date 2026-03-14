<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('🚨 Report Lost Item (Passenger)') }}
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
                                <div id="step-1-dot" class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                                <span class="text-xs font-bold mt-1 text-red-600">Contact</span>
                            </div>
                            
                            <div class="flex flex-col items-center">
                                <div id="step-2-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">2</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Item</span>
                            </div>
                            
                            <div class="flex flex-col items-center">
                                <div id="step-3-dot" class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">3</div>
                                <span class="text-xs font-bold mt-1 text-gray-500">Lost Info</span>
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

                    <form method="POST" action="{{ route('staff.lost-items.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div id="step1" class="step-section">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">👤 Step 1: Passenger Contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Passenger Name *</label>
                                    <input type="text" name="passenger_name" value="{{ old('passenger_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                </div>
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Phone Number *</label>
                                    <input type="text" name="passenger_phone" value="{{ old('passenger_phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block font-bold text-sm text-gray-700">Email Address *</label>
                                    <input type="email" name="passenger_email" value="{{ old('passenger_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                </div>
                            </div>
                            <div class="flex justify-end mt-6">
                                <button type="button" onclick="nextStep(2)" class="bg-red-600 text-white px-6 py-2 rounded-md font-bold hover:bg-red-700 transition">Next ➡️</button>
                            </div>
                        </div>

                        <div id="step2" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📦 Step 2: Item Details</h3>
                            
                            <x-item-details-form />

                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="prevStep(1)" class="bg-gray-500 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-600 transition">⬅️ Previous</button>
                                <button type="button" onclick="nextStep(3)" class="bg-red-600 text-white px-6 py-2 rounded-md font-bold hover:bg-red-700 transition">Next ➡️</button>
                            </div>
                        </div>

                        <div id="step3" class="step-section hidden">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📍 Step 3: Where & When?</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Where was it lost? (Location) *</label>
                                    <select name="lost_location" id="lostLocationSelect" onchange="toggleFlightInput()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                                        <option value="">-- Select Location --</option>
                                        <option value="Terminal 1">Terminal 1</option>
                                        <option value="Terminal 2">Terminal 2</option>
                                        <option value="Check-in Counter">Check-in Counter</option>
                                        <option value="Security Checkpoint">Security Checkpoint</option>
                                        <option value="Departure Hall">Departure Hall</option>
                                        <option value="Arrival Hall">Arrival Hall</option>
                                        <option value="Boarding Gate">Boarding Gate (General)</option>
                                        <option value="Baggage Claim">Baggage Claim</option>
                                        <option value="Restroom">Restroom / Toilet</option>
                                        <option value="Restaurant/Shop">Restaurant / Duty Free Shop</option>
                                        <option value="Airplane Cabin">Airplane Cabin (On Board)</option>
                                        <option value="Lounge">VIP Lounge</option>
                                        <option value="Parking Lot">Parking Lot</option>
                                    </select>
                                </div>

                                <div id="flightNumberDiv" class="hidden">
                                    <label class="block font-bold text-sm text-gray-700 text-red-600">Flight Number *</label>
                                    <input type="text" name="flight_number" id="flightInput" class="mt-1 block w-full rounded-md border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="e.g. MH370">
                                    <p class="text-xs text-gray-500 mt-1">Required for items lost on board.</p>
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700">Approx. Lost Time *</label>
                                    <input type="datetime-local" name="lost_time" class="mt-1 block w-full rounded-md border-gray-300" required>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Description / Details</label>
                                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
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
    
    <script>
        // 1. 步骤切换逻辑 (Wizard Navigation)
        function showStep(step) {
            // 隐藏所有步骤
            document.querySelectorAll('.step-section').forEach(el => el.classList.add('hidden'));
            // 显示当前步骤
            document.getElementById('step' + step).classList.remove('hidden');
            
            // 更新顶部圆圈颜色
            for(let i=1; i<=3; i++) {
                const dot = document.getElementById('step-'+i+'-dot');
                if(i < step) { 
                    // 之前的步骤 (绿色勾勾)
                    dot.className = "w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm"; 
                    dot.innerHTML = "✓"; 
                }
                else if(i === step) { 
                    // 当前步骤 (红色高亮)
                    dot.className = "w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-md scale-110"; 
                    dot.innerHTML = i; 
                }
                else { 
                    // 还没到的步骤 (灰色)
                    dot.className = "w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm"; 
                    dot.innerHTML = i; 
                }
            }
        }
        function nextStep(s) { showStep(s); }
        function prevStep(s) { showStep(s); }
        
        // 2. 颜色多选逻辑 (组件 x-item-details-form 需要用到这个)
        function toggleMultiColor() {
            const mainColor = document.getElementById('mainColorSelect').value;
            const optionsDiv = document.getElementById('multiColorOptions');
            if (mainColor === 'Multi-color') { 
                optionsDiv.classList.remove('hidden'); 
            } else { 
                optionsDiv.classList.add('hidden'); 
            }
        }

        // 3. 航班号显示逻辑 (Step 3 新增)
        function toggleFlightInput() {
            const location = document.getElementById('lostLocationSelect').value;
            const flightDiv = document.getElementById('flightNumberDiv');
            const flightInput = document.getElementById('flightInput');

            if (location === 'Airplane Cabin') {
                // 如果选了机舱，显示输入框，并设为必填
                flightDiv.classList.remove('hidden');
                flightInput.setAttribute('required', 'required');
            } else {
                // 没选机舱，隐藏输入框，取消必填，并清空内容
                flightDiv.classList.add('hidden');
                flightInput.removeAttribute('required');
                flightInput.value = ''; 
            }
        }
    </script>
</x-app-layout>