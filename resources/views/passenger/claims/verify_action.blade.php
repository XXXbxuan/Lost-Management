@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-blue-700 p-4 text-white flex items-center justify-between">
            <span class="font-bold uppercase tracking-tight"><i class="fas fa-user-shield mr-2"></i>Staff Verification</span>
            <span class="text-xs bg-blue-500 px-2 py-1 rounded">MATCH #{{ $match->id }}</span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="text-center">
                    <p class="text-[10px] font-bold text-red-500 mb-2 uppercase">Passenger Report</p>
                    <div class="aspect-square rounded-xl overflow-hidden border-2 border-red-100 shadow-sm">
                        <img src="{{ asset('storage/' . $match->lostReport->image_path) }}" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-bold text-blue-500 mb-2 uppercase">Physical Found Item</p>
                    <div class="aspect-square rounded-xl overflow-hidden border-2 border-blue-100 shadow-sm">
                        <img src="{{ asset('storage/' . $match->foundItem->image_path) }}" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="mb-8 space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-2 border-b pb-2">Passenger Information</h3>
                <p class="text-sm"><span class="text-gray-500">Name:</span> <strong>{{ $match->lostReport->passenger_name }}</strong></p>
                <p class="text-sm"><span class="text-gray-500">ID / IC Number:</span> <strong>{{ $match->lostReport->ic_number }}</strong></p>
            </div>

            <form action="{{ route('staff.handover.complete', $match->id) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center"
                        onclick="return confirm('WARNING: Are you sure you have verified the identity and handed over the item?')">
                    <i class="fas fa-handshake mr-2 text-xl"></i>
                    CONFIRM ITEM HANDOVER
                </button>
            </form>
        </div>
    </div>
</div>
@endsection