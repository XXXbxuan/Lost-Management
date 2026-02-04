<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Rewards & Points') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- SUCCESS / ERROR MESSAGES --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- 1. MEMBERSHIP CARD (LEFT) --}}
                <div class="md:col-span-1 space-y-6">
                    {{-- Blue Card --}}
                    <div class="rounded-2xl shadow-xl text-white p-8 relative overflow-hidden" 
                         style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                        <div class="relative z-10">
                            <h3 class="text-sm font-bold tracking-widest uppercase opacity-80">Current Balance</h3>
                            <div class="mt-4 text-4xl font-extrabold tracking-tight">
                                {{ Auth::user()->points }} <span class="text-xl font-normal opacity-80">pts</span>
                            </div>
                            <div class="mt-8">
                                <p class="text-xs opacity-70 uppercase">Member</p>
                                <p class="font-bold text-lg">{{ Auth::user()->name }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- My Redemption History (NEW!) --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="font-bold text-gray-700 mb-4 border-b pb-2">My Vouchers</h4>
                        @if($myRedemptions->count() > 0)
                            <ul class="space-y-3">
                                @foreach($myRedemptions as $history)
                                    <li class="text-sm bg-gray-50 p-2 rounded border flex justify-between items-center">
                                        <span class="font-bold text-gray-700">{{ $history->voucher->name }}</span>
                                        <span class="text-xs text-green-600 font-bold">Active</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-400 italic">You haven't redeemed anything yet.</p>
                        @endif
                    </div>
                </div>

                {{-- 2. REDEEM VOUCHERS LIST (RIGHT) --}}
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Available Rewards</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($vouchers as $voucher)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition flex flex-col justify-between">
                                        <div>
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">{{ $voucher->category }}</span>
                                            <h4 class="font-bold mt-2 text-lg">{{ $voucher->name }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">{{ $voucher->description }}</p>
                                        </div>
                                        
                                        <div class="mt-4 flex items-center justify-between border-t pt-4">
                                            <span class="font-bold text-blue-600">{{ $voucher->points }} pts</span>
                                            
                                            {{-- DYNAMIC REDEEM FORM --}}
                                            <form action="{{ route('passenger.redeem', $voucher->id) }}" method="POST">
                                                @csrf
                                                @if(Auth::user()->points >= $voucher->points)
                                                    <button type="submit" class="bg-black text-white text-xs px-4 py-2 rounded hover:bg-gray-800 transition">
                                                        Redeem Now
                                                    </button>
                                                @else
                                                    <button type="button" disabled class="bg-gray-200 text-gray-400 text-xs px-4 py-2 rounded cursor-not-allowed">
                                                        Need {{ $voucher->points - Auth::user()->points }} more
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>