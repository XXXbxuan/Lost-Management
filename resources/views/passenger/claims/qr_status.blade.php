<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Pickup Pass | Airport Lost & Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-white text-2xl font-black tracking-tight">Airport Lost & Found</h1>
            <p class="text-slate-500 text-[10px] uppercase tracking-[0.3em] mt-2 font-bold">Official Digital Voucher</p>
        </div>

        @php
            // 生成網頁用的 QR Code Source
            $qrData = QrCode::format('png')->size(300)->margin(1)->color(15, 23, 42)->generate(route('pickup.verify', ['token' => $match->verification_token]));
            $qrSource = 'data:image/png;base64,' . base64_encode($qrData);
        @endphp

        <x-pickup-ticket :match="$match" :qrSource="$qrSource" />

        <div class="mt-10 flex justify-center gap-8">
            <button onclick="window.print()" class="text-slate-500 text-[11px] font-black hover:text-emerald-400 transition flex items-center gap-2 uppercase tracking-widest">
                <i class="fas fa-print text-sm"></i> Print Pass
            </button>
            <div class="w-px h-4 bg-slate-800"></div>
            <a href="{{ route('passenger.history') }}" class="text-slate-500 text-[11px] font-black hover:text-blue-400 transition flex items-center gap-2 no-underline uppercase tracking-widest">
                <i class="fas fa-history text-sm"></i> My History
            </a>
        </div>
    </div>

</body>
</html>