<x-guest-layout>
    <div class="min-h-screen bg-slate-900 flex items-center justify-center p-4">
        <div class="max-w-sm w-full bg-white rounded-[2rem] p-8 text-center shadow-2xl">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-emerald-100 mb-6 animate-bounce">
                <i class="fas fa-check text-emerald-500 text-5xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800 mb-2 tracking-tight uppercase">Scan Success!</h2>
            <p class="text-slate-500 font-medium mb-6">Signal sent to desktop.</p>
            
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 mb-8">
                <p class="text-emerald-800 font-bold text-sm">
                    <i class="fas fa-desktop mr-2"></i> Please proceed with photo verification on your computer monitor.
                </p>
            </div>

            <button onclick="window.close()" class="w-full bg-slate-100 text-slate-600 font-bold py-3 px-4 rounded-xl hover:bg-slate-200 transition">
                Close Window
            </button>
        </div>
    </div>
</x-guest-layout>