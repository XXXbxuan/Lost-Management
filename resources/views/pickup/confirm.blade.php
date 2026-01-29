<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Pickup - TARUMT Airport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <div class="text-6xl mb-4">📅</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Confirm Your Appointment</h1>
        <p class="text-gray-600 mb-6">Please confirm that you will collect your item at the scheduled time.</p>

        <div class="bg-blue-50 p-4 rounded-lg mb-6 text-left">
            <p class="text-sm text-gray-500 uppercase font-bold">Time</p>
            <p class="text-lg font-bold text-gray-900 mb-2">{{ $match->appointment_at->format('d M Y, h:i A') }}</p>
            
            <p class="text-sm text-gray-500 uppercase font-bold">Location</p>
            <p class="text-gray-900">{{ $match->appointment_venue }}</p>
        </div>

        <form action="{{ route('pickup.process', $match->verification_token) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded transition transform active:scale-95">
                YES, I Will Be There
            </button>
        </form>
    </div>

</body>
</html>