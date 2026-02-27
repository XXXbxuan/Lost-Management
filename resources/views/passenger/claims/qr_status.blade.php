@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-white text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-3">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold">Appointment Confirmed</h1>
            <p class="text-green-100 text-sm">Please show this code to the staff</p>
        </div>

        <div class="p-8">
            <div class="flex justify-center mb-8">
                <div class="p-4 bg-white rounded-2xl shadow-inner border-2 border-dashed border-blue-200">
                    {!! QrCode::size(220)->margin(1)->color(0, 162, 255)->generate(route('pickup.verify', ['token' => $match->verification_token])) !!}
                </div>
            </div>

            <div class="space-y-4 bg-gray-50 rounded-2xl p-5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Scheduled Time</span>
                    <span class="font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($match->appointment_at)->format('d M Y, h:i A') }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Location</span>
                    <span class="font-bold text-gray-800">{{ $match->appointment_venue ?? 'Admin Office' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Item Description</span>
                    <span class="font-bold text-blue-600">{{ $match->foundItem->description }}</span>
                </div>
            </div>

            <p class="mt-8 text-center text-[10px] text-gray-400 leading-relaxed uppercase tracking-widest">
                Verification Token: {{ $match->verification_token }}<br>
                Please bring your original ID/Passport
            </p>
        </div>
    </div>
</div>
@endsection