<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matching and Verification – Record Verification Result
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                {{-- 🌟 改為 $lostItem->id --}}
                <a href="{{ route('staff.lost-items.show', $lostItem->id) }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
                    &larr; Back to Candidate List
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">

                <div class="bg-white p-6 shadow rounded-lg border-t-4 border-red-500">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Main Record Details (Lost)</h3>
                    
                    <div class="space-y-4 text-sm">
                        <div class="h-48 bg-gray-100 rounded border overflow-hidden">
                            {{-- 🌟 以下全部改為 $lostItem --}}
                            @if($lostItem->image_path)
                                <img src="{{ asset('storage/' . $lostItem->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">No Image</div>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Lost ID</span>
                            <span class="font-bold text-gray-900">#{{ $lostItem->id }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Item Name</span>
                            <span class="text-gray-900">{{ $lostItem->item_name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Category</span>
                            <span class="text-gray-900">{{ $lostItem->category }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Color</span>
                            <span class="text-gray-900">{{ $lostItem->color }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Location</span>
                            <span class="text-gray-900">{{ $lostItem->lost_location }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Date</span>
                            <span class="text-gray-900">{{ $lostItem->lost_time->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Description</span>
                            <p class="text-gray-600 bg-gray-50 p-2 rounded mt-1 border">{{ $lostItem->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg border-t-4 border-blue-500">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Candidate Record Details (Found)</h3>
                    
                    <div class="space-y-4 text-sm">
                        <div class="h-48 bg-gray-100 rounded border overflow-hidden relative">
                            @if($foundItem->image_path)
                                <img src="{{ asset('storage/' . $foundItem->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">No Image</div>
                            @endif
                            <span class="absolute top-2 right-2 bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">Found</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Found ID</span>
                            <span class="font-bold text-gray-900">#{{ $foundItem->id }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Item Name</span>
                            <span class="text-gray-900">{{ $foundItem->item_name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Category</span>
                            <span class="text-gray-900">{{ $foundItem->category }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Color</span>
                            <span class="text-gray-900">{{ $foundItem->color }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Location</span>
                            <span class="text-gray-900">{{ $foundItem->found_location }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Date</span>
                            <span class="text-gray-900">{{ $foundItem->found_time->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase">Description</span>
                            <p class="text-gray-600 bg-gray-50 p-2 rounded mt-1 border">{{ $foundItem->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow rounded-lg border border-gray-200">
                    <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Verification Result</h3>
                    
                    <form action="{{ route('staff.match.store') }}" method="POST">
                        @csrf
                        {{-- 🌟 改為 $lostItem->id --}}
                        <input type="hidden" name="lost_id" value="{{ $lostItem->id }}">
                        <input type="hidden" name="found_id" value="{{ $foundItem->id }}">
                        <input type="hidden" name="similarity_score" value="{{ $score }}">

                        <div class="mb-6" x-data="{ selection: 'matched' }">
                            <span class="block text-sm font-bold text-gray-700 mb-2">Outcome</span>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="outcome" value="matched" class="sr-only" x-model="selection">
                                    <div :class="selection === 'matched' ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-500 border-gray-200'"
                                        class="text-center py-3 border-2 rounded-md font-bold transition-all duration-200 shadow-sm">
                                        Matched
                                    </div>
                                </label>

                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="outcome" value="not_matched" class="sr-only" x-model="selection">
                                    <div :class="selection === 'not_matched' ? 'bg-red-500 text-white border-red-600' : 'bg-white text-gray-500 border-gray-200'"
                                        class="text-center py-3 border-2 rounded-md font-bold transition-all duration-200 shadow-sm">
                                        Not Matched
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Verification Notes</label>
                            <textarea name="notes" rows="6" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                    placeholder="Enter verification details (e.g. brand confirmed, serial number check)..." 
                                    required></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            {{-- 🌟 改為 $lostItem->id --}}
                            <a href="{{ route('staff.lost-items.show', $lostItem->id) }}" 
                            class="px-4 py-2 text-gray-600 font-bold text-sm hover:underline">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-black text-white font-bold rounded-md text-sm hover:bg-gray-800 shadow-lg transition transform active:scale-95">
                                Save Verification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>