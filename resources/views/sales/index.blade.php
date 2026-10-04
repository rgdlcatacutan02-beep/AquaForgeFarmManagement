<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="badge-dollar-sign" class="w-5 h-5 text-emerald-400"></i>
                    <span>Farm Sales & Orders</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Livestock sales, batch fry fulfillment, payment tracking, and customer receipts</p>
            </div>
            <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-950 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Record New Sale</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Lifetime Gross Sales</span>
                    <div class="text-2xl font-bold text-emerald-400 font-mono mt-0.5">₱{{ number_format($totalRevenue, 2) }}</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-950/50 flex items-center justify-center text-emerald-400">
                    <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Paid Transactions</span>
                    <div class="text-2xl font-bold text-white font-mono mt-0.5">{{ $paidSalesCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-800/80 flex items-center justify-center text-cyan-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Pending / Unpaid</span>
                    <div class="text-2xl font-bold font-mono mt-0.5 {{ $pendingSalesCount > 0 ? 'text-amber-400' : 'text-slate-400' }}">
                        {{ $pendingSalesCount }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg {{ $pendingSalesCount > 0 ? 'bg-amber-950/50 text-amber-400' : 'bg-slate-800/80 text-slate-500' }} flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-center gap-2 w-full">
                <div class="relative flex-1 min-w-[200px]">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search sale # or customer name..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                </div>

                <select name="payment_status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                    <option value="">All Payments</option>
                    <option value="PAID" {{ request('payment_status') === 'PAID' ? 'selected' : '' }}>PAID</option>
                    <option value="UNPAID" {{ request('payment_status') === 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                    <option value="PARTIAL" {{ request('payment_status') === 'PARTIAL' ? 'selected' : '' }}>PARTIAL</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                    <option value="">All Orders</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>

                @if(request()->anyFilled(['search', 'payment_status', 'status']))
                    <a href="{{ route('sales.index') }}" class="px-2 py-1.5 text-xs text-slate-400 hover:text-white">Clear</a>
                @endif
            </form>
        </div>

        <!-- Sales Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-3 px-4">Sale #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Items Summary</th>
                            <th class="py-3 px-4">Total Amount</th>
                            <th class="py-3 px-4">Payment</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-cyan-400">
                                    <a href="{{ route('sales.show', $sale) }}" class="hover:underline flex items-center gap-1.5">
                                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-slate-500"></i>
                                        <span>{{ $sale->sale_number }}</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    @if($sale->customer)
                                        <a href="{{ route('customers.show', $sale->customer) }}" class="hover:text-cyan-400 transition-colors">
                                            {{ $sale->customer->name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Walk-in Customer</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td class="py-3.5 px-4 text-slate-300 max-w-xs truncate">
                                    @foreach($sale->items as $idx => $item)
                                        <span>{{ $item->quantity }}x {{ $item->item_description }}</span>{{ $idx < $sale->items->count() - 1 ? '; ' : '' }}
                                    @endforeach
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-400 text-sm">
                                    ₱{{ number_format($sale->total, 2) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $sale->payment_status === 'PAID' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' : ($sale->payment_status === 'PARTIAL' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' : 'bg-amber-950/60 text-amber-400 border-amber-800/50') }}">
                                        {{ $sale->payment_status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sale->status === 'COMPLETED' ? 'text-emerald-400 bg-emerald-950/30' : ($sale->status === 'CANCELLED' ? 'text-rose-400 bg-rose-950/30' : 'text-cyan-400 bg-cyan-950/30') }}">
                                        {{ $sale->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('sales.show', $sale) }}" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors text-xs flex items-center gap-1" title="View & Print Invoice">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            <span>View</span>
                                        </a>
                                        <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Remove sale record {{ $sale->sale_number }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-rose-400 transition-colors" title="Delete">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="receipt" class="w-8 h-8 text-slate-600"></i>
                                        <p>No sales orders match your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $sales->links() }}</div>
    </div>
</x-app-layout>
