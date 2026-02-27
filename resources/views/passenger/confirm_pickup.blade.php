@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8 border border-gray-100 text-center">
        
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Final Confirmation</h1>
            <p class="text-gray-500 mt-2">Please verify your pickup appointment.</p>
        </div>

        <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left space-y-4">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Item Found</label>
                <p class="font-bold text-blue-700">{{ $match->foundItem->description }}</p>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Scheduled Date & Time</label>
                <p class="font-bold text-gray-800">
                    {{ \Carbon\Carbon::parse($match->appointment_at)->format('d M Y, h:i A') }}
                </p>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pick-up Location</label>
                <p class="font-bold text-gray-800">{{ $match->appointment_venue ?? 'Airport Lost & Found Office' }}</p>
            </div>
        </div>

        <form action="{{ route('pickup.process', ['token' => $match->verification_token]) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center">
                <i class="fas fa-check-circle mr-2"></i>
                YES, I CONFIRM ATTENDANCE
            </button>
        </form>

        <p class="mt-6 text-[10px] text-gray-400 leading-tight uppercase">
            Confirmation is required to generate your secure QR verification code.
        </p>
    </div>
</div>
@endsection