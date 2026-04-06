<x-app-layout>
    @if (Auth::user()->role === 'Passenger')
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Passenger Portal') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-8 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-4 text-cyan-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <svg class="h-5 w-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Login Successful</div>
                                <div class="text-sm">{{ session('success') }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-800 shadow-lg">
                    <div class="absolute right-0 top-0 -mr-4 -mt-4 text-white opacity-10">
                        <svg class="h-48 w-48" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 16v-2l-8.5-5V3.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5V9L2 14v2l8.5-2.5V19L8 20.5V22l4-1 4 1v-1.5L13.5 19v-5.5L22 16z"/>
                        </svg>
                    </div>

                    <div class="relative z-10 px-8 py-10">
                        <h2 class="mb-2 text-3xl font-extrabold text-white">
                            Welcome, {{ Auth::user()->name }}!
                        </h2>
                        <p class="max-w-2xl text-lg text-blue-100">
                            The official Airport Lost &amp; Found portal. Have you misplaced your luggage or found an item in the terminal? Select an option below to get started.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <a
                        href="{{ route('passenger.report') }}"
                        class="group rounded-xl border border-gray-100 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:border-red-200 hover:shadow-xl"
                    >
                        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-800">Report Lost Item</h3>
                        <p class="text-sm text-gray-500">
                            File a report for missing luggage, electronics, or personal belongings.
                        </p>
                    </a>

                    <a
                        href="{{ route('passenger.found_items') }}"
                        class="group rounded-xl border border-gray-100 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:border-green-200 hover:shadow-xl"
                    >
                        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-800">Browse Found Items</h3>
                        <p class="text-sm text-gray-500">
                            Search our real-time database of items recovered by airport security and staff.
                        </p>
                    </a>

                    <a
                        href="{{ route('passenger.rewards') }}"
                        class="group relative overflow-hidden rounded-xl border border-gray-100 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl"
                    >
                        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-800">Rewards Center</h3>
                        <p class="text-sm text-gray-500">Spend your points on airport vouchers.</p>

                        <div class="absolute right-6 top-6 flex items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1 shadow-sm">
                            <span class="font-bold text-blue-700">{{ Auth::user()->points }}</span>
                            <span class="ml-1 text-xs font-semibold uppercase text-blue-500">pts</span>
                        </div>
                    </a>
                </div>

                <div class="mt-8 rounded-2xl border border-gray-200 bg-gray-50 p-8">
                    <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-700">
                        How the Airport Protocol Works
                    </h3>

                    <div class="grid grid-cols-1 gap-6 text-sm text-gray-600 md:grid-cols-3">
                        <div>
                            <strong class="mb-1 block text-gray-800">1. Secure Handover</strong>
                            If you find an item in the terminal or on an airplane, please hand it to the nearest staff counter.
                        </div>

                        <div>
                            <strong class="mb-1 block text-gray-800">2. Earn Points</strong>
                            Provide your registered email to the staff. You will instantly receive 100 points as a thank you!
                        </div>

                        <div>
                            <strong class="mb-1 block text-gray-800">3. Security Verification</strong>
                            High-value items like Passports and Laptops are transferred to Airport Police after 24 hours.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @php
            $user = Auth::user();
            $isAdmin = $user->role === 'Admin';
            $analyticsRoute = $isAdmin ? route('admin.dashboard') : route('staff.dashboard');
        @endphp

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-8 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-4 text-cyan-800 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <svg class="h-5 w-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Login Successful</div>
                                <div class="text-sm">{{ session('success') }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-700 shadow-lg">
                    <div class="absolute right-0 top-0 translate-x-6 -translate-y-4 text-white opacity-10">
                        <svg class="h-56 w-56" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 16v-2l-8.5-5V3.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5V9L2 14v2l8.5-2.5V19L8 20.5V22l4-1 4 1v-1.5L13.5 19v-5.5L22 16z"/>
                        </svg>
                    </div>

                    <div class="relative z-10 px-8 py-10 md:px-10 md:py-12">
                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center rounded-full border border-white/20 bg-white/15 px-4 py-1.5 text-sm font-semibold text-white">
                                {{ $user->role }} Access
                            </span>
                            <span class="inline-flex items-center rounded-full border border-white/20 bg-white/15 px-4 py-1.5 text-sm font-semibold text-white">
                                Airport Lost &amp; Found System
                            </span>
                        </div>

                        <h2 class="mb-3 text-3xl font-extrabold text-white md:text-4xl">
                            Welcome back, {{ $user->name }}!
                        </h2>

                        <p class="max-w-3xl text-base leading-relaxed text-blue-100 md:text-lg">
                            Manage airport lost reports, found item registration, claim processing, vouchers, and operational tracking from one centralized portal. Use the quick navigation cards below to access each module faster.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a
                                href="{{ $analyticsRoute }}"
                                class="inline-flex items-center rounded-lg bg-white px-5 py-2.5 font-semibold text-indigo-700 shadow transition hover:bg-gray-100"
                            >
                                View Analytics Chart
                            </a>

                            <a
                                href="{{ route('staff.found-items.index') }}"
                                class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 px-5 py-2.5 font-semibold text-white transition hover:bg-white/15"
                            >
                                Open Found Items
                            </a>

                            <a
                                href="{{ route('staff.lost-items.index') }}"
                                class="inline-flex items-center rounded-lg border border-white/20 bg-white/10 px-5 py-2.5 font-semibold text-white transition hover:bg-white/15"
                            >
                                Open Lost Reports
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                    @if ($isAdmin)
                        <a
                            href="{{ route('admin.staff.index') }}"
                            class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl"
                        >
                            <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 transition group-hover:scale-110">
                                <svg class="h-7 w-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-11a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <h3 class="mb-2 text-lg font-bold text-gray-800">Staff Management</h3>
                            <p class="text-sm text-gray-500">
                                Create, update, block, or review staff accounts and their operational details.
                            </p>
                        </a>

                        <a
                            href="{{ route('admin.logs.index') }}"
                            class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-slate-200 hover:shadow-xl"
                        >
                            <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 transition group-hover:scale-110">
                                <svg class="h-7 w-7 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6m2 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586A1 1 0 0113.293 4.293l3.414 3.414A1 1 0 0117 8.414V18a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="mb-2 text-lg font-bold text-gray-800">Audit Logs</h3>
                            <p class="text-sm text-gray-500">
                                Track important admin activities, changes, and historical system actions.
                            </p>
                        </a>
                    @endif

                    <a
                        href="{{ $analyticsRoute }}"
                        class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl"
                    >
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19V6m-4 13V10m8 9V4m4 15v-7"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Analytics Chart</h3>
                        <p class="text-sm text-gray-500">
                            View item distribution, hotspot locations, total reports, and recovery success rate.
                        </p>
                    </a>

                    <a
                        href="{{ route('staff.found-items.index') }}"
                        class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl"
                    >
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 8V7a2 2 0 00-2-2h-3V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v1H6a2 2 0 00-2 2v1m16 0H4"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Found Items</h3>
                        <p class="text-sm text-gray-500">
                            Register, review, edit, and manage all found property records in the airport system.
                        </p>
                    </a>

                    <a
                        href="{{ route('staff.lost-items.index') }}"
                        class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-rose-200 hover:shadow-xl"
                    >
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h8M8 10h8M8 14h4m5.707 4.293a1 1 0 010 1.414l-.586.586a1 1 0 01-1.414 0L13 18l2.707-2.707a1 1 0 011.414 0l.586.586zM6 20h4"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Lost Reports</h3>
                        <p class="text-sm text-gray-500">
                            Handle reported missing items, review details, and start the match verification process.
                        </p>
                    </a>

                    <a
                        href="{{ route('staff.claims.index') }}"
                        class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl"
                    >
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Claim History</h3>
                        <p class="text-sm text-gray-500">
                            Monitor appointment confirmation, QR verification, handover completion, and claim records.
                        </p>
                    </a>

                    <a
                        href="{{ route('staff.vouchers.index') }}"
                        class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl"
                    >
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-cyan-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.5 4H7a2 2 0 00-2 2v3a2 2 0 012 2 2 2 0 01-2 2v3a2 2 0 002 2h7.5a2.5 2.5 0 000-5 2.5 2.5 0 010-5zM16 4l4 4-4 4"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Vouchers</h3>
                        <p class="text-sm text-gray-500">
                            Manage reward vouchers available for passenger redemption and airport loyalty incentives.
                        </p>
                    </a>

                    <a
                        href="{{ route('staff.ai-chat.index') }}"
                        class="group rounded-2xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-violet-200 hover:shadow-xl"
                    >
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-violet-100 transition group-hover:scale-110">
                            <svg class="h-7 w-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">AI Help Assistant</h3>
                        <p class="text-sm text-gray-500">
                            Use smart search and command shortcuts to locate records and speed up system operations.
                        </p>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm xl:col-span-2">
                        <h3 class="mb-2 text-xl font-bold text-gray-800">
                            How the Airport Lost &amp; Found Workflow Operates
                        </h3>
                        <p class="mb-6 text-sm text-gray-500">
                            This portal supports the full operational flow from item intake until final passenger handover.
                        </p>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
                                <div class="mb-1 text-sm font-bold text-blue-700">1. Receive and Register</div>
                                <p class="text-sm text-gray-600">
                                    Staff records newly found property details, image, category, and storage location into the system.
                                </p>
                            </div>

                            <div class="rounded-xl border border-rose-100 bg-rose-50 p-5">
                                <div class="mb-1 text-sm font-bold text-rose-700">2. Review Lost Reports</div>
                                <p class="text-sm text-gray-600">
                                    Lost item reports are checked against existing found item records for possible candidate matches.
                                </p>
                            </div>

                            <div class="rounded-xl border border-amber-100 bg-amber-50 p-5">
                                <div class="mb-1 text-sm font-bold text-amber-700">3. Verify and Schedule Claim</div>
                                <p class="text-sm text-gray-600">
                                    After match verification, the claimant can confirm pickup and staff can prepare the handover process.
                                </p>
                            </div>

                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-5">
                                <div class="mb-1 text-sm font-bold text-emerald-700">4. Handover and Close Case</div>
                                <p class="text-sm text-gray-600">
                                    QR and identity checks are completed before the item is handed over and the case is marked as claimed.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-8">
                        <h3 class="mb-4 text-xl font-bold text-gray-800">Portal Guide</h3>

                        <div class="space-y-4 text-sm text-gray-600">
                            <div class="border-b border-gray-200 pb-4">
                                <strong class="mb-1 block text-gray-800">Dashboard Purpose</strong>
                                This page acts as a quick navigation hub for daily airport lost and found operations.
                            </div>

                            <div class="border-b border-gray-200 pb-4">
                                <strong class="mb-1 block text-gray-800">Recommended Start</strong>
                                Open <span class="font-semibold text-gray-800">Found Items</span> to register new items or <span class="font-semibold text-gray-800">Lost Reports</span> to begin match checking.
                            </div>

                            <div class="border-b border-gray-200 pb-4">
                                <strong class="mb-1 block text-gray-800">Claim Monitoring</strong>
                                Use <span class="font-semibold text-gray-800">Claim History</span> to monitor confirmation, handover, and completed claim records.
                            </div>

                            <div>
                                <strong class="mb-1 block text-gray-800">Operational Insight</strong>
                                Use <span class="font-semibold text-gray-800">Analytics Chart</span> to view recovery trends and hotspot locations for reporting.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>