<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📜 Claims History (Audit Log)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-sm text-gray-500">Total Records: <span class="font-bold text-gray-800">{{ $claims->total() }}</span></p>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Item Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Claimer Identity</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Processed By</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Proof</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($claims as $claim)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-bold text-gray-700">{{ $claim->claimedAt->format('Y-m-d') }}</div>
                                        <div class="text-xs">{{ $claim->claimedAt->format('h:i A') }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-indigo-700">{{ $claim->foundItem->item_name ?? 'Item Deleted' }}</div>
                                        <div class="text-xs text-gray-500">{{ $claim->foundItem->category ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Ref ID: #{{ $claim->foundItem->id ?? '?' }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $claim->claimerName }}</div>
                                        <div class="flex items-center text-xs text-gray-600 mt-1">
                                            <span class="bg-gray-200 px-1 rounded mr-1">IC</span> 
                                            {{ $claim->claimerIcPassport }}
                                        </div>
                                        <div class="text-xs text-gray-500">📞 {{ $claim->claimerPhone }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                ST
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-medium text-gray-900">{{ $claim->handler->name ?? 'Staff #'.$claim->processedBy }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-gray-400 hover:text-indigo-600 cursor-not-allowed" title="PDF Feature coming soon">
                                            📄 Receipt
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        No claims record found yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $claims->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>