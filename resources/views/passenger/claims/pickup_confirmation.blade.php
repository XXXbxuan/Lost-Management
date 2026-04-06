<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Your Pickup | Airport Lost &amp; Found</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen items-center justify-center">
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-md overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-2xl">
            <div class="h-3 bg-blue-600"></div>

            <div class="p-8 md:p-10">
                <div class="mb-8 text-center">
                    <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 shadow-inner">
                        <i class="fas fa-calendar-check text-3xl text-blue-600"></i>
                    </div>

                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">
                        Final Confirmation
                    </h1>

                    <p class="mt-2 text-sm leading-relaxed text-slate-500">
                        Please review your appointment details carefully. Your digital pickup pass will be generated upon confirmation.
                    </p>
                </div>

                @if (session('error'))
                    <div class="mb-6 flex items-center rounded-r-xl border-l-4 border-red-500 bg-red-50 p-4 text-sm text-red-700">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-8 space-y-6 rounded-3xl border border-slate-100 bg-slate-50 p-6">
                    <div class="flex items-start">
                        <div class="mr-4 mt-1 rounded-lg bg-white p-2 text-sm text-blue-500 shadow-sm">
                            <i class="fas fa-tag"></i>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">
                                Item to be Claimed
                            </label>
                            <p class="font-bold leading-tight text-slate-800">
                                {{ $match->foundItem->item_name }}
                            </p>
                            <p class="mt-1 text-xs italic text-slate-500">
                                {{ \Illuminate\Support\Str::limit($match->foundItem->description, 60) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="mr-4 mt-1 rounded-lg bg-white p-2 text-sm text-emerald-500 shadow-sm">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">
                                Appointment Date &amp; Time
                            </label>
                            <p class="font-bold text-slate-800">
                                {{ \Carbon\Carbon::parse($match->appointment_at)->format('d M Y') }}
                            </p>
                            <p class="text-sm font-semibold text-emerald-600">
                                {{ \Carbon\Carbon::parse($match->appointment_at)->format('h:i A') }}
                                <span class="ml-1 text-[10px] font-normal text-slate-400">(Local Time)</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="mr-4 mt-1 rounded-lg bg-white p-2 text-sm text-orange-500 shadow-sm">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">
                                Pickup Venue
                            </label>
                            <p class="font-bold text-slate-800">
                                {{ $match->appointment_venue ?? 'Airport Lost & Found Office' }}
                            </p>
                            <p class="mt-1 text-[10px] font-semibold uppercase text-slate-400">
                                Terminal 1, Level 2, Arrival Hall
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pickup.process', ['token' => $match->verification_token]) }}" method="POST">
                    @csrf

                    <button
                        type="submit"
                        class="group relative flex w-full items-center justify-center overflow-hidden rounded-2xl bg-slate-900 py-5 font-bold text-white shadow-xl transition-all hover:bg-blue-700 active:scale-[0.98]"
                    >
                        <div class="absolute inset-0 h-full w-1/2 -translate-x-full skew-x-[-30deg] bg-white/10 transition-transform duration-700 group-hover:translate-x-[250%]"></div>

                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <span class="tracking-wide">CONFIRM ATTENDANCE</span>
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-100 pt-6 text-center">
                    <div class="mb-3 flex items-center justify-center gap-2 text-[10px] font-bold uppercase tracking-widest text-rose-500">
                        <i class="fas fa-shield-alt"></i>
                        <span>Identity Verification Required</span>
                    </div>

                    <p class="text-[10px] font-medium leading-relaxed text-slate-400">
                        You must bring your <span class="text-slate-600">Original Passport or IC</span> for physical verification.
                        Digital copies will not be accepted.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 space-x-4 text-center">
            <a href="#" class="text-xs font-medium text-slate-400 transition hover:text-blue-500">Privacy Policy</a>
            <span class="text-slate-300">|</span>
            <a href="#" class="text-xs font-medium text-slate-400 transition hover:text-blue-500">Support Center</a>
        </div>
    </div>
</body>
</html>