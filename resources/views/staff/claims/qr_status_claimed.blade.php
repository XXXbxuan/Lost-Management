<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 text-center border-t-8 border-rose-500">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-100 mb-6">
                <i class="fas fa-exclamation-triangle text-rose-500 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-2 tracking-tight">Already Claimed</h2>
            <p class="text-gray-500 mb-6">This item has already been handed over. The QR code is no longer valid.</p>
            
            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm text-gray-600"><strong>Item:</strong> {{ $match->foundItem->item_name }}</p>
                <p class="text-sm text-gray-600"><strong>Match ID:</strong> #{{ $match->id }}</p>
            </div>

            <a href="{{ route('staff.claims.process', $match->id) }}" class="inline-block w-full bg-slate-900 text-white font-bold py-3 px-4 rounded-xl hover:bg-slate-800 transition">
                Return to Dashboard
            </a>
        </div>
    </div>
</x-app-layout>