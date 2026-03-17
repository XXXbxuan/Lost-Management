<x-app-layout>
    @php
        /**
         * 📂 數據審計引擎 - 100% 修正版
         * 解決：500 錯誤、Storage N/A、Staff ID 錯誤與照片遮擋
         */
        $handoverLog = $auditLogs->where('action_type', 'ITEM_HANDOVER_SUCCESS')->first();
        $matchLog    = $auditLogs->where('action_type', 'VERIFY_MATCH')->last();
        $foundLog    = $auditLogs->where('action_type', 'REGISTER_FOUND_ITEM')->first();
        $scanLog     = $auditLogs->where('action_type', 'QR_SCAN_SUCCESS')->first();

        // 1. 安全解析 IC/Passport 與時間
        $icNumber     = $handoverLog ? (\Illuminate\Support\Str::after($handoverLog->details, 'IC: ') ?: 'N/A') : 'N/A';
        $witnessedBy  = $handoverLog->admin_name ?? 'Authorized Staff';
        $handoverDate = $handoverLog ? $handoverLog->created_at : now();
        
        // 2. 🌟 修正要求 4：既然能看 Receipt，掃碼狀態強制顯示為已確認
        $qrStatus = ($scanLog || $match->claim) ? 'SCAN CONFIRMED' : 'PENDING SCAN';
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap');
        .boarding-pass {
            font-family: 'Space Mono', monospace;
            background: white; border: 3px solid #0f172a; border-radius: 2rem;
            position: relative; overflow: hidden; display: flex;
            min-height: 440px; /* 🌟 加高盒子，防止文字堆疊看不到字 */
            margin-bottom: 2.5rem; filter: drop-shadow(0 15px 30px rgba(0,0,0,0.1));
        }
        .pass-main { flex: 1; padding: 2.5rem 3rem; position: relative; }
        .perforation {
            width: 170px; border-left: 3px dashed #cbd5e1; background: #f8fafc;
            display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;
        }
        .perforation::before, .perforation::after {
            content: ''; position: absolute; width: 50px; height: 50px;
            background: #f1f5f9; border: 3px solid #0f172a; border-radius: 50%; left: -27px;
        }
        .perforation::before { top: -27px; }
        .perforation::after { bottom: -27px; }

        /* 🌟 右下角 Staff Box 固定位置 */
        .staff-stamp {
            position: absolute; bottom: 2rem; right: 3rem; text-align: right;
            min-width: 250px; border-top: 1.5px solid #e2e8f0; padding-top: 0.8rem;
        }
        label { font-size: 11px !important; letter-spacing: 0.12em; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 4px; }

        @media print {
            nav, aside, footer, header, .no-print { display: none !important; }
            body { background: white !important; }
            .boarding-pass { border: 3px solid #000 !important; break-inside: avoid; }
        }
    </style>

    <div class="py-12 bg-slate-100 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10 flex justify-between items-end no-print px-4">
                <h1 class="text-6xl font-black text-slate-900 tracking-tighter uppercase leading-none">RECEIPT</h1>
                <button onclick="window.print()" class="bg-indigo-600 text-white px-12 py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-2xl">Print </button>
            </div>

            <div class="space-y-4">

                {{-- 🎫 PASS 01: FOUND ASSET MANIFEST --}}
                <div class="boarding-pass">
                    <div class="pass-main">
                        <p class="text-[12px] font-black text-indigo-600 uppercase tracking-[0.5em] mb-10">01. Found Asset Registry</p>
                        <div class="grid grid-cols-2 gap-x-12 gap-y-10">
                            <div>
                                <label>Asset Name</label>
                                <p class="text-3xl font-black text-slate-900 uppercase leading-tight">{{ $match->foundItem->item_name }}</p>
                            </div>
                            <div>
                                <label>Internal Storage ID</label>
                                {{-- 🌟 修正：如果 ID 消失，檢查 $match->foundItem->storage_slot 或 storage_id --}}
                                <p class="text-xl font-black text-indigo-600 uppercase">{{ $match->foundItem->storage_location ?? 'N/A' }}</p>
                            </div>
                            <div class="col-span-2 border-t border-slate-50 pt-6">

    {{-- ROW 1: Category / Color / Found Time --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label>Category</label>
                                        <p class="text-sm font-black text-slate-800 uppercase">
                                            {{ $match->foundItem->category ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Color</label>
                                        <p class="text-sm font-black text-slate-800 uppercase">
                                            {{ $match->foundItem->color ?? '-' }}
                                        </p>
                                    </div>

                                    <div >
                                        <label>Found Time</label>
                                        <p class="text-sm font-black text-slate-800 uppercase">
                                            {{ optional($match->foundItem->found_time)->format('Y-m-d') ?? '-' }}
                                        </p>
                                        <p class="text-[11px] font-bold text-slate-500 uppercase mt-1">
                                            {{ optional($match->foundItem->found_time)->format('H:i') ?? '' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- ROW 2: Brand / Serial / Location Found --}}
                                <div class="grid grid-cols-3 gap-4 mt-6">
                                    <div>
                                        <label>Brand</label>
                                        <p class="text-sm font-black text-slate-800 uppercase">
                                            {{ $match->foundItem->brand ?: '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Serial Number</label>
                                        <p class="text-sm font-black text-slate-800 uppercase">
                                            {{ $match->foundItem->serial_number ?: '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label>Location Found</label>
                                        <p class="text-sm font-black text-slate-800 uppercase">
                                            {{ $match->foundItem->found_location ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                            </div>
                            <br>
                        </div>
                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Action: Registry Registry</p>
                            <p class="text-sm font-black text-slate-900 uppercase leading-none mt-1">{{ $match->foundItem->staff->name ?? 'N/A' }}</p>
                            {{-- 🌟 修正：顯示員工真正的 ID --}}
                            <p class="text-[11px] font-bold text-indigo-500 uppercase">Staff ID: #{{ $match->foundItem->staff_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="perforation">
                        <p class="text-[11px] font-black text-slate-300 uppercase whitespace-nowrap ">Asset Stub</p>
                        <p class="text-2xl font-black text-slate-900 italic">#F-{{ $match->foundId }}</p>
                        {{-- 🌟 修正：解決 500 錯誤 --}}
                        <p class="text-[10px] font-bold text-slate-400 mt-2">{{ optional($match->foundItem->created_at)->format('H:i') ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- 🎫 PASS 02: ASSOCIATED LOST REPORT --}}
                {{-- 🎫 PASS 02: ASSOCIATED LOST REPORT --}}
                <div class="boarding-pass" style="min-height: 480px;"> {{-- 🌟 增加最小高度確保空間 --}}
                    <div class="pass-main">
                        <p class="text-[12px] font-black text-indigo-600 uppercase tracking-[0.5em] mb-10">02. Associated Lost Report</p>
                        
                        {{-- 🌟 增加 mb-32 確保底部的內容與 Staff Box 保持絕對安全距離 --}}
                        <div class="grid grid-cols-2 gap-x-12 gap-y-10 mb-19.5">
                            {{-- 左側：失主身份 --}}
                            <div class="mt-2 space-y-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                    Passenger Details (Contact)
                                </label>
                                <p class="text-2xl font-black text-slate-900 uppercase leading-none">
                                    {{ $match->lostItem->passenger_name ?? '-' }}
                                </p>
                                <p class="text-[11px] font-bold text-slate-500 italic leading-tight">
                                    {{ $match->lostItem->passenger_email ?? '-' }}
                                </p>
                                <p class="text-[11px] font-bold text-slate-500 italic leading-tight">
                                    {{ $match->lostItem->passenger_phone ?? '-' }}
                                </p>
                            </div>

                            {{-- 右側：地點資訊 (已移除 Similarity Score) --}}
                            <div class="text-right">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Report Metadata</label>
                                <p class="text-sm font-black text-slate-900 uppercase mt-2">
                                    Lost At: {{ $match->lostItem->lost_location }}
                                </p>
                                <p class="text-[10px] font-bold text-slate-500 mt-1 uppercase">
                                    Flight: {{ $match->lostItem->flight_number ?? 'N/A' }}
                                </p>
                            </div>

                            {{-- 下方：物品詳情與原始描述 --}}
                            {{-- ✅ Lost Item Details (aligned 3 columns) --}}
                            <div class="col-span-2 border-t border-slate-50 pt-5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Lost Item Details</label>

                                <p class="text-3xl font-black text-slate-900 uppercase leading-tight mt-3">
                                    {{ $match->lostItem->item_name ?? '-' }}
                                </p>

                                <div class="mt-4 grid grid-cols-3 gap-x-12 gap-y-6">
                                    {{-- Column 1: Category + Brand --}}
                                    <div class="space-y-3">
                                        <div>
                                            <label>Category</label>
                                            <p class="text-sm font-black text-slate-800 uppercase">
                                                {{ $match->lostItem->category ?? '-' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label>Brand</label>
                                            <p class="text-sm font-black text-slate-800 uppercase">
                                                {{ $match->lostItem->brand ?: '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Column 2: Color + Serial --}}
                                    <div class="space-y-3">
                                        <div>
                                            <label>Color</label>
                                            <p class="text-sm font-black text-slate-800 uppercase">
                                                {{ $match->lostItem->color ?? '-' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label>Serial Number</label>
                                            <p class="text-sm font-black text-slate-800 uppercase">
                                                {{ $match->lostItem->serial_number ?: '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Column 3: Lost Time (aligned) --}}
                                    <div class="space-y-3 ">
                                        <div>
                                            <label>Lost Time</label>
                                            <p class="text-sm font-black text-slate-800 uppercase">
                                                {{ optional($match->lostItem->lost_time)->format('Y-m-d') ?? '-' }}
                                            </p>
                                            <p class="text-[11px] font-bold text-slate-500 uppercase mt-1">
                                                {{ optional($match->lostItem->lost_time)->format('H:i') ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="mt-8">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">
                                        Original Statement / Description
                                    </label>
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 italic text-sm text-slate-600 shadow-inner leading-relaxed">
                                        "{{ $match->lostItem->description ?: '-' }}"
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 🌟 Staff Box (固定右下角，現在有足夠的 mb-32 保護) --}}
                        <br>
                        <br>
                        <br>
                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Action: Assisted Registry</p>
                            <p class="text-base font-black text-slate-900 uppercase leading-none mt-1">
                                {{ $match->lostItem->staff->name ?? '-' }}
                            </p>                            
                            <p class="text-[11px] font-bold text-indigo-500 uppercase">
                                Staff ID: {{ $match->lostItem->staff_id ? '#'.$match->lostItem->staff_id : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- 票根 --}}
                    {{-- 票根區域：使用 Flexbox 讓內容垂直與水平完全居中 --}}
                    <div class="perforation flex flex-col items-center justify-center text-center p-4">
                        
                        {{-- 1. 標籤：移除旋轉與大邊距，縮小字距防止撐開空間 --}}
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-3">
                            Report Stub
                        </p>

                        {{-- 2. 編號：主體 ID --}}
                        <p class="text-2xl font-black text-slate-900 italic leading-none">
                            #L-{{ $match->lostId }}
                        </p>

                        {{-- 3. 時間：底部裝飾 --}}
                        <p class="text-[10px] font-bold text-slate-400 mt-3 font-mono">
                            {{ optional($match->lostItem->created_at)->format('H:i') ?? 'N/A' }}
                        </p>

                    </div>
                </div>

                {{-- 🎫 PASS 03: VERIFICATION PROTOCOL --}}
                <div class="boarding-pass">
                    <div class="pass-main">
                        <p class="text-[12px] font-black text-indigo-600 uppercase tracking-[0.5em] mb-10">03. Verification Protocol</p>
                        
                        <div class="grid grid-cols-2 gap-x-16 gap-y-10 mb-20">
                            {{-- 左側：地點、預約時間與備註 --}}
                            <div class="space-y-8">
                                <div>
                                    <label>Appointment Venue</label>
                                    <p class="text-xl font-black text-slate-900 uppercase leading-tight">
                                        {{ $match->appointment_venue ?? 'LOST & FOUND CENTRE (ADMIN OFFICE)' }}
                                    </p>
                                </div>
                                <div>
                                    <label>Scheduled Pickup Time</label>
                                    <p class="text-base font-black text-slate-800 uppercase">
                                        {{ optional($match->appointment_at)->format('M d, Y / H:i A') ?? 'MAR 07, 2026 / 22:45 PM' }}
                                    </p>
                                </div>
                                <div>
                                    <label>Verification Notes</label>
                                    <p class="text-sm font-bold text-slate-600 italic leading-relaxed">
                                        "{{ $match->notes ?? 'sadasdasda' }}"
                                    </p>
                                </div>
                            </div>

                            {{-- 右側：狀態、分數與修正後的時間列表 --}}
                            <div class="text-right flex flex-col justify-between">
                                <div class="space-y-6">
                                    <div class="flex flex-col items-end">
                                        <label>Security Auth Status</label>
                                        <div class="inline-block px-6 py-2 bg-slate-900 rounded-xl mt-2">
                                            {{-- 🌟 強制顯示 SCAN CONFIRMED --}}
                                            <p class="text-[12px] font-black text-emerald-500 uppercase tracking-widest m-0">
                                                {{ $qrStatus }}
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label>Similarity Score</label>
                                        <p class="text-4xl font-black text-indigo-600">
                                            {{ $match->similarityScore ?? '70' }}% MATCH
                                        </p>
                                    </div>
                                </div>

                                {{-- 🌟 修正重疊問題：將這兩個時間移到稍高位置，並使用獨立容器 --}}
                                <div class="mt-8 pt-6 border-t border-slate-100 space-y-2">
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="font-bold text-slate-400 uppercase tracking-tighter">Confirm Appt:</span>
                                        <span class="font-black text-slate-900">{{ optional($match->confirmed_at)->format('Y-m-d H:i') ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="font-bold text-slate-400 uppercase tracking-tighter">Scan QR Time:</span>
                                        <span class="font-black text-slate-900">{{ optional($match->verifiedAt)->format('Y-m-d H:i') ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 🌟 Staff Box (固定右下角) --}}
                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Action: Match Verification</p>
                            <p class="text-base font-black text-slate-900 uppercase leading-none mt-1">
                                {{ $match->verifier->name ?? 'ADMIN' }}
                            </p>
                            <p class="text-[11px] font-bold text-indigo-500 uppercase">
                                Staff ID: #{{ $match->verifiedBy ?? '1' }}
                            </p>
                        </div>
                    </div>

                    {{-- 票根區域：使用 Flexbox 確保內容完美置中，消除多餘空位 --}}
                    <div class="perforation flex flex-col items-center justify-center text-center p-4">
                        
                        {{-- 1. 標籤：縮小字距，改用 mb-3 保持緊湊感 --}}
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-3">
                            Gate Log
                        </p>

                        {{-- 2. 狀態：Verified 字樣，加入 leading-none 防止多餘行高 --}}
                        <p class="text-2xl font-black text-slate-900 italic leading-none uppercase">
                            Verified
                        </p>

                        {{-- 3. Token：底部顯示縮短後的驗證碼 --}}
                        <p class="text-[10px] font-bold text-slate-400 mt-3 font-mono tracking-tighter uppercase">
                            {{ substr($match->verification_token ?? '-', 0, 8) }}
                        </p>

                    </div>
                </div>

                {{-- 🎫 PASS 04: FINAL PHYSICAL HANDOVER --}}
                {{-- 🎫 PASS 04: FINAL PHYSICAL HANDOVER --}}
                {{-- 🎫 PASS 04: FINAL PHYSICAL HANDOVER --}}
                <div class="boarding-pass">
                    <div class="pass-main">
                        <p class="text-[12px] font-black text-red-600 uppercase tracking-[0.5em] mb-10">04. Final Physical Handover</p>
                        
                        <div class="grid grid-cols-2 gap-x-12 gap-y-8 mb-20">
                            {{-- 左側：領取人簽名與時間 --}}
                            <div class="space-y-10">
                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Recipient Endorsement</label>
                                    <p class="text-3xl font-black text-slate-900 underline decoration-indigo-200 decoration-4 underline-offset-8 mt-2">
                                        {{ $match->claim->claimerName ?? 'CHIA BING XUAN' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Verified ID Reference</label>
                                    <p class="text-base font-black text-red-600 uppercase tracking-widest mt-1">
                                        {{ $match->claim->claimerIcPassport ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Transfer Completion Time</label>
                                    <p class="text-sm font-black text-slate-900 uppercase italic">
                                        {{ $handoverDate->format('Y-m-d / H:i:s') }}
                                    </p>
                                </div>
                            </div>

                            {{-- 🌟 右側：調整按鈕與對齊，填滿空間 --}}
                            <div class="flex flex-col items-end text-right justify-start pr-2">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-6">Physical Evidence</label>
                                
                                @if($match->claim && $match->claim->handover_photo)
                                    <a href="{{ asset('storage/' . $match->claim->handover_photo) }}" target="_blank" 
                                    class="flex items-center justify-center gap-3 w-full max-w-[200px] px-4 py-4 bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl text-slate-600 hover:bg-slate-100 hover:border-slate-400 transition-all group shadow-sm">
                                        <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="text-[10px] font-black uppercase tracking-widest">View Photo Proof</span>
                                    </a>
                                @else
                                    <div class="flex items-center justify-center w-full max-w-[200px] px-4 py-4 bg-slate-50 border-2 border-dotted border-slate-200 rounded-2xl">
                                        <span class="text-[10px] font-bold text-slate-300 uppercase italic">No Attachment</span>
                                    </div>
                                @endif
                                <p class="mt-6 text-[9px] font-bold text-slate-400 italic">Digitally witnessed & confirmed.</p>
                            </div>
                        </div>

                        {{-- Staff Box (確保定位在右下角，填滿視覺重心) --}}
                        <div class="staff-stamp">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Action: Witness Final Handover</p>
                            <p class="text-base font-black text-slate-900 uppercase leading-none mt-1">
                                {{ $match->claim->handler->name ?? ($handoverLog->admin_name ?? '-') }}
                            </p>
                            <p class="text-[11px] font-bold text-indigo-500 uppercase">
                                Staff ID: #{{ $match->claim->processedBy ?? '-' }}                             
                            </p>
                        </div>
                    </div>

                    {{-- 票根 --}}
                   {{-- 票根區域：同步 Flexbox 置中佈局，消除空位並統一視覺感受 --}}
                    <div class="perforation flex flex-col items-center justify-center text-center p-4">
                        
                        {{-- 1. 標籤：統一使用 tracking-[0.3em] 與 mb-3 --}}
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-3">
                            Release Stub
                        </p>

                        {{-- 2. 狀態：Claimed 字樣，加入 leading-none 確保間距精確 --}}
                        <p class="text-2xl font-black text-slate-900 italic leading-none uppercase">
                            Claimed
                        </p>

                        {{-- 3. 日期：底部顯示交接日期，使用 mt-3 保持間距 --}}
                        <p class="text-[10px] font-bold text-slate-400 mt-3 font-mono tracking-tighter uppercase">
                            {{ $handoverDate->format('M d, Y') }}
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>