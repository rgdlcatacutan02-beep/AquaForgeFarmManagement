<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                        <span>Invoice #{{ $sale->sale_number }}</span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Recorded on {{ $sale->sale_date->format('F d, Y') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Print Invoice</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Printable Invoice Container -->
    <div class="max-w-3xl mx-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 sm:p-8 shadow-sm space-y-6 print:border-none print:p-0 print:bg-white print:text-black">
            <!-- Brand & Invoice Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b border-slate-800 print:border-slate-300">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-cyan-600 flex items-center justify-center text-white font-bold text-base">
                            <i data-lucide="waves" class="w-5 h-5"></i>
                        </div>
                        <span class="text-lg font-black tracking-tight text-white print:text-slate-900">AQUAFORGE</span>
                    </div>
                    <p class="text-xs text-slate-400 print:text-slate-600 mt-1">Aquatic Husbandry & Selective Breeding Operations</p>
                    <p class="text-[11px] text-slate-500 print:text-slate-500">Official Sales & Livestock Transfer Receipt</p>
                </div>

                <div class="sm:text-right space-y-1">
                    <div class="text-xs font-bold text-slate-400 print:text-slate-600 uppercase tracking-wider">Invoice #</div>
                    <div class="text-xl font-bold font-mono text-cyan-400 print:text-cyan-700">{{ $sale->sale_number }}</div>
                    <div class="text-xs text-slate-300 print:text-slate-700">Date: {{ $sale->sale_date->format('M d, Y') }}</div>
                    <div class="pt-1">
                        <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-bold border {{ $sale->payment_status === 'PAID' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50 print:bg-emerald-50 print:text-emerald-800 print:border-emerald-300' : 'bg-amber-950/60 text-amber-400 border-amber-800/50 print:bg-amber-50 print:text-amber-800 print:border-amber-300' }}">
                            {{ $sale->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer & Farm Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 print:text-slate-500 tracking-wider">Billed / Delivered To</span>
                    @if($sale->customer)
                        <div class="mt-1 font-bold text-white print:text-slate-900 text-sm">{{ $sale->customer->name }}</div>
                        <div class="text-slate-300 print:text-slate-700 font-mono mt-0.5">{{ $sale->customer->phone ?? 'No phone registered' }}</div>
                        <div class="text-slate-400 print:text-slate-600">{{ $sale->customer->email ?? '' }}</div>
                        <div class="text-slate-400 print:text-slate-600 mt-1 max-w-xs">{{ $sale->customer->address ?? '' }}</div>
                    @else
                        <div class="mt-1 font-semibold text-slate-300 print:text-slate-700 italic">Walk-in Cash Customer</div>
                    @endif
                </div>

                <div class="sm:text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 print:text-slate-500 tracking-wider">Fulfillment & Status</span>
                    <div class="mt-1 font-semibold text-white print:text-slate-900">
                        Order Status: <span class="text-cyan-400 print:text-cyan-700">{{ $sale->status }}</span>
                    </div>
                    @if($sale->notes)
                        <div class="text-slate-400 print:text-slate-600 mt-1 text-[11px]">Note: {{ $sale->notes }}</div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="border border-slate-800 print:border-slate-300 rounded-lg overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/60 print:bg-slate-100 text-slate-400 print:text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-800 print:border-slate-300">
                        <tr>
                            <th class="py-2.5 px-3">Item Description</th>
                            <th class="py-2.5 px-3 text-center">Ref Code</th>
                            <th class="py-2.5 px-3 text-center">Quantity</th>
                            <th class="py-2.5 px-3 text-right">Unit Price</th>
                            <th class="py-2.5 px-3 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 print:divide-slate-200">
                        @foreach ($sale->items as $item)
                            <tr>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-white print:text-slate-900">{{ $item->item_description }}</div>
                                    @if($item->livestock)
                                        <div class="text-[10px] text-cyan-400 print:text-cyan-700">
                                            {{ $item->livestock->species->name }} ({{ $item->livestock->quality_tier }}) &bull; Former Tank: {{ $item->livestock->tank->tank_code }}
                                        </div>
                                    @elseif($item->offspringBatch)
                                        <div class="text-[10px] text-cyan-400 print:text-cyan-700">
                                            {{ $item->offspringBatch->species->name }} fry &bull; Source Tank: {{ $item->offspringBatch->tank->tank_code }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center font-mono text-[11px] text-slate-400 print:text-slate-600">
                                    {{ $item->livestock?->livestock_code ?? $item->offspringBatch?->batch_code ?? '--' }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono text-white print:text-slate-900 font-bold">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-300 print:text-slate-700">
                                    ₱{{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-white print:text-slate-900">
                                    ₱{{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Totals Block -->
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pt-4 border-t border-slate-800 print:border-slate-300 text-xs">
                <div class="text-slate-500 print:text-slate-500 text-[11px] max-w-sm space-y-1">
                    <p class="font-semibold text-slate-400 print:text-slate-700">Livestock Health & Arrival Policy:</p>
                    <p>All livestock inspected for vitality prior to handover. Acclimate gradually via drip or float method for 30 minutes before release.</p>
                </div>

                <div class="w-64 space-y-1.5 self-end">
                    <div class="flex justify-between text-slate-400 print:text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-mono text-white print:text-slate-900 font-semibold">₱{{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    @if($sale->discount > 0)
                        <div class="flex justify-between text-rose-400 print:text-rose-600">
                            <span>Discount:</span>
                            <span class="font-mono font-semibold">&minus;₱{{ number_format($sale->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-slate-800 print:border-slate-300 text-white print:text-slate-900">
                        <span>Total:</span>
                        <span class="font-mono text-emerald-400 print:text-emerald-700">₱{{ number_format($sale->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- PHILIPPINE SCAN-TO-PAY BOX (GCash / Maya) -->
            @php
                $farmUser = auth()->user() ?? \App\Models\User::first();
            @endphp
            @if ($farmUser && ($farmUser->gcash_number || $farmUser->maya_number || $farmUser->gcash_qr_path || $farmUser->bank_details))
                <div class="mt-6 p-4 rounded-xl bg-slate-950/70 print:bg-slate-50 border border-slate-800 print:border-slate-300" x-data="{ copiedNumber: false }">
                    <div class="flex items-center justify-between mb-3 border-b border-slate-800/80 print:border-slate-200 pb-2">
                        <div class="text-xs font-bold text-white print:text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="qr-code" class="w-4 h-4 text-emerald-400 print:text-emerald-700"></i>
                            <span>Scan-to-Pay Instructions (GCash / Maya)</span>
                        </div>
                        <span class="text-[10px] text-slate-400 print:text-slate-500">Fast & Verified Payment</span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        @if ($farmUser->gcash_number || $farmUser->gcash_qr_path)
                            <div class="p-3 rounded-lg bg-blue-950/30 print:bg-blue-50/50 border border-blue-800/40 print:border-blue-200 flex items-center gap-3">
                                @if ($farmUser->gcash_qr_path)
                                    <div class="p-1 rounded bg-white shadow-sm flex-shrink-0">
                                        <img src="{{ asset('storage/' . $farmUser->gcash_qr_path) }}" alt="GCash QR" class="w-16 h-16 object-contain">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <span class="text-[10px] font-bold text-blue-400 print:text-blue-700 uppercase">GCash Account</span>
                                    <div class="font-bold text-white print:text-slate-900 font-mono text-sm flex items-center gap-2">
                                        <span>{{ $farmUser->gcash_number }}</span>
                                        @if ($farmUser->gcash_number)
                                            <button type="button" @click="navigator.clipboard.writeText('{{ $farmUser->gcash_number }}'); copiedNumber = true; setTimeout(() => copiedNumber = false, 2000)" 
                                                    class="p-1 text-slate-400 hover:text-white print:hidden" title="Copy Number">
                                                <i data-lucide="copy" class="w-3 h-3"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-300 print:text-slate-700 truncate">{{ $farmUser->gcash_name }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($farmUser->maya_number)
                            <div class="p-3 rounded-lg bg-emerald-950/30 print:bg-emerald-50/50 border border-emerald-800/40 print:border-emerald-200 flex items-center gap-3">
                                <div class="flex-1 min-w-0">
                                    <span class="text-[10px] font-bold text-emerald-400 print:text-emerald-700 uppercase">Maya Account</span>
                                    <div class="font-bold text-white print:text-slate-900 font-mono text-sm flex items-center gap-2">
                                        <span>{{ $farmUser->maya_number }}</span>
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $farmUser->maya_number }}'); copiedNumber = true; setTimeout(() => copiedNumber = false, 2000)" 
                                                class="p-1 text-slate-400 hover:text-white print:hidden" title="Copy Number">
                                            <i data-lucide="copy" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                    <div class="text-[11px] text-slate-300 print:text-slate-700 truncate">{{ $farmUser->maya_name }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($farmUser->bank_details)
                        <div class="mt-2.5 pt-2 border-t border-slate-800/80 print:border-slate-200 text-[11px] text-slate-400 print:text-slate-600">
                            <strong>Bank Transfer:</strong> {{ $farmUser->bank_details }}
                        </div>
                    @endif

                    <div x-show="copiedNumber" x-cloak class="mt-2 text-[10px] font-bold text-emerald-400 print:hidden">
                        ? Account number copied to clipboard!
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>