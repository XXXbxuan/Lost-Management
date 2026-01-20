<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lost Items Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">Items List</h3>
                        
                        <a href="{{ route('staff.lost-items.create') }}" 
                           style="background-color: black; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-weight: bold;"
                           class="hover:bg-gray-700">
                            + Register New Item
                        </a>
                    </div>

                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Image</th>
                                <th class="py-3 px-6 text-left">Item Details</th>
                                <th class="py-3 px-6 text-left">Location</th>
                                <th class="py-3 px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($lostItems as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" style="width: 64px; height: 64px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px;">
                                        @else
                                            <span style="display:inline-block; width:64px; height:64px; background:#eee; text-align:center; line-height:64px; color:#999; font-size:12px;">No Img</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="font-bold">{{ $item->item_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->category }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div>{{ $item->found_location }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->found_time }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-bold uppercase">{{ $item->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">No items found. Click the BLACK button to add one!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $lostItems->links() }}</div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>