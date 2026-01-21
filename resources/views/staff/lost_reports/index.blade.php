<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('🚨 Passenger Lost Reports') }}
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
                        <h3 class="text-lg font-bold text-gray-700">All Lost Reports</h3>
                        
                        <a href="{{ route('staff.lost-items.create') }}" 
                           class="bg-red-600 text-white px-4 py-2 rounded-md font-bold hover:bg-red-700 transition shadow-md no-underline">
                           + Create Lost Report
                        </a>
                    </div>

                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Date</th>
                                <th class="py-3 px-6 text-left">Item Details</th>
                                <th class="py-3 px-6 text-left">Passenger Info</th>
                                <th class="py-3 px-6 text-left">Lost Location</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse($lostReports as $report)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        <div class="font-medium">{{ $report->created_at->format('Y-m-d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->created_at->format('h:i A') }}</div>
                                    </td>

                                    <td class="py-3 px-6 text-left">
                                        <div class="flex items-center">
                                            <div class="mr-3">
                                                @if($report->image_path)
                                                    <img src="{{ asset('storage/' . $report->image_path) }}" class="w-10 h-10 rounded border object-cover">
                                                @else
                                                    <div class="w-10 h-10 rounded border bg-gray-100 flex items-center justify-center text-xs text-gray-400">No Pic</div>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="font-bold block">{{ $report->item_name }}</span>
                                                <span class="text-xs text-gray-500">{{ $report->category }} | {{ $report->color }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3 px-6 text-left">
                                        <div class="font-bold text-gray-700">{{ $report->passenger_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->passenger_phone }}</div>
                                    </td>

                                    <td class="py-3 px-6 text-left">
                                        <div>{{ $report->lost_location }}</div>
                                        @if($report->flight_number)
                                            <div class="text-xs text-blue-500 font-bold">Flight: {{ $report->flight_number }}</div>
                                        @endif
                                    </td>

                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs font-bold uppercase">{{ $report->status }}</span>
                                    </td>

                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('staff.lost-items.show', $report->id) }}" 
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-blue-700 shadow-sm transition">
                                            ⚡ Match
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
                                        No lost reports found. Click the button to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $lostReports->links() }}</div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>