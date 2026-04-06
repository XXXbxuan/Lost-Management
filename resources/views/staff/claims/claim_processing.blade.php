<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a
                    href="{{ route('staff.lost-items.index') }}"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white shadow-sm transition-all hover:bg-slate-50"
                >
                    <svg class="h-5 w-5 text-slate-600 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <h2 class="text-xl font-bold leading-tight text-gray-800">
                    {{ __('🔄 Claim Logistics & Scheduling') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 overflow-hidden rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">
                @include('staff.claims.partials.timeline')
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="h-fit rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm md:col-span-1">
                    <h3 class="mb-4 border-b pb-2 text-xs font-black uppercase tracking-widest text-slate-800">
                        📦 Warehouse Guide
                    </h3>

                    @if ($foundItem->image_path)
                        <img
                            src="{{ asset('storage/' . $foundItem->image_path) }}"
                            alt="Found Item Image"
                            class="mb-4 h-40 w-full rounded-2xl border bg-slate-50 object-cover shadow-inner"
                        >
                    @endif

                    <div class="space-y-1">
                        <p class="text-sm">
                            <strong>Item:</strong> {{ $foundItem->item_name }}
                        </p>
                        <p class="text-sm">
                            <strong>Storage:</strong>
                            <span class="rounded bg-amber-100 px-2 py-0.5 font-bold text-amber-800">
                                📍 {{ $foundItem->storage_location }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="md:col-span-2">
                    @if (is_null($match->appointment_at))
                        @php
                            $isRejected = ($match->status === 'Reschedule Requested') || !is_null($match->rejected_at);

                            $suggest1 = $match->suggested_time_1 ? \Carbon\Carbon::parse($match->suggested_time_1) : null;
                            $suggest2 = $match->suggested_time_2 ? \Carbon\Carbon::parse($match->suggested_time_2) : null;
                        @endphp

                        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
                            <h3 class="mb-4 text-lg font-bold uppercase tracking-tight text-blue-800">
                                📅 Step 1: Schedule Pickup
                            </h3>

                            @if ($isRejected && ($suggest1 || $suggest2))
                                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-[11px] font-black uppercase tracking-widest text-amber-700">
                                        Passenger Suggested New Time
                                    </p>

                                    @if ($match->suggested_remarks)
                                        <p class="mt-1 text-sm font-bold text-slate-700">
                                            Remark: {{ $match->suggested_remarks }}
                                        </p>
                                    @endif

                                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                        @if ($suggest1)
                                            <button
                                                type="button"
                                                class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 transition hover:border-indigo-300 hover:bg-indigo-50"
                                                onclick="applySuggestedTime('{{ $suggest1->format('Y-m-d') }}', '{{ $suggest1->format('H:i') }}')"
                                            >
                                                <div class="text-left">
                                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">
                                                        Suggested 1
                                                    </p>
                                                    <p class="text-sm font-black text-slate-900">
                                                        {{ $suggest1->format('Y-m-d') }} • {{ $suggest1->format('H:i') }}
                                                    </p>
                                                </div>
                                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500 font-black text-white">
                                                    ✓
                                                </div>
                                            </button>
                                        @endif

                                        @if ($suggest2)
                                            <button
                                                type="button"
                                                class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 transition hover:border-indigo-300 hover:bg-indigo-50"
                                                onclick="applySuggestedTime('{{ $suggest2->format('Y-m-d') }}', '{{ $suggest2->format('H:i') }}')"
                                            >
                                                <div class="text-left">
                                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">
                                                        Suggested 2
                                                    </p>
                                                    <p class="text-sm font-black text-slate-900">
                                                        {{ $suggest2->format('Y-m-d') }} • {{ $suggest2->format('H:i') }}
                                                    </p>
                                                </div>
                                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500 font-black text-white">
                                                    ✓
                                                </div>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('staff.claims.schedule') }}" method="POST">
                                @csrf

                                <input type="hidden" name="match_id" value="{{ $match->id }}">

                                <div class="mb-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <input
                                            id="appointment_date"
                                            type="date"
                                            name="appointment_date"
                                            value="{{ old('appointment_date') }}"
                                            required
                                            class="w-full rounded-xl border-gray-300"
                                        >
                                    </div>

                                    <div>
                                        <input
                                            id="appointment_time"
                                            type="time"
                                            name="appointment_time"
                                            value="{{ old('appointment_time') }}"
                                            required
                                            class="w-full rounded-xl border-gray-300"
                                        >
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-blue-600 py-3 font-black text-white shadow-lg transition hover:bg-blue-700"
                                >
                                    Set Appointment & Send Email
                                </button>
                            </form>
                        </div>

                        <script>
                            function applySuggestedTime(dateStr, timeStr) {
                                const dateInput = document.getElementById('appointment_date');
                                const timeInput = document.getElementById('appointment_time');

                                if (dateInput) dateInput.value = dateStr;
                                if (timeInput) timeInput.value = timeStr;
                            }
                        </script>
                    @elseif (!$match->is_confirmed)
                        <div class="rounded-[2rem] border border-gray-100 bg-white p-12 text-center shadow-sm">
                            <div class="mb-6 text-5xl animate-bounce">📩</div>

                            <h3 class="mb-2 text-xl font-bold uppercase tracking-widest text-gray-800">
                                Waiting for Passenger...
                            </h3>

                            <p class="mb-6 text-gray-500">
                                Waiting for the passenger to confirm the appointment time via email.
                            </p>

                            <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <div class="h-2 w-2 animate-ping rounded-full bg-blue-400"></div>
                                Auto-syncing status...
                            </div>
                        </div>

                        <script>
                            setInterval(function () {
                                fetch('{{ route('staff.claims.check_confirmation', $match->id) }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.is_confirmed) {
                                            window.location.reload();
                                        }
                                    });
                            }, 3000);

                            setInterval(function () {
                                fetch('{{ route('staff.claims.check_reschedule', $match->id) }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'refresh') {
                                            window.location.reload();
                                        }
                                    });
                            }, 3000);
                        </script>
                    @else
                        <div class="rounded-[2rem] border border-gray-100 bg-white p-12 text-center shadow-sm">
                            <div class="inline-block w-full rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50 p-12">
                                <div class="mb-6 flex justify-center text-slate-300 animate-pulse">
                                    <i class="fas fa-qrcode text-6xl"></i>
                                </div>

                                <h3 class="mb-2 text-2xl font-black uppercase tracking-tight text-slate-800">
                                    Passenger Arrived
                                </h3>

                                <p class="mb-8 font-medium text-slate-500">
                                    Scan the passenger's pickup pass to start Verification.
                                </p>

                                <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    <div class="h-2 w-2 animate-ping rounded-full bg-blue-400"></div>
                                    Waiting for QR Scan trigger...
                                </div>
                            </div>
                        </div>

                        <script>
                            setInterval(function () {
                                fetch('{{ route('staff.claims.check_scan') }}?current_id={{ $match->id }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success' && data.redirect_url) {
                                            window.location.href = data.redirect_url;
                                        }
                                    });
                            }, 2000);

                            setInterval(function () {
                                fetch('{{ route('staff.claims.check_reschedule', $match->id) }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'refresh') {
                                            window.location.reload();
                                        }
                                    });
                            }, 3000);
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>