<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{-- 標題自動切換 --}}
                {{ request('step') === 'enter_ic' ? __('Final Security Handover') : __('🔄 Claim Process & Handover') }}
            </h2>
            @if(request('step') === 'enter_ic')
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-black rounded-full uppercase tracking-widest">
                    STAGE 2: HANDOVER
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. 進度時間軸 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] mb-6 p-6 border border-gray-100">
                @include('staff.claims.partials.timeline') 
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- 左側：物品基本資訊 --}}
                <div class="md:col-span-1 bg-white shadow-sm rounded-[2rem] p-6 h-fit border border-gray-100 {{ request('step') === 'enter_ic' ? 'hidden md:block' : '' }}">
                    <h3 class="font-black text-slate-800 mb-4 border-b pb-2 uppercase text-xs tracking-widest">📦 Item Info</h3>
                    @if($foundItem->image_path)
                        <img src="{{ Storage::url($foundItem->image_path) }}" class="w-full h-40 object-cover rounded-2xl mb-4 border shadow-inner">
                    @endif
                    <div class="space-y-1">
                        <p class="text-sm"><strong>Name:</strong> {{ $foundItem->item_name }}</p>
                        <p class="text-sm"><strong>Ref ID:</strong> <span class="font-mono text-blue-600">#{{ $foundItem->id }}</span></p>
                        <p class="text-sm"><strong>Storage:</strong> <span class="bg-gray-200 px-2 rounded font-bold">{{ $foundItem->storage_location }}</span></p>
                    </div>
                </div>

                <div class="md:col-span-2">
                    
                    {{-- 狀況 1：設定預約 --}}
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

                    {{-- 🌟 狀況 2：等待 Email 確認 (已加入自動刷新功能) --}}
                    @elseif(!$match->is_confirmed)
                        <div class="bg-white shadow-sm rounded-[2rem] p-12 text-center border border-gray-100">
                            <div class="text-5xl mb-6 animate-bounce">📩</div>
                            <h3 class="text-xl font-bold text-gray-800 uppercase tracking-widest mb-2">Waiting for Confirmation...</h3>
                            <p class="text-gray-500 mb-6">The passenger has received an email to confirm the time.</p>
                            
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest border border-slate-100">
                                <div class="w-2 h-2 bg-blue-400 rounded-full animate-ping"></div>
                                Auto-syncing status...
                            </div>
                        </div>

                        {{-- 🔄 AJAX 自動檢查腳本 --}}
                        <script>
                            setInterval(function() {
                                fetch('{{ route('staff.claims.check_confirmation', $match->id) }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.is_confirmed) {
                                            window.location.reload(); // 旅客點了確認，電腦自動重新整理跳下一關
                                        }
                                    });
                            }, 3000); 
                        </script>

                    {{-- 🌟 狀況 3：核心連線區域 (旅客已確認) --}}
                    @else
                        
                        {{-- 🔑 3A：結案表單 (Identity Verification) --}}
                        @if(request('step') === 'enter_ic')
                            <div class="max-w-3xl mx-auto">
                                <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100">
                                    <div class="bg-slate-900 p-5 text-white flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                            <span class="text-sm font-black tracking-widest uppercase">Identity Verification</span>
                                        </div>
                                        <span class="text-xs font-mono opacity-60">MATCH: #{{ $match->id }}</span>
                                    </div>

                                    <div class="p-8">
                                        <div class="grid grid-cols-2 gap-8 mb-10">
                                            <div class="relative">
                                                <div class="absolute -top-3 -left-3 bg-rose-500 text-white text-[10px] font-black px-2 py-1 rounded-md z-10 shadow-lg uppercase">Proof</div>
                                                <div class="rounded-2xl border-4 border-rose-50 overflow-hidden">
                                                    <img src="{{ asset('storage/' . ($match->lostItem->image_path ?? $match->lostItem->image_url)) }}" class="w-full aspect-square object-cover">
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <div class="absolute -top-3 -left-3 bg-blue-500 text-white text-[10px] font-black px-2 py-1 rounded-md z-10 shadow-lg uppercase">Record</div>
                                                <div class="rounded-2xl border-4 border-blue-50 overflow-hidden">
                                                    <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" class="w-full aspect-square object-cover">
                                                </div>
                                            </div>
                                        </div>

                                        <form action="{{ route('staff.claims.complete', $match->id) }}" method="POST">
                                            @csrf
                                            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 mb-8">
                                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Passenger Details</h4>
                                                <div class="grid grid-cols-2 gap-6">
                                                    <div>
                                                        <p class="text-xs text-slate-500 font-bold uppercase">Legal Name</p>
                                                        <p class="text-lg font-bold text-slate-800">{{ $match->lostItem->passenger_name }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-rose-500 font-black uppercase tracking-tighter">Enter IC / Passport <span class="text-rose-600">*</span></p>
                                                        <input type="text" name="passenger_ic" required 
                                                            class="w-full mt-1 bg-white border-2 border-slate-200 rounded-xl px-3 py-2 font-mono font-bold focus:border-slate-900 focus:ring-0 transition-all shadow-sm"
                                                            placeholder="e.g. 990101-14-5566"
                                                            value="{{ $match->lostItem->ic_number }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-8">
                                                <label class="flex items-center p-5 bg-emerald-50 rounded-2xl border border-emerald-100 cursor-pointer group transition-all hover:bg-emerald-100">
                                                    <input type="checkbox" required class="w-6 h-6 rounded text-emerald-600 focus:ring-emerald-500 border-emerald-300 transition">
                                                    <div class="ml-4">
                                                        <p class="text-sm font-black text-emerald-800 uppercase tracking-widest">Identity Verified</p>
                                                        <p class="text-xs text-emerald-600 font-bold opacity-80">I checked the original document.</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <button type="submit" onclick="return confirm('Complete this handover permanently?')" class="w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-2xl shadow-xl flex items-center justify-center gap-3">
                                                🤝 COMPLETE HANDOVER
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        {{-- 📡 3B：雷達畫面 (等待 QR Scan) --}}
                        @else
                            <div class="bg-white shadow-sm rounded-[2rem] p-12 border border-gray-100 text-center">
                                <div class="bg-slate-50 p-12 rounded-[2rem] border-2 border-dashed border-slate-200 inline-block w-full">
                                    <div class="animate-pulse mb-6 flex justify-center">
                                        <div class="p-5 bg-slate-100 rounded-full text-slate-300">
                                            <i class="fas fa-qrcode text-6xl"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight">Waiting for QR Scan</h3>
                                    <p class="text-slate-500 font-medium mb-8">Scan the pickup pass to unlock Stage 1.</p>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-ping"></div>
                                        Monitoring radar...
                                    </div>
                                </div>
                            </div>
                            
                            {{-- 🔄 QR 雷達腳本 --}}
                            <script>
                                setInterval(function() {
                                    fetch('{{ route('staff.claims.check_scan') }}?current_id={{ $match->id }}')
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === 'success' && data.redirect_url) {
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