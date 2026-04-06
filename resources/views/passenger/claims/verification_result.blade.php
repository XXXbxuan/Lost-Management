<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Staff Verification - Match #{{ $match->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between bg-blue-700 p-4 text-white">
                    <span class="font-bold uppercase tracking-tight">
                        <i class="fas fa-user-shield mr-2"></i>Staff Verification
                    </span>
                    <span class="rounded bg-blue-500 px-2 py-1 text-xs">
                        MATCH #{{ $match->id }}
                    </span>
                </div>

                <div class="p-6">
                    <div class="mb-8 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="mb-2 text-[10px] font-bold uppercase text-red-500">
                                Passenger Report
                            </p>
                            <div class="aspect-square overflow-hidden rounded-xl border-2 border-red-100 shadow-sm">
                                <img
                                    src="{{ asset('storage/' . $match->lostItem->image_path) }}"
                                    alt="Passenger Report Image"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                        </div>

                        <div class="text-center">
                            <p class="mb-2 text-[10px] font-bold uppercase text-blue-500">
                                Physical Found Item
                            </p>
                            <div class="aspect-square overflow-hidden rounded-xl border-2 border-blue-100 shadow-sm">
                                <img
                                    src="{{ asset('storage/' . $match->foundItem->image_path) }}"
                                    alt="Physical Found Item Image"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 space-y-3 rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <h3 class="mb-2 border-b pb-2 text-sm font-bold text-gray-700">
                            Passenger Information
                        </h3>

                        <p class="text-sm">
                            <span class="text-gray-500">Name:</span>
                            <strong>{{ $match->lostItem->passenger_name }}</strong>
                        </p>

                        <p class="text-sm">
                            <span class="text-gray-500">ID / IC Number:</span>
                            <strong>{{ $match->lostItem->ic_number }}</strong>
                        </p>
                    </div>

                    <form
                        action="{{ route('staff.claims.handover', $match->id) }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-xl bg-blue-600 py-4 font-bold text-white shadow-lg transition-all hover:bg-blue-700 active:scale-95"
                            onclick="return confirm('WARNING: Are you sure you have verified the identity and handed over the item?')"
                        >
                            <i class="fas fa-handshake mr-2 text-xl"></i>
                            CONFIRM ITEM HANDOVER
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>