<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 返回 Stage 1 按鈕 --}}
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('staff.claims.handover', $match->id) }}" class="text-sm font-black text-slate-400 hover:text-slate-600 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> BACK TO STAGE 1 (RE-VERIFY)
                </a>
                <span class="text-[10px] font-black bg-slate-200 px-3 py-1 rounded-full uppercase tracking-widest text-slate-600">Security Protocol v2.0</span>
            </div>

            <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100">
                
                {{-- 🔒 頂部安全標題 --}}
                <div class="bg-slate-900 p-8 text-white relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></div>
                            <span class="text-xs font-black tracking-[0.3em] uppercase opacity-70">Security Protocol Stage 2</span>
                        </div>
                        <h2 class="text-3xl font-black tracking-tight">Final Handover & Photo Evidence</h2>
                        <p class="text-slate-400 text-xs mt-2 font-bold uppercase tracking-widest italic">Capturing legal identity and physical handover proof.</p>
                    </div>
                </div>

                <div class="p-10">
                    {{-- 💡 表單必須加上 enctype="multipart/form-data" 才能傳照片 --}}
                    <form action="{{ route('staff.claims.complete', $match->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-8">
                            
                            {{-- 1. 真實姓名 --}}
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-2">
                                    Full Name (As per IC / Passport) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="claimerName" required 
                                       class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-4 font-bold text-lg focus:border-slate-900 focus:ring-0 transition-all shadow-sm"
                                       placeholder="Enter FULL LEGAL NAME">
                            </div>

                            {{-- 2. IC / Passport 號碼 --}}
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-2">
                                    IC / Passport Number <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="claimerIcPassport" required 
                                       class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-4 font-mono font-bold text-lg focus:border-slate-900 focus:ring-0 transition-all shadow-sm"
                                       placeholder="e.g. 010203-14-5566">
                            </div>

                            {{-- 3. 📸 現場領取照片 (Handover Photo) - 全新明亮版 UI --}}
                            <div class="bg-slate-50 rounded-[2rem] p-8 border-2 border-slate-100">
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-4 text-center">
                                    <i class="fas fa-camera mr-2"></i> Take / Upload Handover Photo <span class="text-rose-500">*</span>
                                </label>
                                
                                <div class="flex flex-col items-center">
                                    {{-- 照片預覽區 --}}
                                    <div id="image-preview" class="w-full h-56 bg-white rounded-2xl border-2 border-dashed border-slate-300 flex items-center justify-center mb-6 overflow-hidden shadow-inner transition-all">
                                        <div class="text-center text-slate-400" id="preview-text">
                                            <i class="fas fa-image text-4xl mb-2"></i>
                                            <p class="text-[10px] font-black uppercase tracking-widest mt-2">No photo selected</p>
                                        </div>
                                    </div>
                                    
                                    {{-- 隱藏的 input --}}
                                    <input type="file" name="handover_photo" id="handover_photo" accept="image/*" required class="hidden">
                                    
                                    {{-- 觸發按鈕 --}}
                                    <label for="handover_photo" class="cursor-pointer bg-slate-900 text-white px-8 py-4 rounded-xl font-black text-sm hover:bg-black transition-transform active:scale-95 uppercase shadow-lg flex items-center gap-2">
                                        <i class="fas fa-upload"></i> Select / Capture Photo
                                    </label>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-4 text-center font-bold">Take a photo of the passenger holding the item as final legal proof.</p>
                            </div>
                            

                        </div>
                        <div class="mt-6">
    <label for="handover_notes" class="block text-sm font-bold text-slate-700 mb-2">
        Handover Notes
    </label>
    <textarea
        name="handover_notes"
        id="handover_notes"
        rows="3"
        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:ring-2 focus:ring-slate-400 focus:border-slate-400"
        placeholder="Optional notes"
    >{{ old('handover_notes') }}</textarea>
</div>

                        {{-- 結案按鈕 (附帶防呆確認) --}}
                        <div class="pt-6 border-t border-slate-100">
                            <button type="submit" 
                                    onclick="return confirm('FINAL WARNING: Ensure all legal details and the photo are correct. Close this case permanently?')"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-6 rounded-[2rem] shadow-xl text-xl flex items-center justify-center gap-4 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <span class="text-2xl">🤝</span>
                                COMPLETE HANDOVER & CLOSE CASE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 照片預覽腳本 --}}
    <script>
        document.getElementById('handover_photo').onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('image-preview');
                // 清空預設的圖標和文字，放入圖片
                preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover rounded-xl">`;
                preview.classList.remove('border-dashed', 'border-slate-300', 'p-4');
                preview.classList.add('border-solid', 'border-emerald-400', 'shadow-md');
            }
        }
    </script>
</x-app-layout>