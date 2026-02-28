<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔄 Claim Process & Handover
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                @include('staff.claims.partials.timeline') 
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-1 bg-white shadow-sm rounded-lg p-6 h-fit">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">📦 Item Info</h3>
                    @if($foundItem->image_path)
                        <img src="{{ Storage::url($foundItem->image_path) }}" class="w-full h-40 object-cover rounded mb-4 border">
                    @endif
                    <p class="text-sm"><strong>Name:</strong> {{ $foundItem->item_name }}</p>
                    <p class="text-sm"><strong>Ref ID:</strong> #{{ $foundItem->id }}</p>
                    <p class="text-sm"><strong>Storage:</strong> <span class="bg-gray-200 px-2 rounded">{{ $foundItem->storage_location }}</span></p>
                </div>

                <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-6">
                    
                    @if(is_null($match->appointment_at))
                        <h3 class="text-lg font-bold text-blue-800 mb-4">📅 Step 1: Schedule Pickup</h3>
                        <p class="text-sm text-gray-500 mb-6">Please contact the passenger and agree on a pickup time.</p>

                        <form action="{{ route('staff.claims.schedule') }}" method="POST">
                            @csrf
                            <input type="hidden" name="match_id" value="{{ $match->id }}">
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Date</label>
                                    <input type="date" name="appointment_date" required class="w-full rounded border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Time</label>
                                    <input type="time" name="appointment_time" required class="w-full rounded border-gray-300 shadow-sm">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">
                                Set Appointment & Send SMS
                            </button>
                        </form>

                    @elseif(!$match->is_confirmed)
                        <div class="text-center py-8">
                            <div class="text-5xl mb-4">📩</div>
                            <h3 class="text-xl font-bold text-gray-800">Appointment Set! Waiting for Confirmation...</h3>
                            <p class="text-gray-500 mt-2">The passenger has received an SMS. They must click the link to confirm.</p>
                            
                            <div class="mt-8 bg-yellow-50 border border-yellow-200 p-4 rounded text-left shadow-sm">
                                <p class="text-xs font-bold text-yellow-800 uppercase mb-1">🔧 Developer Tool (Simulated SMS):</p>
                                <p class="text-sm text-gray-600">User received this link:</p>
                                <a href="{{ route('pickup.confirm', ['token' => $match->verification_token]) }}" target="_blank" class="text-blue-600 underline font-mono break-all">
                                    {{ route('pickup.confirm', ['token' => $match->verification_token]) }}
                                </a>
                                <p class="text-xs text-gray-400 mt-2">(Click this link later to simulate user confirmation)</p>
                            </div>
                        </div>

                    @else
                        <h3 class="text-lg font-bold text-green-800 mb-4">🏁 Step 2: Final Handover</h3>
                        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-6 text-sm flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            User confirmed arrival for <strong>{{ $match->appointment_at->format('d M, h:i A') }}</strong>.
                        </div>

                        <form action="{{ route('staff.claims.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="lostId" value="{{ $lostItem->id }}">
                            <input type="hidden" name="foundId" value="{{ $foundItem->id }}">
                            <input type="hidden" name="claimerName" value="{{ $lostItem->passenger_name }}">
                            <input type="hidden" name="claimerPhone" value="{{ $lostItem->passenger_phone }}">
                            
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Verify Identity Document (IC / Passport) <span class="text-red-500">*</span></label>
                                <input type="text" name="claimerIcPassport" required class="w-full rounded border-gray-300 shadow-sm text-lg" placeholder="e.g. 990101-14-xxxx">
                            </div>

                            <div class="flex items-start mb-6 bg-gray-50 p-3 border border-gray-200">
                                <div class="flex items-center h-5">
                                    <input id="confirm" name="confirm_handover" type="checkbox" required class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-400 rounded-none bg-white">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="confirm" class="font-bold text-gray-700">Confirmation of Handover</label>
                                    <p class="text-gray-500">I confirm that I have verified the identity and handed over the item.</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded hover:bg-green-700 shadow-lg transition transform hover:scale-105">
                                ✅ Complete Handover
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 