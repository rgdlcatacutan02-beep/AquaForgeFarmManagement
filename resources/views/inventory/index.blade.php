<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-cyan-400"></i>
                    <span>Supplies & Inventory</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Fish feed, medications, water conditioners, testing kits, and packaging stock</p>
            </div>
            <a href="{{ route('inventory.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Inventory Item</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Total Tracked Items</span>
                    <div class="text-2xl font-bold text-white font-mono mt-0.5">{{ $items->total() }}</div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-800/80 flex items-center justify-center text-cyan-400">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Low Stock Alert</span>
                    <div class="text-2xl font-bold font-mono mt-0.5 {{ $lowStockCount > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $lowStockCount }}
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg {{ $lowStockCount > 0 ? 'bg-rose-950/50 text-rose-400' : 'bg-emerald-950/50 text-emerald-400' }} flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs text-slate-400">Inventory Status</span>
                    <div class="text-sm font-semibold text-white mt-1">
                        @if($lowStockCount > 0)
                            <span class="text-rose-400 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Restock Needed</span>
                        @else
                            <span class="text-emerald-400 flex items-center gap-1"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Healthy Stock</span>
                        @endif
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-800/80 flex items-center justify-center text-slate-400">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-2 w-full">
                <div class="relative flex-1 min-w-[200px]">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items, supplier, notes..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                </div>

                <select name="category" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                    <option value="">All Categories</option>
                    <option value="FEED" {{ request('category') === 'FEED' ? 'selected' : '' }}>Feed & Nutrition</option>
                    <option value="MEDICATION" {{ request('category') === 'MEDICATION' ? 'selected' : '' }}>Medications</option>
                    <option value="WATER_TREATMENT" {{ request('category') === 'WATER_TREATMENT' ? 'selected' : '' }}>Water Treatment</option>
                    <option value="EQUIPMENT" {{ request('category') === 'EQUIPMENT' ? 'selected' : '' }}>Equipment</option>
                    <option value="PACKAGING" {{ request('category') === 'PACKAGING' ? 'selected' : '' }}>Packaging / Shipping</option>
                    <option value="OTHER" {{ request('category') === 'OTHER' ? 'selected' : '' }}>Other</option>
                </select>

                <a href="{{ route('inventory.index', array_merge(request()->except('low_stock', 'page'), request('low_stock') ? [] : ['low_stock' => 1])) }}"
                    class="px-2.5 py-1.5 text-xs rounded-lg border font-medium transition-colors flex items-center gap-1 {{ request('low_stock') ? 'bg-rose-950/60 border-rose-800 text-rose-300' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-slate-200' }}">
                    <i data-lucide="alert-triangle" class="w-3 h-3 text-rose-400"></i>
                    <span>Low Stock Only</span>
                </a>

                @if(request()->anyFilled(['search', 'category', 'low_stock']))
                    <a href="{{ route('inventory.index') }}" class="px-2 py-1.5 text-xs text-slate-400 hover:text-white">Clear</a>
                @endif
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Current Stock</th>
                            <th class="py-3 px-4">Min. Threshold</th>
                            <th class="py-3 px-4">Unit Cost</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($items as $item)
                            <tr class="hover:bg-slate-800/40 transition-colors {{ $item->isLowStock() ? 'bg-rose-950/10' : '' }}">
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $item->name }}</span>
                                        @if($item->notes)
                                            <span class="text-slate-500 hover:text-slate-300 cursor-help" title="{{ $item->notes }}">
                                                <i data-lucide="info" class="w-3 h-3"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ str_replace('_', ' ', $item->category) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold font-mono text-white text-sm">
                                    {{ $item->quantity }} <span class="text-xs text-slate-400 font-normal">{{ $item->unit }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-400">
                                    {{ $item->minimum_quantity }} {{ $item->unit }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    ₱{{ number_format($item->cost, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    {{ $item->supplier ?? '--' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->isLowStock())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-950/70 text-rose-300 border border-rose-800">
                                            <i data-lucide="alert-triangle" class="w-3 h-3 text-rose-400"></i>
                                            <span>LOW STOCK</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-950/40 text-emerald-400 border border-emerald-800/40">
                                            <i data-lucide="check" class="w-3 h-3 text-emerald-400"></i>
                                            <span>OK</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('inventory.edit', $item) }}" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-cyan-400 transition-colors" title="Edit">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <form method="POST" action="{{ route('inventory.destroy', $item) }}" onsubmit="return confirm('Delete {{ addslashes($item->name) }}?');" class="inline">
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
                                        <i data-lucide="package-open" class="w-8 h-8 text-slate-600"></i>
                                        <p>No inventory items match your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $items->links() }}</div>
    </div>
</x-app-layout>
