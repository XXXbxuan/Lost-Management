<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('👁️ Stage 1: Visual & Info Comparison') }}
            </h2>
            <span class="px-4 py-1 bg-amber-100 text-amber-700 text-xs font-black rounded-full uppercase">
                Verification in Progress
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <div class="text-center mb-10">
                <h3 class="text-2xl font-black text-slate-800">Double-Check Everything</h3>
                <p class="text-slate-500">Ensure the reported item matches our physical inventory before proceeding.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                
                {{-- 左邊：旅客報失資訊 --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border-t-8 border-rose-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">👤</div>
                    <span class="bg-rose-100 text-rose-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Passenger's Proof</span>
                    
                    <div class="mt-6 aspect-square rounded-[2rem] overflow-hidden border-4 border-slate-50 shadow-inner">
                        <img src="{{ asset('storage/' . ($match->lostItem->image_path ?? $match->lostItem->image_url)) }}" 
                             class="w-full h-full object-cover transition duration-500 hover:scale-110">
                    </div>

                    <div class="mt-8 space-y-4">
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-black text-slate-400 uppercase">Item Name</p>
                            <p class="text-lg font-bold text-slate-800">{{ $match->lostItem->item_name }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-black text-slate-400 uppercase">Description / Details</p>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $match->lostItem->description ?? 'No extra details provided.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- 右邊：系統入庫資訊 --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border-t-8 border-blue-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">📦</div>
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">System Record</span>
                    
                    <div class="mt-6 aspect-square rounded-[2rem] overflow-hidden border-4 border-slate-50 shadow-inner">
                        <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" 
                             class="w-full h-full object-cover transition duration-500 hover:scale-110">
                    </div>

                    <div class="mt-8 space-y-4">
                        <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest">📍 Current Storage Location</p>
                            <p class="text-xl font-black text-blue-800">{{ $match->foundItem->storage_location }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-black text-slate-400 uppercase">Found At</p>
                            <p class="text-sm font-bold text-slate-800">{{ $match->foundItem->location_found }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 底部按鈕 --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex justify-center gap-6 w-full">
                    <a href="{{ route('staff.claims.process', $match->id) }}" 
                       class="px-10 py-4 bg-white text-slate-400 font-bold rounded-2xl border-2 border-slate-100 hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    
                    {{-- ✅ 按下後去 Stage 2 --}}
                    <a href="{{ route('staff.claims.handover', ['id' => $match->id, 'step' => 2]) }}" 
                       class="px-12 py-4 bg-slate-900 text-white font-black rounded-2xl shadow-2xl hover:bg-black transition transform hover:scale-105 flex items-center gap-3">
                        CONFIRM MATCH - GO TO HANDOVER
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em]">Handover Protocol Stage 1 of 2</p>
            </div>
        </div>
    </div>
</x-app-layout>