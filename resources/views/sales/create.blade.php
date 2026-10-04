<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Record Farm Sale</h1>
                <p class="text-xs text-slate-400 mt-0.5">Fulfill livestock orders, deplete inventory, and generate receipts</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="salesForm()">
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
            @csrf

            <!-- Header Information Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i data-lucide="file-check" class="w-4 h-4 text-cyan-400"></i>
                    <span>Order & Customer Information</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Sale Order # <span class="text-rose-400">*</span></label>
                        <input type="text" name="sale_number" value="{{ old('sale_number', $suggestedSaleNumber) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500 @error('sale_number') border-rose-500 @enderror">
                        @error('sale_number')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Customer</label>
                        <select name="customer_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="">Walk-in / Anonymous Customer</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ (string) old('customer_id', request('customer_id')) === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} {{ $c->phone ? "({$c->phone})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Sale Date <span class="text-rose-400">*</span></label>
                        <input type="date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Status <span class="text-rose-400">*</span></label>
                        <select name="payment_status" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="PAID" {{ old('payment_status') === 'PAID' ? 'selected' : '' }}>PAID (Cash / Transfer Received)</option>
                            <option value="UNPAID" {{ old('payment_status') === 'UNPAID' ? 'selected' : '' }}>UNPAID (Pending Payment)</option>
                            <option value="PARTIAL" {{ old('payment_status') === 'PARTIAL' ? 'selected' : '' }}>PARTIAL (Deposit Made)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Order Status <span class="text-rose-400">*</span></label>
                        <select name="status" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="COMPLETED" {{ old('status') === 'COMPLETED' ? 'selected' : '' }}>COMPLETED (Fulfilled & Delivered)</option>
                            <option value="PENDING" {{ old('status') === 'PENDING' ? 'selected' : '' }}>PENDING (Awaiting Pickup / Dispatch)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Payment Notes / Method</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="e.g. Paid via PayNow, Cash on pickup"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
            </div>

            <!-- Dynamic Line Items Builder Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-white flex items-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4 text-cyan-400"></i>
                            <span>Sold Items & Livestock</span>
                        </h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Selecting individual fish automatically marks them as SOLD; selecting batches automatically depletes available fry</p>
                    </div>
                    <button type="button" @click="addItem()" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 border border-slate-700 transition-colors flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        <span>Add Item</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-3 relative group">
                            <!-- Quick Select Source Picker -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pb-2 border-b border-slate-900">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Quick Source: Individual Adult Livestock</label>
                                    <select @change="selectLivestock(index, $event.target.value)" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                                        <option value="">-- Choose Available Adult --</option>
                                        @foreach ($availableLivestock as $l)
                                            <option value="{{ $l->id }}" data-desc="[{{ $l->livestock_code }}] {{ $l->species->name }} ({{ ucfirst(strtolower($l->sex)) }} - {{ $l->quality_tier }})" data-price="25.00">
                                                [{{ $l->livestock_code }}] {{ $l->species->name }} ({{ $l->sex }} - {{ $l->quality_tier }}) - Tank {{ $l->tank->tank_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Quick Source: Offspring Batch (Fry)</label>
                                    <select @change="selectBatch(index, $event.target.value)" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                                        <option value="">-- Choose Available Fry Batch --</option>
                                        @foreach ($availableBatches as $b)
                                            <option value="{{ $b->id }}" data-desc="[{{ $b->batch_code }}] {{ $b->species->name }} Fry Cohort" data-price="5.00" data-max="{{ $b->available_count }}">
                                                [{{ $b->batch_code }}] {{ $b->species->name }} ({{ $b->available_count }} fry avail) - Tank {{ $b->tank->tank_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Line Item Row Inputs -->
                            <div class="grid grid-cols-12 gap-3 items-center">
                                <input type="hidden" :name="'items[' + index + '][livestock_id]'" :value="item.livestock_id">
                                <input type="hidden" :name="'items[' + index + '][offspring_batch_id]'" :value="item.offspring_batch_id">

                                <div class="col-span-12 sm:col-span-6">
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Description <span class="text-rose-400">*</span></label>
                                    <input type="text" :name="'items[' + index + '][item_description]'" x-model="item.description" required placeholder="e.g. Albino Full Red Guppy Trio"
                                        class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                                </div>

                                <div class="col-span-4 sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Qty <span class="text-rose-400">*</span></label>
                                    <input type="number" min="1" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" required
                                        class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                                </div>

                                <div class="col-span-4 sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Unit Price (₱) <span class="text-rose-400">*</span></label>
                                    <input type="number" step="0.01" min="0" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" required
                                        class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                                </div>

                                <div class="col-span-3 sm:col-span-1 text-right">
                                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">Line Total</label>
                                    <span class="font-mono font-bold text-xs text-emerald-400" x-text="'₱' + (item.quantity * item.unit_price).toFixed(2)"></span>
                                </div>

                                <div class="col-span-1 text-right pt-4">
                                    <button type="button" @click="removeItem(index)" class="p-1 rounded text-slate-500 hover:text-rose-400 hover:bg-slate-900 transition-colors" title="Remove line">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Totals Calculation Block -->
                <div class="pt-4 border-t border-slate-800 flex flex-col items-end gap-2 text-xs">
                    <input type="hidden" name="subtotal" :value="subtotal.toFixed(2)">
                    <input type="hidden" name="total" :value="total.toFixed(2)">

                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal:</span>
                            <span class="font-mono text-white" x-text="'₱' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-slate-400">
                            <span>Discount (₱):</span>
                            <input type="number" step="0.01" min="0" name="discount" x-model.number="discount"
                                class="w-24 bg-slate-950 border border-slate-800 rounded px-2 py-0.5 text-xs text-white text-right font-mono focus:outline-none focus:border-cyan-500">
                        </div>
                        <div class="flex justify-between text-sm font-bold pt-2 border-t border-slate-800 text-white">
                            <span>Total Payable:</span>
                            <span class="font-mono text-emerald-400" x-text="'₱' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('sales.index') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-950 transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Confirm & Record Sale</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function salesForm() {
            return {
                items: [
                    { description: '', quantity: 1, unit_price: 0, livestock_id: null, offspring_batch_id: null }
                ],
                discount: 0,
                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_price: 0, livestock_id: null, offspring_batch_id: null });
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                selectLivestock(index, id) {
                    if (!id) return;
                    const opt = event.target.selectedOptions[0];
                    this.items[index].description = opt.getAttribute('data-desc') || '';
                    this.items[index].quantity = 1;
                    this.items[index].unit_price = parseFloat(opt.getAttribute('data-price') || 0);
                    this.items[index].livestock_id = id;
                    this.items[index].offspring_batch_id = null;
                },
                selectBatch(index, id) {
                    if (!id) return;
                    const opt = event.target.selectedOptions[0];
                    this.items[index].description = opt.getAttribute('data-desc') || '';
                    this.items[index].quantity = 5;
                    this.items[index].unit_price = parseFloat(opt.getAttribute('data-price') || 0);
                    this.items[index].livestock_id = null;
                    this.items[index].offspring_batch_id = id;
                },
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.unit_price || 0)), 0);
                },
                get total() {
                    const d = parseFloat(this.discount) || 0;
                    return Math.max(0, this.subtotal - d);
                }
            }
        }
    </script>
</x-app-layout>
