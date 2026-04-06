<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Found Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">Items Found at Airport</h3>
                        <span class="text-sm text-gray-500">If you see your item here, please report it!</span>
                    </div>

                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Image</th>
                                <th class="py-3 px-6 text-left">Item Details</th>
                                <th class="py-3 px-6 text-left">Found Location</th>
                                <th class="py-3 px-6 text-center">Date Found</th>
                                {{-- 🟢 NEW: Status Column Header --}}
                                <th class="py-3 px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($foundItems as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" class="w-16 h-16 object-cover rounded border">
                                        @else
                                            <div class="w-16 h-16 bg-gray-100 flex items-center justify-center text-xs text-gray-400">No Img</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="font-bold text-gray-800">{{ $item->item_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->category }} | {{ $item->color }}</div>
                                        @if($item->brand)
                                            <div class="text-xs text-gray-400">Brand: {{ $item->brand }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div>{{ $item->found_location }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        {{ $item->created_at->format('d M Y') }}
                                    </td>
                                    
                                    {{-- 🟢 NEW: Status Badge Logic --}}
                                    <td class="py-3 px-6 text-center">
                                        @if($item->status == 'Claimed')
                                            <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold uppercase tracking-wider">
                                                Claimed
                                            </span>
                                        @elseif($item->status == 'Matched')
                                            <span class="bg-blue-100 text-blue-700 py-1 px-3 rounded-full text-xs font-bold uppercase tracking-wider">
                                                Processing
                                            </span>
                                        @else
                                            {{-- Default for 'Found', 'Unclaimed', 'In Storage' --}}
                                            <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold uppercase tracking-wider">
                                                Available
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">No items found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $foundItems->links() }}</div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>