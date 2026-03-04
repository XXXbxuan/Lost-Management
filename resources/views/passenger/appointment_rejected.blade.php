<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Appointment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">

        @if(isset($success) && $success)
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-4xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 mb-2">Proposal Sent!</h1>
                <p class="text-slate-500 mb-8 leading-relaxed">
                    Thank you. We have received your suggested times. Our staff will review and send you a new confirmation email shortly.
                </p>
                <div class="pt-6 border-t border-slate-100 text-xs text-slate-400 uppercase tracking-widest font-semibold">
                    Airport Lost & Found System
                </div>
            </div>

        @else
            <div class="bg-red-500 p-6 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times text-3xl"></i>
                </div>
                <h1 class="text-xl font-bold">Appointment Declined</h1>
                <p class="text-red-100 text-sm mt-1">Ref ID: #{{ $match->id }}</p>
            </div>

            <div class="p-6">
                <p class="text-slate-600 text-sm mb-6 text-center">
                    You have declined the proposed time. Please suggest alternative times below. (Office hours: 09:00 AM - 05:00 PM)
                </p>

                <p class="text-slate-600 text-sm mb-6 text-center">
                    You have declined the proposed time. Please suggest alternative times below. (Office hours: 09:00 AM - 05:00 PM)
                </p>

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-2"></i>
                            <div>
                                <h3 class="text-red-800 text-sm font-bold">Please check your inputs:</h3>
                                <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                <form action="{{ route('pickup.propose', $match->verification_token) }}" method="POST" class="space-y-4"></form>

                <form action="{{ route('pickup.propose', ['token' => $match->verification_token]) }}" method="POST" class="space-y-4">                    
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Option 1 (Preferred) <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="suggested_time_1" required
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Option 2 (Alternative)</label>
                        <input type="datetime-local" name="suggested_time_2"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Remarks</label>
                        <textarea name="suggested_remarks" rows="2" placeholder="e.g. I am only free in the afternoon."
                                  class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl shadow-md transition-all active:scale-95 mt-2">
                        SUBMIT NEW TIME
                    </button>
                </form>
            </div>
        @endif

    </div>
</body>
</html>