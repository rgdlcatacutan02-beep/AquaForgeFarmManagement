<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('customers.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                        <span>{{ $customer->name }}</span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Registered customer since {{ $customer->created_at->format('M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors flex items-center gap-1.5">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    <span>Edit Profile</span>
                </a>
                <a href="{{ route('sales.create') }}?customer_id={{ $customer->id }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>New Sale Order</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Profile & KPI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 shadow-sm md:col-span-1 space-y-3">
                <h2 class="text-xs uppercase tracking-wider font-semibold text-slate-400">Contact Information</h2>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2 text-slate-300">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-cyan-400"></i>
                        <span class="font-mono">{{ $customer->phone ?? 'No phone provided' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-300">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-cyan-400"></i>
                        <span>{{ $customer->email ?? 'No email provided' }}</span>
                    </div>
                    <div class="flex items-start gap-2 text-slate-300">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-cyan-400 mt-0.5"></i>
                        <span>{{ $customer->address ?? 'No address registered' }}</span>
                    </div>
                </div>

                @if($customer->notes)
                    <div class="pt-3 border-t border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Notes & Preferences</span>
                        <p class="text-xs text-slate-300 mt-1 italic">{{ $customer->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs text-slate-400">Total Orders Placed</span>
                    <div class="text-3xl font-bold text-white font-mono mt-1">{{ $customer->sales->count() }}</div>
                </div>
                <p class="text-[11px] text-slate-500">Completed & active sales orders</p>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs text-slate-400">Lifetime Farm Revenue</span>
                    <div class="text-3xl font-bold text-emerald-400 font-mono mt-1">₱{{ number_format($totalSpent, 2) }}</div>
                </div>
                <p class="text-[11px] text-slate-500">Total purchases excluding cancelled orders</p>
            </div>
        </div>

        <!-- Orders History -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-800 bg-slate-950/40 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-cyan-400"></i>
                    <span>Purchase History</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/30">
                        <tr>
                            <th class="py-3 px-4">Sale #</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Line Items</th>
                            <th class="py-3 px-4">Payment</th>
                            <th class="py-3 px-4">Order Status</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($customer->sales as $sale)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-cyan-400">
                                    <a href="{{ route('sales.show', $sale) }}" class="hover:underline">
                                        {{ $sale->sale_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $sale->items->count() }} item(s)</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $sale->payment_status === 'PAID' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' : 'bg-amber-950/60 text-amber-400 border-amber-800/50' }}">
                                        {{ $sale->payment_status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sale->status === 'COMPLETED' ? 'text-emerald-400' : ($sale->status === 'CANCELLED' ? 'text-rose-400' : 'text-cyan-400') }}">
                                        {{ $sale->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-white">₱{{ number_format($sale->total, 2) }}</td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors">
                                        <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                        <span>Invoice</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">
                                    No sales transactions recorded for this customer yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
