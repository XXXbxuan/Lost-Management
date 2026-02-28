<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Pickup Pass | Airport Lost & Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-white text-xl font-bold tracking-tight">Airport Lost & Found</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest">Official Digital Voucher</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-8 text-white text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-full mb-4 animate-pulse">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight">Ready for Pickup</h2>
                <p class="text-emerald-100 text-sm mt-1 opacity-90">Valid at Lost & Found Counter</p>
            </div>

            <div class="p-8">
                <div class="flex justify-center mb-8">
                    <div class="p-4 bg-white rounded-3xl shadow-[inset_0_2px_10px_rgba(0,0,0,0.05)] border-2 border-dashed border-slate-200 relative">
                        <div class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-emerald-500 rounded-tl-md"></div>
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-emerald-500 rounded-br-md"></div>
                        
                        <div class="bg-white p-2 rounded-xl">
                            {{-- 生成指向 smartVerify 的連結 --}}
                            {!! QrCode::size(220)->margin(1)->color(15, 23, 42)->generate(route('pickup.verify', ['token' => $match->verification_token])) !!}
                        </div>
                    </div>
                </div>

                <div class="space-y-4 bg-slate-50 rounded-3xl p-6 border border-slate-100">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Passenger</span>
                        <span class="text-sm font-extrabold text-slate-700 uppercase">
                            {{ $match->lostItem->passenger_name }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Appointment</span>
                        <span class="text-sm font-extrabold text-slate-700">
                            {{ \Carbon\Carbon::parse($match->appointment_at)->format('d M, h:i A') }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Venue</span>
                        <span class="text-sm font-extrabold text-slate-700">{{ $match->appointment_venue ?? 'T1 Main Office' }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-200">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Item Ref</span>
                        <span class="text-sm font-bold text-blue-600 leading-tight block">
                             {{ $match->foundItem->item_name }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 text-center px-4">
                    <p class="text-[10px] text-slate-400 leading-relaxed uppercase tracking-[0.2em] font-bold">
                        Token: {{ $match->verification_token }}
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-2 text-rose-500 font-black text-[10px] uppercase tracking-widest border border-rose-100 bg-rose-50 py-2 rounded-full">
                        <i class="fas fa-id-card"></i>
                        <span>Original ID Required</span>
                    </div>
                </div>
            </div>

            <div class="relative h-6 bg-slate-50 border-t border-dashed border-slate-200">
                <div class="absolute -top-3 -left-3 w-6 h-6 bg-slate-900 rounded-full"></div>
                <div class="absolute -top-3 -right-3 w-6 h-6 bg-slate-900 rounded-full"></div>
            </div>
        </div>

        <div class="mt-8 flex justify-center gap-6">
            <button onclick="window.print()" class="text-slate-500 text-xs font-bold hover:text-white transition flex items-center gap-2">
                <i class="fas fa-print"></i> PRINT PASS
            </button>
            <span class="text-slate-700">|</span>
            <a href="{{ route('passenger.history') }}" class="text-slate-500 text-xs font-bold hover:text-blue-400 transition flex items-center gap-2 no-underline">
                <i class="fas fa-history"></i> MY HISTORY
            </a>
        </div>
    </div>

</body>
</html>