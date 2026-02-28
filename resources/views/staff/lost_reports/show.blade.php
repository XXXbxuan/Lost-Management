<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matching and Verification – Candidate Matches
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[95rem] mx-auto sm:px-6 lg:px-8"> 
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <div class="lg:col-span-3 space-y-6">
                    
                    <div class="bg-white p-4 shadow-sm rounded-lg border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-3 border-b pb-2 text-xs uppercase tracking-wider">Filter Criteria</h3>
                        
                        {{-- 🌟 這裡改成 $lostItem->id --}}
                        <form method="GET" action="{{ route('staff.lost-items.show', $lostItem->id) }}">
                            <input type="hidden" name="search" value="1">
                            
                            <div class="mb-3">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase block mb-1">Category</label>
                                <select name="category" class="w-full text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5">
                                    <option value="">-- All --</option>
                                    {{-- 🌟 以下全部改成 $lostItem->category --}}
                                    <option value="Electronics" {{ request('category', $lostItem->category) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="Bag" {{ request('category', $lostItem->category) == 'Bag' ? 'selected' : '' }}>Bag</option>
                                    <option value="Wallet" {{ request('category', $lostItem->category) == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                                    <option value="Clothing" {{ request('category', $lostItem->category) == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="Document" {{ request('category', $lostItem->category) == 'Document' ? 'selected' : '' }}>Document</option>
                                    <option value="Others" {{ request('category', $lostItem->category) == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase block mb-1">Found Location</label>
                                <select name="location" class="w-full text-sm rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-1.5">
                                    <option value="">-- All --</option>
                                    <option value="Airplane Cabin" {{ request('location') == 'Airplane Cabin' ? 'selected' : '' }}>Airplane Cabin</option>
                                    <option value="Check-in Counter" {{ request('location') == 'Check-in Counter' ? 'selected' : '' }}>Check-in Counter</option>
                                    <option value="Departure Hall" {{ request('location') == 'Departure Hall' ? 'selected' : '' }}>Departure Hall</option>
                                    <option value="Arrival Hall" {{ request('location') == 'Arrival Hall' ? 'selected' : '' }}>Arrival Hall</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase block mb-1">Date Range</label>
                                <div class="flex gap-2">
                                    {{-- 🌟 這裡改成 $lostItem->lost_time --}}
                                    <input type="date" name="date_from" value="{{ request('date_from', $lostItem->lost_time->format('Y-m-d')) }}" class="w-1/2 text-xs rounded border-gray-300 py-1.5">
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-1/2 text-xs rounded border-gray-300 py-1.5">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-[10px] font-extrabold text-gray-400 uppercase block mb-1">Keyword</label>
                                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Search items..." class="w-full text-sm rounded border-gray-300 py-1.5">
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold text-xs hover:bg-blue-700 transition shadow-sm">
                                SEARCH / REFRESH
                            </button>
                        </form>
                    </div>

                    <div class="bg-white p-4 shadow-sm rounded-lg border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-3 border-b pb-2 text-xs uppercase tracking-wider">Lost Report (Main)</h3>
                        <div class="space-y-3 text-sm">
                            <div class="bg-gray-50 p-2 rounded border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-400 block uppercase">Report ID</span>
                                {{-- 🌟 這裡改成 $lostItem->id --}}
                                <span class="font-black text-red-600">#{{ $lostItem->id }}</span>
                            </div>
                            {{-- 🌟 以下全部改成 $lostItem --}}
                            <p><span class="text-[10px] font-bold text-gray-400 block uppercase">Passenger</span> <span class="font-semibold">{{ $lostItem->passenger_name }}</span></p>
                            <p><span class="text-[10px] font-bold text-gray-400 block uppercase">Item Details</span> {{ $lostItem->item_name }} ({{ $lostItem->color }})</p>
                            <p><span class="text-[10px] font-bold text-gray-400 block uppercase">Lost At</span> {{ $lostItem->lost_location }}</p>
                            <p><span class="text-[10px] font-bold text-gray-400 block uppercase">Date</span> {{ $lostItem->lost_time->format('Y-m-d') }}</p>
                            
                            @if($lostItem->image_path)
                                <div class="mt-2">
                                    <span class="text-[10px] font-bold text-gray-400 block uppercase mb-1">Reference Photo</span>
                                    <img src="{{ asset('storage/' . $lostItem->image_path) }}" class="w-full rounded border shadow-sm">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-9">
                    
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-700">
                            Candidate Matches 
                            <span class="ml-2 text-xs font-normal text-gray-500 bg-gray-100 border px-2 py-1 rounded-full italic">
                                {{ $candidateMatches->count() }} high-similarity matches found
                            </span>
                        </h3>
                    </div>

                    @if($candidateMatches->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach($candidateMatches as $found)
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full relative group shadow-sm">
                                    
                                    <div class="h-32 bg-gray-50 w-full relative border-b overflow-hidden">
                                        @if($found->image_path)
                                            <img src="{{ asset('storage/' . $found->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="flex items-center justify-center h-full text-gray-300 text-[10px] flex-col">
                                                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                No Photo
                                            </div>
                                        @endif
                                        <div class="absolute top-1 right-1 bg-black/70 text-white px-1.5 py-0.5 rounded text-[9px] font-bold">
                                            #{{ $found->id }}
                                        </div>
                                    </div>

                                    <div class="p-2.5 flex-grow flex flex-col justify-between">
                                        <div>
                                            <h4 class="font-black text-gray-900 text-xs truncate mb-1" title="{{ $found->item_name }}">{{ $found->item_name }}</h4>
                                            <div class="text-[10px] text-gray-500 space-y-0.5">
                                                <p class="flex items-center">📅 {{ $found->found_time ? $found->found_time->format('Y-m-d') : 'N/A' }}</p>
                                                <p class="flex items-center truncate">📍 {{ $found->found_location }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="flex justify-between text-[9px] font-black mb-1 uppercase">
                                                <span class="text-gray-400">Match Score</span>
                                                <span class="{{ ($found->similarity_score ?? 0) >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ number_format($found->similarity_score ?? 0, 0) }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 border border-gray-50">
                                                <div class="h-1.5 rounded-full {{ ($found->similarity_score ?? 0) >= 80 ? 'bg-green-500' : 'bg-yellow-500' }} shadow-inner" 
                                                     style="width: {{ $found->similarity_score ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 🌟 這裡改成 $lostItem->id --}}
                                    <a href="{{ route('staff.match.verify', ['lost_id' => $lostItem->id, 'found_id' => $found->id, 'score' => $found->similarity_score]) }}" 
                                       class="w-full bg-blue-50 text-blue-600 font-bold py-2 rounded-md hover:bg-blue-100 transition text-xs uppercase tracking-wider block text-center no-underline border border-blue-200">
                                        Verify Match
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white p-16 rounded-lg shadow-sm border border-gray-200 text-center">
                            <p class="text-gray-400 text-sm italic">No matching unclaimed items found with current filters.</p>
                        </div>
                    @endif

                    @if(isset($rejectedItems) && $rejectedItems->count() > 0)
                        <div class="mt-12 border-t pt-6">
                            <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                                <span class="bg-gray-100 p-1 rounded mr-2">🚫</span> Previously Rejected (Not Matched)
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                @foreach($rejectedItems as $rejected)
                                    <div class="bg-gray-50 border border-gray-200 rounded p-2 flex items-center gap-2 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition">
                                        @if($rejected->image_path)
                                            <img src="{{ asset('storage/' . $rejected->image_path) }}" class="w-8 h-8 object-cover rounded shadow-xs border">
                                        @endif
                                        <div class="text-[9px] truncate">
                                            <p class="font-bold text-gray-600 truncate">#{{ $rejected->id }}</p>
                                            <p class="text-gray-400 italic">Rejected</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div> 
            </div>
        </div>
    </div>
</x-app-layout>