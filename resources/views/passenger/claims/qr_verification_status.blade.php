<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Pickup Pass | Airport Lost &amp; Found</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-900 flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-black tracking-tight text-white">
                Airport Lost &amp; Found
            </h1>
            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.3em] text-slate-500">
                Official Digital Voucher
            </p>
        </div>

        @php
            $qrData = QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->color(15, 23, 42)
                ->generate(route('pickup.verify', ['token' => $match->verification_token]));

            $qrSource = 'data:image/png;base64,' . base64_encode($qrData);
        @endphp

        <x-pickup-ticket :match="$match" :qrSource="$qrSource" />

        <div class="mt-10 flex justify-center gap-8">
            <button
                onclick="window.print()"
                class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-500 transition hover:text-emerald-400"
            >
                <i class="fas fa-print text-sm"></i>
                Print Pass
            </button>

            <div class="h-4 w-px bg-slate-800"></div>

            <a
                href="{{ route('passenger.history') }}"
                class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-500 no-underline transition hover:text-blue-400"
            >
                <i class="fas fa-history text-sm"></i>
                My History
            </a>
        </div>
    </div>
</body>
</html>