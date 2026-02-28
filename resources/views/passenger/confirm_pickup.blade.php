<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Your Pickup | Airport Lost & Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
            
            <div class="h-3 bg-blue-600"></div>

            <div class="p-8 md:p-10">
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-50 rounded-full mb-4 shadow-inner">
                        <i class="fas fa-calendar-check text-blue-600 text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Final Confirmation</h1>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">Please review your appointment details carefully. Your digital pickup pass will be generated upon confirmation.</p>
                </div>

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="bg-slate-50 rounded-3xl p-6 mb-8 space-y-6 border border-slate-100">
                    <div class="flex items-start">
                        <div class="mt-1 mr-4 text-blue-500 bg-white p-2 rounded-lg shadow-sm text-sm"><i class="fas fa-tag"></i></div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] block mb-1">Item to be Claimed</label>
                            <p class="font-bold text-slate-800 leading-tight">{{ $match->foundItem->item_name }}</p>
                            <p class="text-xs text-slate-500 mt-1 italic">{{ Str::limit($match->foundItem->description, 60) }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="mt-1 mr-4 text-emerald-500 bg-white p-2 rounded-lg shadow-sm text-sm"><i class="fas fa-clock"></i></div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] block mb-1">Appointment Date & Time</label>
                            <p class="font-bold text-slate-800">
                                {{ \Carbon\Carbon::parse($match->appointment_at)->format('d M Y') }}
                            </p>
                            <p class="text-sm font-semibold text-emerald-600">
                                {{ \Carbon\Carbon::parse($match->appointment_at)->format('h:i A') }} <span class="text-[10px] font-normal text-slate-400 ml-1">(Local Time)</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="mt-1 mr-4 text-orange-500 bg-white p-2 rounded-lg shadow-sm text-sm"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] block mb-1">Pickup Venue</label>
                            <p class="font-bold text-slate-800">{{ $match->appointment_venue ?? 'Airport Lost & Found Office' }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold">Terminal 1, Level 2, Arrival Hall</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pickup.process', ['token' => $match->verification_token]) }}" method="POST">
                    @csrf
                    <button type="submit" class="group relative w-full bg-slate-900 hover:bg-blue-700 text-white font-bold py-5 rounded-2xl shadow-xl transition-all active:scale-[0.98] flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 w-1/2 h-full bg-white/10 skew-x-[-30deg] -translate-x-full group-hover:translate-x-[250%] transition-transform duration-700"></div>
                        
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <span class="tracking-wide">CONFIRM ATTENDANCE</span>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <div class="flex items-center justify-center gap-2 text-rose-500 font-bold text-[10px] uppercase tracking-widest mb-3">
                        <i class="fas fa-shield-alt"></i>
                        <span>Identity Verification Required</span>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-relaxed font-medium">
                        You must bring your <span class="text-slate-600">Original Passport or IC</span> for physical verification. Digital copies will not be accepted.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center space-x-4">
            <a href="#" class="text-slate-400 text-xs hover:text-blue-500 transition font-medium">Privacy Policy</a>
            <span class="text-slate-300">|</span>
            <a href="#" class="text-slate-400 text-xs hover:text-blue-500 transition font-medium">Support Center</a>
        </div>
    </div>

</body>
</html>