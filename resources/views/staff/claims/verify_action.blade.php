<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Handover Verification</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border">
                <div class="bg-slate-800 p-4 text-white flex justify-between">
                    <span class="font-bold">PASSPORT/IC VERIFICATION</span>
                    <span class="text-xs">MATCH #{{ $match->id }}</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <p class="text-[10px] font-bold text-red-500 uppercase mb-2">Reported Photo</p>
                            <img src="{{ asset('storage/' . $match->lostItem->image_path) }}" class="w-full aspect-square object-cover rounded-lg border-2 border-red-100">
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-blue-500 uppercase mb-2">Found Item Photo</p>
                            <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" class="w-full aspect-square object-cover rounded-lg border-2 border-blue-100">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl mb-6">
                        <p class="text-sm"><strong>Passenger:</strong> {{ $match->lostItem->passenger_name }}</p>
                        <p class="text-sm"><strong>ID Number:</strong> {{ $match->lostItem->ic_number }}</p>
                    </div>

                    <form action="{{ route('staff.handover.complete', $match->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Confirm identity verified and item handed over?')" 
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg transition active:scale-95">
                            COMPLETE HANDOVER
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 