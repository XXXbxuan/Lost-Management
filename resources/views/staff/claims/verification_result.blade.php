<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('staff.lost-items.index') }}"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white transition hover:bg-slate-50"
            >
                <span class="text-xl leading-none">‹</span>
            </a>

            <h2 class="text-xl font-bold leading-tight text-gray-800">
                {{ __('👁️ Stage 1: Identity & Item Verification') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-10 grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="relative rounded-[2.5rem] border-t-8 border-slate-900 bg-white p-8 shadow-xl">
                    <div class="flex flex-col items-center text-center">
                        <span class="mb-6 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500">
                            Passenger Profile
                        </span>

                        <div class="w-full space-y-4 text-left">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Full Name
                                </p>
                                <p class="text-lg font-bold text-slate-800">
                                    {{ $match->lostItem->passenger_name }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Gmail / Email
                                </p>
                                <p class="break-all text-sm font-bold text-slate-600">
                                    {{ $match->lostItem->passenger_email }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Contact Number
                                </p>
                                <p class="text-sm font-bold text-slate-600">
                                    {{ $match->lostItem->passenger_phone ?? 'No phone provided' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Lost Report Reference
                                </p>
                                <p class="text-sm font-black text-slate-800">
                                    #L-{{ $match->lostId }}
                                </p>
                            </div>

                            <div class="col-span-2 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Verification Notes
                                </p>
                                <p class="text-sm leading-relaxed text-slate-600">
                                    {{ $match->notes ?? 'No verification notes.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative rounded-[2.5rem] border-t-8 border-blue-600 bg-white p-8 shadow-xl">
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-blue-700">
                        Found Item Details
                    </span>

                    <div class="mb-6 mt-6 flex aspect-video items-center justify-center overflow-hidden rounded-3xl border-4 border-slate-50 bg-slate-50 shadow-inner">
                        @if (!empty($match->foundItem->image_path))
                            <img
                                src="{{ asset('storage/' . $match->foundItem->image_path) }}"
                                alt="Found Item Image"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <span class="text-sm font-bold uppercase tracking-widest text-slate-300">
                                No Image
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 rounded-2xl border border-blue-100 bg-blue-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-blue-400">
                                Item Name
                            </p>
                            <p class="text-lg font-black text-blue-900">
                                {{ $match->foundItem->item_name }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Category
                            </p>
                            <p class="text-sm font-black text-slate-800">
                                {{ $match->foundItem->category ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Serial Number
                            </p>
                            <p class="text-sm font-black text-slate-800">
                                {{ $match->foundItem->serial_number ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Color
                            </p>
                            <p class="text-sm font-black text-slate-800">
                                {{ $match->foundItem->color ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Brand
                            </p>
                            <p class="text-sm font-black text-slate-800">
                                {{ $match->foundItem->brand ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="col-span-2 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Description
                            </p>
                            <p class="text-sm leading-relaxed text-slate-600">
                                {{ $match->foundItem->description ?? 'No description.' }}
                            </p>
                        </div>

                        <div class="col-span-2 rounded-2xl border-2 border-amber-200 bg-amber-50 p-5">
                            <p class="mb-1 text-[10px] font-black uppercase tracking-widest text-amber-600">
                                📍 Storage Location (Grab item here)
                            </p>
                            <p class="text-2xl font-black text-amber-900">
                                {{ $match->foundItem->storage_location ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-6">
                <form
                    action="{{ route('staff.match.store') }}"
                    method="POST"
                    onsubmit="return confirm('Reject this claim (mark as NOT MATCHED)?');"
                >
                    @csrf

                    <input type="hidden" name="lost_id" value="{{ $match->lostId }}">
                    <input type="hidden" name="found_id" value="{{ $match->foundId }}">
                    <input type="hidden" name="outcome" value="not_matched">
                    <input type="hidden" name="notes" value="Rejected at Stage 1 (Identity & Item Verification)">
                    <input type="hidden" name="similarity_score" value="{{ $match->similarityScore ?? 0 }}">
                    <input type="hidden" name="return_url" value="{{ route('staff.lost-items.index') }}">
                    <input type="hidden" name="source" value="reject_claim">

                    <button
                        type="submit"
                        class="flex items-center gap-2 rounded-2xl border-2 border-red-100 bg-white px-10 py-4 font-black text-red-600 transition hover:bg-red-50"
                    >
                        <span class="text-xl">＋</span>
                        REJECT CLAIM
                    </button>
                </form>

                <a
                    href="{{ route('staff.claims.handover', ['id' => $match->id, 'step' => 2]) }}"
                    class="flex items-center gap-3 rounded-2xl bg-slate-900 px-12 py-4 font-black text-white shadow-2xl transition hover:scale-105 hover:bg-black"
                >
                    VERIFIED - PROCEED TO HANDOVER
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>