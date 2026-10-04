<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="receipt" class="w-5 h-5 text-rose-400"></i>
                    <span>Farm Expenses</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Feed, electricity, water utility, equipment maintenance, and packaging costs</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-md shadow-rose-950 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Log New Expense</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Total Lifetime Expenses</span>
                    <div class="text-2xl font-bold text-rose-400 font-mono mt-0.5">₱{{ number_format($totalExpenses, 2) }}</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-950/50 flex items-center justify-center text-rose-400">
                    <i data-lucide="trending-down" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Expenses This Month</span>
                    <div class="text-2xl font-bold text-white font-mono mt-0.5">₱{{ number_format($monthExpenses, 2) }}</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-800/80 flex items-center justify-center text-cyan-400">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Expense Entries</span>
                    <div class="text-2xl font-bold text-white font-mono mt-0.5">{{ $expenses->total() }}</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-800/80 flex items-center justify-center text-slate-400">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('expenses.index') }}" class="flex flex-wrap items-center gap-2 w-full">
                <div class="relative flex-1 min-w-[200px]">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description or notes..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                </div>

                <select name="category" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $catKey => $catLabel)
                        <option value="{{ $catKey }}" {{ request('category') === $catKey ? 'selected' : '' }}>
                            {{ $catLabel }}
                        </option>
                    @endforeach
                </select>

                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('expenses.index') }}" class="px-2 py-1.5 text-xs text-slate-400 hover:text-white">Clear</a>
                @endif
            </form>
        </div>

        <!-- Expenses Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Notes</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($expenses as $exp)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">{{ $exp->expense_date->format('M d, Y') }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ $categories[$exp->category] ?? $exp->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-white">{{ $exp->description }}</td>
                                <td class="py-3.5 px-4 font-mono font-bold text-rose-400 text-sm">₱{{ number_format($exp->amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-slate-400 max-w-xs truncate">{{ $exp->notes ?? '--' }}</td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('expenses.edit', $exp) }}" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-cyan-400 transition-colors" title="Edit">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <form method="POST" action="{{ route('expenses.destroy', $exp) }}" onsubmit="return confirm('Delete this expense entry?');" class="inline">
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
                                <td colspan="6" class="py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="wallet" class="w-8 h-8 text-slate-600"></i>
                                        <p>No expenses found matching your filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $expenses->links() }}</div>
    </div>
</x-app-layout>
