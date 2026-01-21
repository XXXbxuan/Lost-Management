<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('🚨 Passenger Lost Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">All Lost Reports</h3>
                        
                        <a href="{{ route('staff.lost-items.create') }}" 
                           class="bg-red-600 text-white px-4 py-2 rounded-md font-bold hover:bg-red-700 transition shadow-md no-underline flex items-center gap-2">
                           <span class="text-lg">+</span> Create Lost Report
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal border-b">
                                    <th class="py-4 px-6 text-left font-bold">Date</th>
                                    <th class="py-4 px-6 text-left font-bold">Item Details</th>
                                    <th class="py-4 px-6 text-left font-bold">Passenger Info</th>
                                    <th class="py-4 px-6 text-left font-bold">Lost Location</th>
                                    <th class="py-4 px-6 text-center font-bold">Status</th>
                                    <th class="py-4 px-6 text-center font-bold">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm">
                                @forelse($lostReports as $report)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                        <td class="py-4 px-6 text-left whitespace-nowrap">
                                            <div class="font-bold text-gray-800">{{ $report->created_at->format('Y-m-d') }}</div>
                                            <div class="text-[11px] text-gray-400 italic">{{ $report->created_at->format('h:i A') }}</div>
                                        </td>

                                        <td class="py-4 px-6 text-left">
                                            <div class="flex items-center">
                                                <div class="mr-3 flex-shrink-0">
                                                    @if($report->image_path)
                                                        <img src="{{ asset('storage/' . $report->image_path) }}" class="w-12 h-12 rounded shadow-sm border object-cover">
                                                    @else
                                                        <div class="w-12 h-12 rounded border bg-gray-50 flex items-center justify-center text-[10px] text-gray-300 italic">No Pic</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="font-bold block text-gray-800">{{ $report->item_name }}</span>
                                                    <span class="text-[11px] text-gray-500">{{ $report->category }} | {{ $report->color }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="py-4 px-6 text-left">
                                            <div class="font-bold text-gray-700">{{ $report->passenger_name }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $report->passenger_phone }}</div>
                                        </td>

                                        <td class="py-4 px-6 text-left">
                                            <div class="text-gray-700">{{ $report->lost_location }}</div>
                                            @if($report->flight_number)
                                                <div class="text-[11px] text-blue-500 font-semibold bg-blue-50 px-1.5 py-0.5 rounded inline-block mt-1">Flight: {{ $report->flight_number }}</div>
                                            @endif
                                        </td>

                                        <td class="py-4 px-6 text-center">
                                            @if($report->status == 'Matched')
                                                <span class="bg-emerald-100 text-emerald-700 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                                                    Matched
                                                </span>
                                            @elseif($report->status == 'Claimed')
                                                <span class="bg-gray-100 text-gray-500 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-gray-200">
                                                    Claimed
                                                </span>
                                            @else
                                                <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-red-200">
                                                    LOST
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-4 px-6 text-center">
                                            @if($report->status == 'Matched')
                                                <div class="flex flex-col items-center gap-2">
                                                    <a href="{{ route('staff.claims.create', ['lost_id' => $report->id]) }}" 
                                                    class="bg-emerald-600 text-white px-4 py-1.5 rounded text-[11px] font-black hover:bg-emerald-700 shadow-sm transition no-underline uppercase">
                                                        🛠️ Manage Claim
                                                    </a>
                                                    <form action="{{ route('staff.match.unmatch', $report->id) }}" method="POST" onsubmit="return confirm('Undo this match?')">
                                                        @csrf
                                                        <button type="submit" class="text-[10px] text-gray-400 hover:text-red-500 underline font-bold bg-transparent border-none cursor-pointer">
                                                            Undo Match
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($report->status == 'Claimed')
                                                <span class="text-gray-400 font-bold italic text-xs">✅ Handed Over</span>
                                            @else
                                                <a href="{{ route('staff.lost-items.show', $report->id) }}" 
                                                class="bg-blue-600 text-white px-4 py-1.5 rounded text-[11px] font-black hover:bg-blue-700 shadow-sm transition no-underline uppercase inline-flex items-center gap-1">
                                                    ⚡ Match
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-gray-400 italic">
                                            No lost reports found. Click "+ Create Lost Report" to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $lostReports->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>