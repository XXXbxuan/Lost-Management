<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('🤝 Stage 2: Final Handover') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100">
                
                {{-- 黑色精緻標題列 --}}
                <div class="bg-slate-900 p-8 text-white">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                            <span class="text-sm font-black tracking-widest uppercase">Security Protocol</span>
                        </div>
                        <span class="text-xs font-mono opacity-40">VERIFY_ID: #{{ $match->id }}</span>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="flex -space-x-4">
                            <img src="{{ asset('storage/' . ($match->lostItem->image_path ?? $match->lostItem->image_url)) }}" class="w-16 h-16 rounded-2xl border-2 border-slate-900 object-cover shadow-lg">
                            <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" class="w-16 h-16 rounded-2xl border-2 border-slate-900 object-cover shadow-lg">
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Releasing Item to</p>
                            <p class="text-2xl font-black">{{ $match->lostItem->passenger_name }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-10">
                    <form action="{{ route('staff.claims.complete', $match->id) }}" method="POST">
                        @csrf
                        
                        {{-- IC / Passport 輸入 --}}
                        <div class="mb-10">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4">
                                Passenger IC / Passport Number <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="passenger_ic" required 
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-8 py-6 font-mono font-bold text-2xl text-slate-800 focus:border-slate-900 focus:ring-0 transition-all shadow-inner"
                                   placeholder="e.g. 010203-14-5566"
                                   value="{{ $match->lostItem->ic_number }}">
                            <p class="mt-3 text-[10px] text-slate-400 font-medium italic">* Ensure this matches the physical document presented by the passenger.</p>
                        </div>

                        {{-- 安全聲明 --}}
                        <div class="mb-10">
                            <label class="flex items-start p-6 bg-emerald-50 rounded-[2rem] border border-emerald-100 cursor-pointer transition hover:bg-emerald-100">
                                <input type="checkbox" required class="mt-1 w-6 h-6 rounded text-emerald-600 border-emerald-300 focus:ring-emerald-500">
                                <div class="ml-4">
                                    <p class="text-sm font-black text-emerald-900 uppercase tracking-tight">Identity Document Verified</p>
                                    <p class="text-xs text-emerald-700 font-bold opacity-70">I confirm that I have physically verified the passenger's identification and it matches the records.</p>
                                </div>
                            </label>
                        </div>

                        {{-- 結案按鈕 --}}
                        <button type="submit" 
                                onclick="return confirm('WARNING: This action is permanent and will close the case. Proceed?')"
                                class="w-full bg-slate-900 hover:bg-black text-white font-black py-6 rounded-[2rem] shadow-2xl text-lg flex items-center justify-center gap-4 transition-all active:scale-[0.98]">
                            <span class="text-2xl">🤝</span>
                            COMPLETE HANDOVER & CLOSE CASE
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <a href="{{ route('staff.claims.handover', $match->id) }}" class="text-xs font-bold text-slate-300 hover:text-slate-500 transition uppercase tracking-widest">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Stage 1
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>