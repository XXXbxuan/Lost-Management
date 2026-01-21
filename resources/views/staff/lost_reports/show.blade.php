<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matching and Verification – Candidate Matches
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[85rem] mx-auto sm:px-6 lg:px-8"> 
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <div class="lg:col-span-3 space-y-6">
                    
                    <div class="bg-white p-4 shadow rounded-lg border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-3 border-b pb-2 text-sm">Filter Criteria</h3>
                        
                        <form method="GET" action="{{ route('staff.lost-items.show', $lostReport->id) }}">
                            <input type="hidden" name="search" value="1">
                            
                            <div class="mb-2">
                                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Category</label>
                                <select name="category" class="w-full text-sm rounded border-gray-300 py-1.5">
                                    <option value="">-- All --</option>
                                    <option value="Electronics" {{ request('category', $lostReport->category) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="Bag" {{ request('category', $lostReport->category) == 'Bag' ? 'selected' : '' }}>Bag</option>
                                    <option value="Wallet" {{ request('category', $lostReport->category) == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                                    <option value="Clothing" {{ request('category', $lostReport->category) == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="Document" {{ request('category', $lostReport->category) == 'Document' ? 'selected' : '' }}>Document</option>
                                    <option value="Others" {{ request('category', $lostReport->category) == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Location</label>
                                <select name="location" class="w-full text-sm rounded border-gray-300 py-1.5">
                                    <option value="">-- All --</option>
                                    <option value="Airplane Cabin" {{ request('location') == 'Airplane Cabin' ? 'selected' : '' }}>Airplane Cabin</option>
                                    <option value="Check-in Counter" {{ request('location') == 'Check-in Counter' ? 'selected' : '' }}>Check-in Counter</option>
                                    <option value="Departure Hall" {{ request('location') == 'Departure Hall' ? 'selected' : '' }}>Departure Hall</option>
                                    <option value="Arrival Hall" {{ request('location') == 'Arrival Hall' ? 'selected' : '' }}>Arrival Hall</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Date Range</label>
                                <div class="flex gap-2">
                                    <input type="date" name="date_from" value="{{ request('date_from', $lostReport->lost_time->format('Y-m-d')) }}" class="w-1/2 text-xs rounded border-gray-300 py-1.5">
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-1/2 text-xs rounded border-gray-300 py-1.5">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Keyword</label>
                                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Search..." class="w-full text-sm rounded border-gray-300 py-1.5">
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold text-sm hover:bg-blue-700 transition">
                                Search
                            </button>
                        </form>
                    </div>

                    <div class="bg-white p-4 shadow rounded-lg border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-3 border-b pb-2 text-sm">Lost Report (Main)</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-xs font-bold text-gray-500 block">ID</span> #{{ $lostReport->id }}</p>
                            <p><span class="text-xs font-bold text-gray-500 block">Passenger</span> {{ $lostReport->passenger_name }}</p>
                            <p><span class="text-xs font-bold text-gray-500 block">Category</span> {{ $lostReport->category }}</p>
                            <p><span class="text-xs font-bold text-gray-500 block">Location</span> {{ $lostReport->lost_location }}</p>
                            <p><span class="text-xs font-bold text-gray-500 block">Date</span> {{ $lostReport->lost_time->format('Y-m-d') }}</p>
                            
                            @if($lostReport->image_path)
                                <div class="mt-2">
                                    <span class="text-xs font-bold text-gray-500 block mb-1">Photo</span>
                                    <img src="{{ asset('storage/' . $lostReport->image_path) }}" class="w-full rounded border">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-9">
                    
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-700">
                            Candidate Matches
                            <span class="ml-2 text-sm font-normal text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                                {{ $candidateMatches->count() }} records (>50%)
                            </span>
                        </h3>
                    </div>

                    @if($candidateMatches->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach($candidateMatches as $found)
                                <div class="bg-white rounded border border-gray-200 overflow-hidden hover:shadow-lg transition flex flex-col h-full relative group">
                                    
                                    <div class="h-32 bg-gray-100 w-full relative">
                                        @if($found->image_path)
                                            <img src="{{ asset('storage/' . $found->image_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full text-gray-400 text-xs">No Image</div>
                                        @endif
                                        <div class="absolute top-1 right-1 bg-white/90 px-1.5 py-0.5 rounded text-[10px] font-bold border shadow-sm">
                                            #{{ $found->id }}
                                        </div>
                                    </div>

                                    <div class="p-2 flex-grow flex flex-col justify-between">
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm truncate" title="{{ $found->item_name }}">{{ $found->item_name }}</h4>
                                            <div class="text-[10px] text-gray-500 mt-1">
                                                <p>{{ $found->found_time ? $found->found_time->format('Y-m-d') : 'N/A' }}</p>
                                                <p class="truncate">{{ $found->location }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <div class="flex justify-between text-[10px] font-bold">
                                                <span>Match</span>
                                                <span class="{{ $found->similarity_score >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ $found->similarity_score }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1 mt-0.5">
                                                <div class="h-1 rounded-full {{ $found->similarity_score >= 80 ? 'bg-green-500' : 'bg-yellow-500' }}" 
                                                     style="width: {{ $found->similarity_score }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('staff.match.verify', ['lost_id' => $lostReport->id, 'found_id' => $found->id]) }}" 
                                       class="block w-full text-center py-2 bg-gray-50 hover:bg-blue-50 text-blue-600 font-bold text-xs border-t">
                                        Details / Verify
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white p-12 rounded-lg shadow-sm border border-gray-200 text-center">
                            <h3 class="text-lg font-medium text-gray-900">No matches found</h3>
                            <p class="mt-2 text-sm text-gray-500">Try adjusting the filter criteria.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>