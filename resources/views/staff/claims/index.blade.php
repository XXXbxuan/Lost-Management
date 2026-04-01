<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-2xl">📜</span> Claims History (Audit Log)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100">
                <div class="p-10 text-gray-900">
                    
                    <div class="mb-10 flex justify-between items-end border-b border-gray-50 pb-8">
                        <div>
                            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Database Ledger</p>
                            <h3 class="text-3xl font-black text-slate-900">Official Claims History</h3>
                        </div>
                        <div class="flex gap-3">
                            <span class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">✓ Audit Synced</span>
                            <span class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-indigo-100 shadow-sm">✓ Total Evidence: {{ $claims->total() }}</span>
                        </div>
                    </div>

                    <div class="overflow-hidden border border-slate-100 rounded-[2.5rem] shadow-sm bg-white">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-900">
                                <tr>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Timestamp</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Asset Details</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Claimant Info</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Witnessed By</th>
                                    <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Document</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-50">
                                @forelse($claims as $claim)
                                <tr data-claim-id="{{ $claim->id }}" class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="font-black text-slate-800 text-sm mb-0.5">
                                            {{ $claim->claimedAt ? $claim->claimedAt->format('Y-m-d') : $claim->created_at->format('Y-m-d') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase italic">
                                            @ {{ $claim->claimedAt ? $claim->claimedAt->format('h:i A') : $claim->created_at->format('h:i A') }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-indigo-600 uppercase italic group-hover:scale-105 transition-transform origin-left cursor-default">
                                            {{ $claim->foundItem->item_name ?? 'Item N/A' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $claim->foundItem->category ?? 'General' }}</div>
                                        <div class="mt-2">
                                            <span class="text-[9px] bg-slate-900 text-white px-2 py-0.5 rounded-lg font-mono font-bold tracking-tighter">REF: #{{ $claim->foundId }}</span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-slate-900">{{ $claim->claimerName }}</div>
                                        <div class="flex items-center text-[10px] text-rose-500 font-black mt-1 uppercase">
                                            <span class="bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100">ID: {{ $claim->claimerIcPassport }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold mt-1 tracking-tight italic">📞 {{ $claim->claimerPhone }}</div>
                                    </td>
                                    
                                    <td class="px-8 py-6 whitespace-nowrap text-xs font-bold text-slate-500">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-900 font-black text-[10px]">
                                                {{ strtoupper(substr($claim->handler->name ?? 'ST', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-slate-900 font-black">{{ $claim->handler->name ?? 'Staff' }}</div>
                                                <div class="text-[9px] uppercase tracking-tighter text-slate-400">Security Team</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6 whitespace-nowrap text-right">
                                        <a href="{{ route('staff.claims.receipt', $claim->match_id ?? $claim->id) }}" 
                                           data-receipt-link="true"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            View Receipt
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-40 text-center">
                                        <p class="text-slate-400 font-black uppercase text-[11px] tracking-[0.4em]">No archive records found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-12">
                        {{ $claims->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if(!empty($openClaimId))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const targetRow = document.querySelector('[data-claim-id="{{ $openClaimId }}"]');

            if (targetRow) {
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

                setTimeout(() => {
                    const receiptBtn = targetRow.querySelector('[data-receipt-link="true"]');
                    if (receiptBtn) {
                        window.open(receiptBtn.href, '_blank');
                    }
                }, 500);
            }
        });
    </script>
    @endif
</x-app-layout>