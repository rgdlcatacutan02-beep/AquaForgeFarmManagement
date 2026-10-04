<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Add Inventory Item</h1>
                <p class="text-xs text-slate-400 mt-0.5">Track feed, medications, water conditioners, or equipment supplies</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <form method="POST" action="{{ route('inventory.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Item Name <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Hikari Fancy Guppy Pellets 22g" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 @error('name') border-rose-500 @enderror">
                        @error('name')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Category <span class="text-rose-400">*</span></label>
                        <select name="category" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="FEED" {{ old('category') === 'FEED' ? 'selected' : '' }}>Feed & Nutrition</option>
                            <option value="MEDICATION" {{ old('category') === 'MEDICATION' ? 'selected' : '' }}>Medications</option>
                            <option value="WATER_TREATMENT" {{ old('category') === 'WATER_TREATMENT' ? 'selected' : '' }}>Water Treatment</option>
                            <option value="EQUIPMENT" {{ old('category') === 'EQUIPMENT' ? 'selected' : '' }}>Equipment & Hardware</option>
                            <option value="PACKAGING" {{ old('category') === 'PACKAGING' ? 'selected' : '' }}>Packaging / Shipping</option>
                            <option value="OTHER" {{ old('category') === 'OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Supplier / Brand</label>
                        <input type="text" name="supplier" value="{{ old('supplier') }}" placeholder="e.g. Hikari, Seachem, Local Wholesaler"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Initial Quantity <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity', 1) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                        @error('quantity')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Unit of Measure <span class="text-rose-400">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit', 'grams') }}" placeholder="e.g. grams, ml, pcs, bottles, kg" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                        @error('unit')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Alert Threshold <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" name="minimum_quantity" value="{{ old('minimum_quantity', 1) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                        <p class="text-[10px] text-slate-500 mt-1">Triggers low stock badge when quantity falls to or below this level</p>
                        @error('minimum_quantity')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Unit Cost (₱) <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" name="cost" value="{{ old('cost', '0.00') }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                        @error('cost')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Storage Location / Notes</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Stored in dry feed cabinet shelf 2; expiry date 2027-04"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Item</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
