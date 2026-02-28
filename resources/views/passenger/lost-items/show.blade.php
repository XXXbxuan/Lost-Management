<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                {{-- Link back to history --}}
                <a href="{{ route('passenger.report') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
                    &larr; Back to History
                </a>
            </div>

            <div class="bg-white p-6 shadow rounded-lg border-t-4 border-red-500">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <h3 class="font-bold text-lg text-gray-800">Report #{{ $lostItem->id }}</h3>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase 
                        {{ $lostItem->status == 'Matched' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $lostItem->status }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    {{-- Left: Image --}}
                    <div>
                        <div class="h-64 bg-gray-100 rounded border overflow-hidden flex items-center justify-center">
                            @if($lostItem->image_path)
                                <img src="{{ asset('storage/' . $lostItem->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-400">No Image Uploaded</span>
                            @endif
                        </div>
                    </div>

                    {{-- Right: Details --}}
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Item Name</span>
                            <span class="font-bold text-gray-900 text-lg">{{ $lostItem->item_name }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Category</span>
                                <span class="text-gray-900">{{ $lostItem->category }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Color</span>
                                <span class="text-gray-900">{{ $lostItem->color }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Lost Location</span>
                            <span class="text-gray-900">{{ $lostItem->lost_location }}</span>
                            @if($lostItem->flight_number)
                                <span class="text-xs text-blue-500 ml-2">(Flight: {{ $lostItem->flight_number }})</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Date Lost</span>
                            <span class="text-gray-900">{{ $lostItem->lost_time->format('d M Y, h:i A') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Description</span>
                            <p class="text-gray-600 bg-gray-50 p-3 rounded mt-1 border">{{ $lostItem->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Status Message --}}
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg text-center">
                    @if($lostItem->status == 'Matched')
                        <h4 class="font-bold text-green-700">Good News! We found a match.</h4>
                        <p class="text-sm text-green-600 mt-1">Please check your email for pickup instructions or visit the "Lost & Found" center at the airport.</p>
                    @else
                        <h4 class="font-bold text-blue-700">We are still searching.</h4>
                        <p class="text-sm text-blue-600 mt-1">Our staff is currently checking for items that match your description. You will be notified immediately if a match is found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>