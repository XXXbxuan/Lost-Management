<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Final Security Handover') }}
            </h2>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-black rounded-full">
                TOKEN VERIFIED
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100">
                
                <div class="bg-slate-900 p-5 text-white flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-black tracking-widest uppercase">Identity Verification</span>
                    </div>
                    <span class="text-xs font-mono opacity-60">MATCH_ID: #{{ $match->id }}</span>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-2 gap-8 mb-10">
                        <div class="relative">
                            <div class="absolute -top-3 -left-3 bg-rose-500 text-white text-[10px] font-black px-2 py-1 rounded-md z-10 shadow-lg">
                                REPORTED BY PASSENGER
                            </div>
                            <div class="group relative overflow-hidden rounded-2xl border-4 border-rose-50">
                                <img src="{{ asset('storage/' . $match->lostItem->image_path) }}" 
                                     class="w-full aspect-square object-cover transition duration-500 group-hover:scale-110">
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute -top-3 -left-3 bg-blue-500 text-white text-[10px] font-black px-2 py-1 rounded-md z-10 shadow-lg">
                                FOUND BY STAFF
                            </div>
                            <div class="group relative overflow-hidden rounded-2xl border-4 border-blue-50">
                                <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" 
                                     class="w-full aspect-square object-cover transition duration-500 group-hover:scale-110">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 mb-8">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Passenger Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-500">Legal Name</p>
                                <p class="text-lg font-bold text-slate-800">{{ $match->lostItem->passenger_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">IC / Passport Number</p>
                                <p class="text-lg font-bold font-mono text-slate-800">{{ $match->lostItem->ic_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 space-y-3">
                        <label class="flex items-center p-4 bg-emerald-50 rounded-2xl border border-emerald-100 cursor-pointer group">
                            <input type="checkbox" required class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-emerald-300">
                            <span class="ml-3 text-sm font-bold text-emerald-800">I have verified the Passenger's Original IC/Passport.</span>
                        </label>
                    </div>

                    <form action="{{ route('staff.claims.complete', $match->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('WARNING: This action is permanent. Confirm item handover?')"
                                class="w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-2xl shadow-xl transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <span class="text-xl">🤝</span>
                            COMPLETE HANDOVER & CLOSE CASE
                        </button>
                    </form>

                    <p class="text-center mt-6 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        Handover will be logged for audit purposes
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>