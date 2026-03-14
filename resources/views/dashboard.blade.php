<x-app-layout>
    
    {{-- 🌟 PASSENGER VIEW 🌟 --}}
    @if(Auth::user()->role === 'Passenger')

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Passenger Portal') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <div class="bg-gradient-to-r from-blue-600 to-indigo-800 rounded-2xl shadow-lg overflow-hidden relative">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10 text-white">
                        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M22 16v-2l-8.5-5V3.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5V9L2 14v2l8.5-2.5V19L8 20.5V22l4-1 4 1v-1.5L13.5 19v-5.5L22 16z"/></svg>
                    </div>
                    
                    <div class="px-8 py-10 relative z-10">
                        <h2 class="text-3xl font-extrabold text-white mb-2">Welcome, {{ Auth::user()->name }}!</h2>
                        <p class="text-blue-100 text-lg max-w-2xl">
                            The official Airport Lost & Found portal. Have you misplaced your luggage or found an item in the terminal? Select an option below to get started.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <a href="{{ route('passenger.report') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-red-200 transition transform hover:-translate-y-1 group">
                        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Report Lost Item</h3>
                        <p class="text-sm text-gray-500">File a report for missing luggage, electronics, or personal belongings.</p>
                    </a>

                    <a href="{{ route('passenger.found_items') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-green-200 transition transform hover:-translate-y-1 group">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Browse Found Items</h3>
                        <p class="text-sm text-gray-500">Search our real-time database of items recovered by airport security and staff.</p>
                    </a>

                    <a href="{{ route('passenger.rewards') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-blue-200 transition transform hover:-translate-y-1 group relative overflow-hidden">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Rewards Center</h3>
                        <p class="text-sm text-gray-500">Spend your points on airport vouchers.</p>
                        
                        <div class="absolute top-6 right-6 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full flex items-center shadow-sm">
                            <span class="font-bold text-blue-700">{{ Auth::user()->points }}</span>
                            <span class="text-xs text-blue-500 ml-1 font-semibold uppercase">pts</span>
                        </div>
                    </a>

                </div>

                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 mt-8">
                    <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">How the Airport Protocol Works</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600">
                        <div>
                            <strong class="block text-gray-800 mb-1">1. Secure Handover</strong>
                            If you find an item in the terminal or on an airplane, please hand it to the nearest staff counter.
                        </div>
                        <div>
                            <strong class="block text-gray-800 mb-1">2. Earn Points</strong>
                            Provide your registered email to the staff. You will instantly receive 100 points as a thank you!
                        </div>
                        <div>
                            <strong class="block text-gray-800 mb-1">3. Security Verification</strong>
                            High-value items like Passports and Laptops are transferred to Airport Police after 24 hours.
                        </div>
                    </div>
                </div>

            </div>
        </div>

    @else
        {{-- 🌟 ADMIN & STAFF VIEW (Simple Dashboard) 🌟 --}}
        
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                    <div class="p-6 text-gray-900 font-medium">
                        {{ __("You're logged in as ") . Auth::user()->role . "!" }}
                    </div>
                </div>
            </div>
        </div>

    @endif

</x-app-layout>