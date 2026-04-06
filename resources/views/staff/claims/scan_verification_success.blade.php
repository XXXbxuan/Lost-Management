<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-900 p-4">
        <div class="w-full max-w-sm rounded-[2rem] bg-white p-8 text-center shadow-2xl">
            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 animate-bounce">
                <i class="fas fa-check text-5xl text-emerald-500"></i>
            </div>

            <h2 class="mb-2 text-2xl font-black uppercase tracking-tight text-slate-800">
                Scan Success!
            </h2>

            <p class="mb-6 font-medium text-slate-500">
                Signal sent to desktop.
            </p>

            <div class="mb-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-sm font-bold text-emerald-800">
                    <i class="fas fa-desktop mr-2"></i>
                    Please proceed with photo verification on your computer monitor.
                </p>
            </div>

            <button
                onclick="window.close()"
                class="w-full rounded-xl bg-slate-100 px-4 py-3 font-bold text-slate-600 transition hover:bg-slate-200"
            >
                Close Window
            </button>
        </div>
    </div>
</x-guest-layout>