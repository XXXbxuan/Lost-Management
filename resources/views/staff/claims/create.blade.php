<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👮‍♂️ Process Item Claim (Handover)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="text-yellow-700 font-bold">⚠️ Staff Action Required:</div>
                    <div class="ml-2 text-yellow-700">
                        Please verify the passenger's physical Identity Card (IC) or Passport before proceeding.
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-1 bg-white shadow-lg rounded-lg p-6 border-t-4 border-indigo-500">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">📦 Item to Return</h3>
                    
                    @if($foundItem->image_path)
                        <img src="{{ Storage::url($foundItem->image_path) }}" class="w-full h-48 object-cover rounded-md mb-4 border">
                    @else
                        <div class="w-full h-48 bg-gray-100 rounded-md mb-4 flex items-center justify-center text-gray-400">No Image</div>
                    @endif

                    <div class="space-y-3 text-sm">
                        <div class="border-b pb-2">
                            <label class="text-xs text-gray-500 uppercase font-bold">Item Name</label>
                            <div class="text-lg font-bold text-gray-900">{{ $foundItem->item_name }}</div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Category</label>
                            <div class="text-gray-700">{{ $foundItem->category }}</div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Storage Location</label>
                            <div class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded font-mono font-bold inline-block mt-1">
                                {{ $foundItem->storage_location }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-6 border-t-4 border-green-500">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">📝 Claimer Verification</h3>

                    <form action="{{ route('staff.claims.store') }}" method="POST">
                        @csrf
                        {{-- 🌟 這裡改成 $lostItem->id --}}
                        <input type="hidden" name="lostId" value="{{ $lostItem->id }}">
                        <input type="hidden" name="foundId" value="{{ $foundItem->id }}">

                        <div class="grid grid-cols-1 gap-6">
                            <div class="bg-gray-50 p-4 rounded border">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Passenger Info (From Report)</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm text-gray-600">Full Name</label>
                                        {{-- 🌟 這裡改成 $lostItem->passenger_name --}}
                                        <input type="text" name="claimerName" value="{{ $lostItem->passenger_name }}" class="w-full bg-gray-200 border-none rounded text-gray-600 font-bold" readonly>
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600">Phone Number</label>
                                        {{-- 🌟 這裡改成 $lostItem->passenger_phone --}}
                                        <input type="text" name="claimerPhone" value="{{ $lostItem->passenger_phone }}" class="w-full bg-gray-200 border-none rounded text-gray-600 font-bold" readonly>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-900">
                                    Identity Document No. (IC / Passport) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="claimerIcPassport" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg" 
                                       placeholder="e.g. 990101-14-xxxx">
                                <p class="text-xs text-gray-500 mt-1">Please ensure this matches the physical document.</p>
                            </div>

                            <div class="flex items-start mt-4 bg-green-50 p-3 rounded border border-green-100">
                                <div class="flex items-center h-5">
                                    <input id="confirm" type="checkbox" required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="confirm" class="font-medium text-gray-700">Confirmation of Handover</label>
                                    <p class="text-gray-500">I confirm that I have verified the identity and handed over the item.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('staff.lost-items.index') }}" class="px-4 py-2 bg-gray-200 rounded-md font-bold text-gray-700 hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-bold hover:bg-green-700 shadow-lg transform hover:scale-105 transition">
                                ✅ Complete Handover
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>