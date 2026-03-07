<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                {{-- 🔙 回到列表頁 --}}
                <a href="{{ route('staff.lost-items.index') }}" class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-full shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 text-slate-600 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ __('🔄 Claim Logistics & Scheduling') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. 進度時間軸：保留，用於顯示目前處於哪個物流階段 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] mb-6 p-6 border border-gray-100">
                @include('staff.claims.partials.timeline') 
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- 左側：物品基本資訊 (僅作為取貨參考) --}}
                <div class="md:col-span-1 bg-white shadow-sm rounded-[2rem] p-6 h-fit border border-gray-100">
                    <h3 class="font-black text-slate-800 mb-4 border-b pb-2 uppercase text-xs tracking-widest">📦 Warehouse Guide</h3>
                    @if($foundItem->image_path)
                        <img src="{{ asset('storage/' . $foundItem->image_path) }}" class="w-full h-40 object-cover rounded-2xl mb-4 border shadow-inner bg-slate-50">
                    @endif
                    <div class="space-y-1">
                        <p class="text-sm"><strong>Item:</strong> {{ $foundItem->item_name }}</p>
                        <p class="text-sm"><strong>Storage:</strong> <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-bold">📍 {{ $foundItem->storage_location }}</span></p>
                    </div>
                </div>

                <div class="md:col-span-2">
                    
                    {{-- 狀況 1：設定預約 (這部分是前端物流，保留) --}}
                    @if(is_null($match->appointment_at))
                        <div class="bg-white shadow-sm rounded-[2rem] p-8 border border-gray-100">
                             <h3 class="text-lg font-bold text-blue-800 mb-4 uppercase tracking-tight">📅 Step 1: Schedule Pickup</h3>
                             <form action="{{ route('staff.claims.schedule') }}" method="POST">
                                @csrf
                                <input type="hidden" name="match_id" value="{{ $match->id }}">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div><input type="date" name="appointment_date" required class="w-full rounded-xl border-gray-300"></div>
                                    <div><input type="time" name="appointment_time" required class="w-full rounded-xl border-gray-300"></div>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-black py-3 rounded-xl hover:bg-blue-700 transition shadow-lg">
                                    Set Appointment & Send Email
                                </button>
                             </form>
                        </div>

                    {{-- 狀況 2：等待 Email 確認 (保留) --}}
                    @elseif(!$match->is_confirmed)
                        <div class="bg-white shadow-sm rounded-[2rem] p-12 text-center border border-gray-100">
                            <div class="text-5xl mb-6 animate-bounce">📩</div>
                            <h3 class="text-xl font-bold text-gray-800 uppercase tracking-widest mb-2">Waiting for Passenger...</h3>
                            <p class="text-gray-500 mb-6">Waiting for the passenger to confirm the appointment time via email.</p>
                            
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <div class="w-2 h-2 bg-blue-400 rounded-full animate-ping"></div>
                                Auto-syncing status...
                            </div>
                        </div>

                        <script>
                            setInterval(function() {
                                fetch('{{ route('staff.claims.check_confirmation', $match->id) }}')
                                    .then(response => response.json())
                                    .then(data => { if (data.is_confirmed) window.location.reload(); });
                            }, 3000); 
                        </script>

                    {{-- 狀況 3：旅客已到現場，等待 QR 掃描 (雷達) --}}
                    @else
                        <div class="bg-white shadow-sm rounded-[2rem] p-12 border border-gray-100 text-center">
                            <div class="bg-slate-50 p-12 rounded-[2rem] border-2 border-dashed border-slate-200 inline-block w-full">
                                <div class="animate-pulse mb-6 flex justify-center text-slate-300">
                                    <i class="fas fa-qrcode text-6xl"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight">Passenger Arrived</h3>
                                <p class="text-slate-500 font-medium mb-8">Scan the passenger's pickup pass to start Verification.</p>
                                
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-ping"></div>
                                    Waiting for QR Scan trigger...
                                </div>
                            </div>
                        </div>
                        
                        {{-- 🔄 核心邏輯：當掃描成功，自動導向 verify_action (Stage 1) --}}
                        <script>
                            setInterval(function() {
                                fetch('{{ route('staff.claims.check_scan') }}?current_id={{ $match->id }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success' && data.redirect_url) {
                                            // 🌟 這裡會自動導向你的 verify_action 頁面
                                            window.location.href = data.redirect_url;
                                        }
                                    });
                            }, 2000); 
                        </script>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>