<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Edit Inventory Item</h1>
                <p class="text-xs text-slate-400 mt-0.5">Update stock count, thresholds, or supplier info</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <form method="POST" action="{{ route('inventory.update', $item) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Item Name <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 @error('name') border-rose-500 @enderror">
                        @error('name')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Category <span class="text-rose-400">*</span></label>
                        <select name="category" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="FEED" {{ old('category', $item->category) === 'FEED' ? 'selected' : '' }}>Feed & Nutrition</option>
                            <option value="MEDICATION" {{ old('category', $item->category) === 'MEDICATION' ? 'selected' : '' }}>Medications</option>
                            <option value="WATER_TREATMENT" {{ old('category', $item->category) === 'WATER_TREATMENT' ? 'selected' : '' }}>Water Treatment</option>
                            <option value="EQUIPMENT" {{ old('category', $item->category) === 'EQUIPMENT' ? 'selected' : '' }}>Equipment & Hardware</option>
                            <option value="PACKAGING" {{ old('category', $item->category) === 'PACKAGING' ? 'selected' : '' }}>Packaging / Shipping</option>
                            <option value="OTHER" {{ old('category', $item->category) === 'OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Supplier / Brand</label>
                        <input type="text" name="supplier" value="{{ old('supplier', $item->supplier) }}"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Current Quantity <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity', $item->quantity) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                        @error('quantity')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Unit of Measure <span class="text-rose-400">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit', $item->unit) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                        @error('unit')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Alert Threshold <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" name="minimum_quantity" value="{{ old('minimum_quantity', $item->minimum_quantity) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                        @error('minimum_quantity')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Unit Cost (₱) <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" name="cost" value="{{ old('cost', $item->cost) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500">
                        @error('cost')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Storage Location / Notes</label>
                        <textarea name="notes" rows="2"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">{{ old('notes', $item->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Update Item</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
