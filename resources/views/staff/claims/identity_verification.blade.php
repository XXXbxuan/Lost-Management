<x-app-layout>
    <div class="min-h-screen bg-slate-50 py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <a
                    href="{{ route('staff.claims.handover', $match->id) }}"
                    class="flex items-center gap-2 text-sm font-black text-slate-400 transition hover:text-slate-600"
                >
                    <i class="fas fa-arrow-left"></i>
                    BACK TO STAGE 1 (RE-VERIFY)
                </a>

                <span class="rounded-full bg-slate-200 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-600">
                    Security Protocol v2.0
                </span>
            </div>

            <div class="overflow-hidden rounded-[3rem] border border-slate-100 bg-white shadow-2xl">
                <div class="relative bg-slate-900 p-8 text-white">
                    <div class="relative z-10">
                        <div class="mb-2 flex items-center gap-3">
                            <div class="h-3 w-3 animate-pulse rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)]"></div>
                            <span class="text-xs font-black uppercase tracking-[0.3em] opacity-70">
                                Security Protocol Stage 2
                            </span>
                        </div>

                        <h2 class="text-3xl font-black tracking-tight">
                            Final Handover &amp; Photo Evidence
                        </h2>

                        <p class="mt-2 text-xs font-bold uppercase tracking-widest italic text-slate-400">
                            Capturing legal identity and physical handover proof.
                        </p>
                    </div>
                </div>

                <div class="p-10">
                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                            <p class="text-sm font-black uppercase tracking-widest text-rose-700">
                                Please check the form details
                            </p>
                            <ul class="mt-2 list-inside list-disc text-sm text-rose-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('staff.claims.complete', $match->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-8"
                    >
                        @csrf

                        <div class="grid grid-cols-1 gap-8">
                            <div>
                                <label class="mb-3 ml-2 block text-xs font-black uppercase tracking-[0.2em] text-slate-500">
                                    Full Name (As per IC / Passport) <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="claimerName"
                                    value="{{ old('claimerName', $lostItem->passenger_name ?? '') }}"
                                    required
                                    class="w-full rounded-2xl border-2 border-slate-100 bg-white px-6 py-4 text-lg font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0"
                                    placeholder="Enter FULL LEGAL NAME"
                                >
                            </div>

                            <div>
                                <label class="mb-3 ml-2 block text-xs font-black uppercase tracking-[0.2em] text-slate-500">
                                    IC / Passport Number <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="claimerIcPassport"
                                    value="{{ old('claimerIcPassport') }}"
                                    required
                                    class="w-full rounded-2xl border-2 border-slate-100 bg-white px-6 py-4 text-lg font-mono font-bold shadow-sm transition-all focus:border-slate-900 focus:ring-0"
                                    placeholder="e.g. 010203-14-5566"
                                >
                            </div>

                            <div class="rounded-[2rem] border-2 border-slate-100 bg-slate-50 p-8">
                                <label class="mb-4 block text-center text-xs font-black uppercase tracking-[0.2em] text-slate-500">
                                    <i class="fas fa-camera mr-2"></i>
                                    Take / Upload Handover Photo <span class="text-rose-500">*</span>
                                </label>

                                <div class="flex flex-col items-center">
                                    <div
                                        id="image-preview"
                                        class="mb-6 flex h-56 w-full items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-white shadow-inner transition-all"
                                    >
                                        <div id="preview-text" class="text-center text-slate-400">
                                            <i class="fas fa-image mb-2 text-4xl"></i>
                                            <p class="mt-2 text-[10px] font-black uppercase tracking-widest">
                                                No photo selected
                                            </p>
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        name="handover_photo"
                                        id="handover_photo"
                                        accept="image/*"
                                        required
                                        class="hidden"
                                    >

                                    <label
                                        for="handover_photo"
                                        class="flex cursor-pointer items-center gap-2 rounded-xl bg-slate-900 px-8 py-4 text-sm font-black uppercase text-white shadow-lg transition-transform hover:bg-black active:scale-95"
                                    >
                                        <i class="fas fa-upload"></i>
                                        Select / Capture Photo
                                    </label>
                                </div>

                                <p class="mt-4 text-center text-[10px] font-bold text-slate-400">
                                    Take a photo of the passenger holding the item as final legal proof.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="handover_notes" class="mb-2 block text-sm font-bold text-slate-700">
                                Handover Notes
                            </label>
                            <textarea
                                name="handover_notes"
                                id="handover_notes"
                                rows="3"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-400"
                                placeholder="Optional notes"
                            >{{ old('handover_notes') }}</textarea>
                        </div>

                        <div class="border-t border-slate-100 pt-6">
                            <button
                                type="submit"
                                onclick="return confirm('FINAL WARNING: Ensure all legal details and the photo are correct. Close this case permanently?')"
                                class="flex w-full items-center justify-center gap-4 rounded-[2rem] bg-emerald-600 py-6 text-xl font-black text-white shadow-xl transition-all hover:scale-[1.02] hover:bg-emerald-700 active:scale-[0.98]"
                            >
                                <span class="text-2xl">🤝</span>
                                COMPLETE HANDOVER &amp; CLOSE CASE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('handover_photo').onchange = function () {
            const [file] = this.files;

            if (file) {
                const preview = document.getElementById('image-preview');
                preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="h-full w-full rounded-xl object-cover">`;
                preview.classList.remove('border-dashed', 'border-slate-300', 'p-4');
                preview.classList.add('border-solid', 'border-emerald-400', 'shadow-md');
            }
        };
    </script>
</x-app-layout>