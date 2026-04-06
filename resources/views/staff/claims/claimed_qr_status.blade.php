<x-app-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md rounded-3xl border-t-8 border-rose-500 bg-white p-8 text-center shadow-xl">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-rose-100">
                <i class="fas fa-exclamation-triangle text-3xl text-rose-500"></i>
            </div>

            <h2 class="mb-2 text-3xl font-black tracking-tight text-gray-900">
                Already Claimed
            </h2>

            <p class="mb-6 text-gray-500">
                This item has already been handed over. The QR code is no longer valid.
            </p>

            <div class="mb-6 rounded-xl bg-gray-50 p-4 text-left">
                <p class="text-sm text-gray-600">
                    <strong>Item:</strong> {{ $match->foundItem->item_name }}
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Match ID:</strong> #{{ $match->id }}
                </p>
            </div>

            <a
                href="{{ route('staff.claims.process', $match->id) }}"
                class="inline-block w-full rounded-xl bg-slate-900 px-4 py-3 font-bold text-white transition hover:bg-slate-800"
            >
                Return to Dashboard
            </a>
        </div>
    </div>
</x-app-layout>