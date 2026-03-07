<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            {{-- Back icon (circle) --}}
            <a href="{{ route('staff.lost-items.index') }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 transition">
                <span class="text-xl leading-none">‹</span>
            </a>

            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('👁️ Stage 1: Identity & Item Verification') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">

                {{-- 👤 左側：領取人信息 (Passenger Profile) --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border-t-8 border-slate-900 relative">
                    <div class="flex flex-col items-center text-center">
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest mb-6">
                            Passenger Profile
                        </span>

                        {{-- 圓形照片佔位符 --}}
                        <div class="w-32 h-32 rounded-full bg-slate-100 border-4 border-slate-50 flex items-center justify-center mb-6 shadow-inner">
                            <i class="fas fa-user text-4xl text-slate-300"></i>
                        </div>

                        <div class="w-full space-y-4 text-left">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Full Name</p>
                                <p class="text-lg font-bold text-slate-800">{{ $match->lostItem->passenger_name }}</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gmail / Email</p>
                                <p class="text-sm font-bold text-slate-600">{{ $match->lostItem->passenger_email }}</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact Number</p>
                                <p class="text-sm font-bold text-slate-600">{{ $match->lostItem->passenger_phone ?? 'No phone provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 📦 右側：物品詳細信息 (Found Item Info) --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border-t-8 border-blue-600 relative">
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                        Found Item Details
                    </span>

                    <div class="mt-6 aspect-video rounded-3xl overflow-hidden border-4 border-slate-50 shadow-inner mb-6">
                        <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" class="w-full h-full object-cover">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 bg-blue-50 p-4 rounded-2xl border border-blue-100">
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Item Name</p>
                            <p class="text-lg font-black text-blue-900">{{ $match->foundItem->item_name }}</p>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Color</p>
                            <p class="text-sm font-black text-slate-800">{{ $match->foundItem->color ?? 'N/A' }}</p>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Brand</p>
                            <p class="text-sm font-black text-slate-800">{{ $match->foundItem->brand ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Description</p>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $match->foundItem->description ?? 'No description.' }}</p>
                        </div>

                        {{-- 存放位置：Staff 最關心的重點 --}}
                        <div class="col-span-2 bg-amber-50 p-5 rounded-2xl border-2 border-amber-200">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">
                                📍 Storage Location (Grab item here)
                            </p>
                            <p class="text-2xl font-black text-amber-900">{{ $match->foundItem->storage_location }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 控制按鈕 --}}
            <div class="flex justify-center gap-6">

                {{-- ❌ Reject Claim：引用现有 storeMatch（outcome=not_matched），Reject 后回 Lost Reports --}}
                <form action="{{ route('staff.match.store') }}" method="POST"
                      onsubmit="return confirm('Reject this claim (mark as NOT MATCHED)?');">
                    @csrf
                    <input type="hidden" name="lost_id" value="{{ $match->lostId }}">
                    <input type="hidden" name="found_id" value="{{ $match->foundId }}">
                    <input type="hidden" name="outcome" value="not_matched">
                    <input type="hidden" name="notes" value="Rejected at Stage 1 (Identity & Item Verification)">
                    <input type="hidden" name="similarity_score" value="{{ $match->similarityScore ?? 0 }}">
                    <input type="hidden" name="return_url" value="{{ route('staff.lost-items.index') }}">

                    {{-- ✅ NEW: 让 audit log 显示 REJECT_CLAIM --}}
                    <input type="hidden" name="source" value="reject_claim">

                    <button type="submit"
                        class="px-10 py-4 bg-white text-red-600 font-black rounded-2xl border-2 border-red-100 hover:bg-red-50 transition flex items-center gap-2">
                        <span class="text-xl">＋</span> REJECT CLAIM
                    </button>
                </form>

                {{-- ✅ Verified - Proceed --}}
                <a href="{{ route('staff.claims.handover', ['id' => $match->id, 'step' => 2]) }}"
                   class="px-12 py-4 bg-slate-900 text-white font-black rounded-2xl shadow-2xl hover:bg-black transition transform hover:scale-105 flex items-center gap-3">
                    VERIFIED - PROCEED TO HANDOVER <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>