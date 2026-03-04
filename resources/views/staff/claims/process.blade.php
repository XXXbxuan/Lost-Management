<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔄 Claim Process & Handover
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                @include('staff.claims.partials.timeline') 
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-1 bg-white shadow-sm rounded-lg p-6 h-fit">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">📦 Item Info</h3>
                    @if($foundItem->image_path)
                        <img src="{{ Storage::url($foundItem->image_path) }}" class="w-full h-40 object-cover rounded mb-4 border">
                    @endif
                    <p class="text-sm"><strong>Name:</strong> {{ $foundItem->item_name }}</p>
                    <p class="text-sm"><strong>Ref ID:</strong> #{{ $foundItem->id }}</p>
                    <p class="text-sm"><strong>Storage:</strong> <span class="bg-gray-200 px-2 rounded">{{ $foundItem->storage_location }}</span></p>
                </div>

                <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-6">
                    
                    {{-- 狀況 1：還沒預約時間 --}}
                    @if(is_null($match->appointment_at))
                        
                        @if($match->suggested_time_1 || $match->suggested_time_2)
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <h3 class="text-blue-800 font-bold text-lg">💡 Passenger's Suggestion</h3>
                                </div>
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full font-bold">
                                    ❌ Previously Rejected
                                </span>
                            </div>
                            
                            <div class="space-y-3">
                                @if($match->suggested_time_1)
                                <div class="flex items-center justify-between bg-white p-3 rounded border border-blue-100 shadow-sm">
                                    <span class="text-sm text-gray-700">
                                        <span class="font-bold text-blue-600 inline-block w-20">Option 1:</span> 
                                        {{ \Carbon\Carbon::parse($match->suggested_time_1)->format('d M Y, h:i A') }}
                                    </span>
                                    <button type="button" 
                                            onclick="fillSuggestedTime('{{ \Carbon\Carbon::parse($match->suggested_time_1)->format('Y-m-d\TH:i') }}')"
                                            class="bg-green-500 hover:bg-green-600 text-white text-xs px-4 py-1.5 rounded-full transition-all shadow-sm">
                                        ✔ Use This
                                    </button>
                                </div>
                                @endif

                                @if($match->suggested_time_2)
                                <div class="flex items-center justify-between bg-white p-3 rounded border border-blue-100 shadow-sm">
                                    <span class="text-sm text-gray-700">
                                        <span class="font-bold text-blue-600 inline-block w-20">Option 2:</span> 
                                        {{ \Carbon\Carbon::parse($match->suggested_time_2)->format('d M Y, h:i A') }}
                                    </span>
                                    <button type="button" 
                                            onclick="fillSuggestedTime('{{ \Carbon\Carbon::parse($match->suggested_time_2)->format('Y-m-d\TH:i') }}')"
                                            class="bg-green-500 hover:bg-green-600 text-white text-xs px-4 py-1.5 rounded-full transition-all shadow-sm">
                                        ✔ Use This
                                    </button>
                                </div>
                                @endif
                            </div>

                            @if($match->suggested_remarks)
                            <div class="mt-4 text-sm text-gray-600 bg-blue-100/50 p-3 rounded border border-blue-100">
                                <span class="font-bold text-gray-700">Note:</span> "{{ $match->suggested_remarks }}"
                            </div>
                            @endif
                        </div>

                        <script>
                            function fillSuggestedTime(datetimeValue) {
                                const [datePart, timePart] = datetimeValue.split('T');
                                const dateInput = document.querySelector('input[name="appointment_date"]'); 
                                const timeInput = document.querySelector('input[name="appointment_time"]');

                                if(dateInput && timeInput) {
                                    dateInput.value = datePart;
                                    timeInput.value = timePart;
                                    
                                    dateInput.classList.add('ring-2', 'ring-green-500', 'border-green-500');
                                    timeInput.classList.add('ring-2', 'ring-green-500', 'border-green-500');
                                    setTimeout(() => {
                                        dateInput.classList.remove('ring-2', 'ring-green-500', 'border-green-500');
                                        timeInput.classList.remove('ring-2', 'ring-green-500', 'border-green-500');
                                    }, 800);
                                }
                            }
                        </script>
                        @endif
                        
                        <h3 class="text-lg font-bold text-blue-800 mb-4">📅 Step 1: Schedule Pickup</h3>
                        <p class="text-sm text-gray-500 mb-6">Please contact the passenger and agree on a pickup time.</p>

                        @if ($errors->any())
                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Oops! Please fix these errors:</h3>
                                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <form action="{{ route('staff.claims.schedule') }}" method="POST">
                            @csrf
                            <input type="hidden" name="match_id" value="{{ $match->id }}">
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Date</label>
                                    <input type="date" name="appointment_date" required class="w-full rounded border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Time</label>
                                    <input type="time" name="appointment_time" required class="w-full rounded border-gray-300 shadow-sm">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">
                                Set Appointment & Send Email
                            </button>
                        </form>

                    {{-- 狀況 2：預約了，等待旅客 Email 確認 --}}
                    @elseif(!$match->is_confirmed)
                        <div class="text-center py-8">
                            <div class="text-5xl mb-4">📩</div>
                            <h3 class="text-xl font-bold text-gray-800">Appointment Set! Waiting for Confirmation...</h3>
                            <p class="text-gray-500 mt-2">The passenger has received an email. They must click the link to confirm.</p>
                            
                            <div class="mt-8 bg-yellow-50 border border-yellow-200 p-4 rounded text-left shadow-sm">
                                <p class="text-xs font-bold text-yellow-800 uppercase mb-1">🔧 Developer Tool (Simulated Email Link):</p>
                                <p class="text-sm text-gray-600">User received this link:</p>
                                <a href="{{ route('pickup.confirm', ['token' => $match->verification_token]) }}" target="_blank" class="text-blue-600 underline font-mono break-all">
                                    {{ route('pickup.confirm', ['token' => $match->verification_token]) }}
                                </a>
                            </div>
                        </div>

                    {{-- 🌟 狀況 3：旅客已確認，進入 QR Code 掃描與最終結案流程 --}}
                    @else
                        
                        {{-- 🔑 3A：檢查是否從照片對比圖 (verify_action) 帶著鑰匙回來 --}}
                        @if(request('step') === 'enter_ic')
                            
                            <h3 class="text-lg font-bold text-green-800 mb-4">🏁 Step 3: Final Handover</h3>
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-6 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Photos Verified. Proceed to check physical ID.
                            </div>

                            <form action="{{ route('staff.claims.complete', $match->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="lostId" value="{{ $lostItem->id }}">
                                <input type="hidden" name="foundId" value="{{ $foundItem->id }}">
                                <input type="hidden" name="claimerName" value="{{ $lostItem->passenger_name }}">
                                <input type="hidden" name="claimerPhone" value="{{ $lostItem->passenger_phone }}">
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Verify Identity Document (IC / Passport) <span class="text-red-500">*</span></label>
                                    <input type="text" name="claimerIcPassport" required class="w-full rounded border-gray-300 shadow-sm text-lg" placeholder="e.g. 990101-14-xxxx">
                                </div>

                                <div class="flex items-start mb-6 bg-gray-50 p-3 border border-gray-200">
                                    <div class="flex items-center h-5">
                                        <input id="confirm" name="confirm_handover" type="checkbox" required class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-400 rounded-none bg-white">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="confirm" class="font-bold text-gray-700">Confirmation of Handover</label>
                                        <p class="text-gray-500">I confirm that I have verified the identity and handed over the item.</p>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded hover:bg-green-700 shadow-lg transition transform hover:scale-105">
                                    ✅ Complete Handover
                                </button>
                            </form>

                        {{-- 📡 3B：還沒鑰匙，顯示雷達等待畫面並啟動掃描監聽 --}}
                        @else
                            
                            <div class="bg-slate-50 p-12 rounded-2xl border-2 border-dashed border-slate-300 text-center">
                                <div class="animate-pulse mb-6">
                                    <i class="fas fa-qrcode text-6xl text-slate-400"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-800 mb-2">Waiting for Passenger QR Code</h3>
                                <p class="text-slate-500 font-medium">
                                    Please use your mobile device (logged in as Staff) to scan the passenger's digital pass.
                                </p>
                            </div>
                            
                            <script>
                                setInterval(function() {
                                    fetch('{{ route('staff.claims.check_scan') }}?current_id={{ $match->id }}')
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success' && data.redirect_url) {
                                                // 🚀 掃描成功！自動跳轉到對比圖頁面
                                                window.location.href = data.redirect_url;
                                            }
                                        });
                                }, 2000); 
                            </script>

                        @endif

                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>