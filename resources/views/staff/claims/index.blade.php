<x-app-layout>
    <x-slot name="header">
        <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
            <span class="text-2xl">📜</span>
            Claims History (Audit Log)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[2.5rem] border border-gray-100 bg-white shadow-2xl">
                <div class="p-10 text-gray-900">
                    <div class="mb-10 flex items-end justify-between border-b border-gray-50 pb-8">
                        <div>
                            <p class="mb-1 text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">
                                Database Ledger
                            </p>
                            <h3 class="text-3xl font-black text-slate-900">
                                Official Claims History
                            </h3>
                        </div>

                        <div class="flex gap-3">
                            <span class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-emerald-600 shadow-sm">
                                ✓ Audit Synced
                            </span>
                            <span class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-indigo-600 shadow-sm">
                                ✓ Total Evidence: {{ $claims->total() }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-900">
                                <tr>
                                    <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        Timestamp
                                    </th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        Asset Details
                                    </th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        Claimant Info
                                    </th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        Witnessed By
                                    </th>
                                    <th class="px-8 py-6 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        Document
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-50 bg-white">
                                @forelse ($claims as $claim)
                                    <tr data-claim-id="{{ $claim->id }}" class="group transition-all hover:bg-slate-50/80">
                                        <td class="whitespace-nowrap px-8 py-6">
                                            <div class="mb-0.5 text-sm font-black text-slate-800">
                                                {{ $claim->claimedAt ? $claim->claimedAt->format('Y-m-d') : $claim->created_at->format('Y-m-d') }}
                                            </div>
                                            <div class="text-[10px] font-bold uppercase italic text-slate-400">
                                                @ {{ $claim->claimedAt ? $claim->claimedAt->format('h:i A') : $claim->created_at->format('h:i A') }}
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <div class="origin-left cursor-default text-sm font-black uppercase italic text-indigo-600 transition-transform group-hover:scale-105">
                                                {{ $claim->foundItem->item_name ?? 'Item N/A' }}
                                            </div>

                                            <div class="mt-0.5 text-[10px] font-bold text-slate-400">
                                                {{ $claim->foundItem->category ?? 'General' }}
                                            </div>

                                            <div class="mt-2 flex flex-col gap-1">
                                                <span class="w-fit rounded-lg bg-slate-900 px-2 py-0.5 font-mono text-[9px] font-bold tracking-tighter text-white">
                                                    {{ $claim->receipt_no ?? ('REF-' . $claim->id) }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <div class="text-sm font-black text-slate-900">
                                                {{ $claim->claimerName }}
                                            </div>

                                            <div class="mt-1 flex items-center text-[10px] font-black uppercase text-rose-500">
                                                <span class="rounded-lg border border-rose-100 bg-rose-50 px-2 py-0.5">
                                                    ID: {{ $claim->claimerIcPassport }}
                                                </span>
                                            </div>

                                            <div class="mt-1 text-[10px] font-bold italic tracking-tight text-slate-400">
                                                📞 {{ $claim->claimerPhone }}
                                            </div>

                                            @if ($claim->handover_notes)
                                                <div class="mt-2 text-[10px] italic text-slate-500">
                                                    Notes: {{ $claim->handover_notes }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap px-8 py-6 text-xs font-bold text-slate-500">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-[10px] font-black text-slate-900">
                                                    {{ strtoupper(substr($claim->processed_by_name ?? $claim->handler->name ?? 'ST', 0, 2)) }}
                                                </div>

                                                <div>
                                                    <div class="font-black text-slate-900">
                                                        {{ $claim->processed_by_name ?? $claim->handler->name ?? 'Staff' }}
                                                    </div>
                                                    <div class="text-[9px] uppercase tracking-tighter text-slate-400">
                                                        Security Team
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="whitespace-nowrap px-8 py-6 text-right">
                                            <a
                                                href="{{ route('staff.claims.receipt', $claim->match_id) }}"
                                                data-receipt-link="true"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:bg-indigo-600 active:scale-95"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                View Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-40 text-center">
                                            <p class="text-[11px] font-black uppercase tracking-[0.4em] text-slate-400">
                                                No archive records found
                                            </p>
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

    @if (!empty($openClaimId))
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