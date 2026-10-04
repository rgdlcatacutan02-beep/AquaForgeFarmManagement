<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.show', $sale) }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Edit Sale: {{ $sale->sale_number }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">Update payment status, fulfillment status, and order notes</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Order Information Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i data-lucide="file-check" class="w-4 h-4 text-cyan-400"></i>
                    <span>Order & Payment Details</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Customer</label>
                        <select name="customer_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="">Walk-in Customer</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ (string) old('customer_id', $sale->customer_id) === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} {{ $c->phone ? "({$c->phone})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Sale Date <span class="text-rose-400">*</span></label>
                        <input type="date" name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Status <span class="text-rose-400">*</span></label>
                        <select name="payment_status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="PAID" {{ old('payment_status', $sale->payment_status) === 'PAID' ? 'selected' : '' }}>PAID (Cash / GCash / Maya Received)</option>
                            <option value="UNPAID" {{ old('payment_status', $sale->payment_status) === 'UNPAID' ? 'selected' : '' }}>UNPAID (Pending Payment)</option>
                            <option value="PARTIAL" {{ old('payment_status', $sale->payment_status) === 'PARTIAL' ? 'selected' : '' }}>PARTIAL (Deposit / Installment)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Order Status <span class="text-rose-400">*</span></label>
                        <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="COMPLETED" {{ old('status', $sale->status) === 'COMPLETED' ? 'selected' : '' }}>COMPLETED (Fulfilled &amp; Delivered)</option>
                            <option value="PENDING" {{ old('status', $sale->status) === 'PENDING' ? 'selected' : '' }}>PENDING (Awaiting Pickup / Courier)</option>
                            <option value="CANCELLED" {{ old('status', $sale->status) === 'CANCELLED' ? 'selected' : '' }}>CANCELLED (Restores Livestock &amp; Stock)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Order Notes / Courier Tracking</label>
                    <textarea name="notes" rows="3" placeholder="e.g. GCash Ref #12345678, Lalamove tracking, packaging notes..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">{{ old('notes', $sale->notes) }}</textarea>
                </div>
            </div>

            <!-- Items Summary Read-Only Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider text-slate-400">Order Items ({{ $sale->items->count() }})</h3>
                <div class="divide-y divide-slate-800/60">
                    @foreach ($sale->items as $item)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-white">{{ $item->quantity }}x</span>
                                <span class="text-slate-300 ml-1">{{ $item->item_description }}</span>
                                @if ($item->livestock)
                                    <span class="ml-2 font-mono text-[10px] text-cyan-400 bg-cyan-950/60 px-1.5 py-0.5 rounded border border-cyan-800/40">{{ $item->livestock->livestock_code }}</span>
                                @elseif ($item->offspringBatch)
                                    <span class="ml-2 font-mono text-[10px] text-amber-400 bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-800/40">{{ $item->offspringBatch->batch_code }}</span>
                                @endif
                            </div>
                            <div class="font-mono text-emerald-400 font-bold">&#8369;{{ number_format($item->subtotal, 2) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-between text-sm font-bold text-white">
                    <span>Order Total:</span>
                    <span class="font-mono text-emerald-400 text-base">&#8369;{{ number_format($sale->total, 2) }}</span>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('sales.show', $sale) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-900/30 transition transform hover:-translate-y-0.5">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
