<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('expenses.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Log Farm Expense</h1>
                <p class="text-xs text-slate-400 mt-0.5">Record costs for feed, power, livestock, water, or equipment</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Expense Category <span class="text-rose-400">*</span></label>
                    <select name="category" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        @foreach ($categories as $catKey => $catLabel)
                            <option value="{{ $catKey }}" {{ old('category') === $catKey ? 'selected' : '' }}>
                                {{ $catLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description <span class="text-rose-400">*</span></label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="e.g. 5kg Artemia Brine Shrimp Eggs, March Electricity Bill" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 @error('description') border-rose-500 @enderror">
                    @error('description')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Amount (₱) <span class="text-rose-400">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-500 @error('amount') border-rose-500 @enderror">
                        @error('amount')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Expense Date <span class="text-rose-400">*</span></label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500 @error('expense_date') border-rose-500 @enderror">
                        @error('expense_date')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Receipt Ref / Notes</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Invoice #99102 from FishFarmHub, paid via corporate debit"
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <a href="{{ route('expenses.index') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-md shadow-rose-950 transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Expense</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
