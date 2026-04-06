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
    <div class="max-w-md w-full overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-xl">
        @if (isset($success) && $success)
            <div class="p-8 text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-4xl"></i>
                </div>

                <h1 class="mb-2 text-2xl font-bold text-slate-800">Proposal Sent!</h1>

                <p class="mb-8 leading-relaxed text-slate-500">
                    Thank you. We have received your suggested times. Our staff will review and send you a new confirmation email shortly.
                </p>

                <div class="border-t border-slate-100 pt-6 text-xs font-semibold uppercase tracking-widest text-slate-400">
                    Airport Lost &amp; Found System
                </div>
            </div>
        @else
            <div class="bg-red-500 p-6 text-center text-white">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/20">
                    <i class="fas fa-times text-3xl"></i>
                </div>

                <h1 class="text-xl font-bold">Appointment Declined</h1>
                <p class="mt-1 text-sm text-red-100">Ref ID: #{{ $match->id }}</p>
            </div>

            <div class="p-6">
                <p class="mb-6 text-center text-sm text-slate-600">
                    You have declined the proposed time. Please suggest alternative times below. (Office hours: 09:00 AM - 05:00 PM)
                </p>

                @if ($errors->any())
                    <div class="mb-4 rounded border-l-4 border-red-500 bg-red-50 p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle mr-2 mt-0.5 text-red-500"></i>

                            <div>
                                <h3 class="text-sm font-bold text-red-800">Please check your inputs:</h3>
                                <ul class="mt-1 list-inside list-disc text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form
                    action="{{ route('pickup.propose', ['token' => $match->verification_token]) }}"
                    method="POST"
                    class="space-y-4"
                >
                    @csrf

                    <div>
                        <label for="suggested_time_1" class="mb-1 block text-sm font-bold text-slate-700">
                            Option 1 (Preferred) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="suggested_time_1"
                            type="datetime-local"
                            name="suggested_time_1"
                            value="{{ old('suggested_time_1') }}"
                            required
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 outline-none transition-all focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="suggested_time_2" class="mb-1 block text-sm font-bold text-slate-700">
                            Option 2 (Alternative)
                        </label>
                        <input
                            id="suggested_time_2"
                            type="datetime-local"
                            name="suggested_time_2"
                            value="{{ old('suggested_time_2') }}"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 outline-none transition-all focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="suggested_remarks" class="mb-1 block text-sm font-bold text-slate-700">
                            Remarks
                        </label>
                        <textarea
                            id="suggested_remarks"
                            name="suggested_remarks"
                            rows="2"
                            placeholder="e.g. I am only free in the afternoon."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 outline-none transition-all focus:ring-2 focus:ring-blue-500"
                        >{{ old('suggested_remarks') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 w-full rounded-xl bg-slate-800 py-3.5 font-bold text-white shadow-md transition-all active:scale-95 hover:bg-slate-900"
                    >
                        SUBMIT NEW TIME
                    </button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>