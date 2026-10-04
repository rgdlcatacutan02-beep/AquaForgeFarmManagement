<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-cyan-400"></i>
                    <span>Farm Reports & Economics</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Practical breeding metrics, survival performance, and farm financial health</p>
            </div>

            <button onclick="window.print()" class="self-start sm:self-auto px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors flex items-center gap-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print Report</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- 1. Farm Financial Overview (Section 31) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="scale" class="w-4 h-4 text-cyan-400"></i>
                        <span>Farm Financial Overview</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Fundamental equation: <code class="font-mono text-cyan-300">Revenue &minus; Expenses = Net Operating Result</code></p>
                </div>
                <div>
                    @if($netResult >= 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950/70 text-emerald-400 border border-emerald-800/60">
                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                            <span>Net Operational Surplus</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-950/70 text-amber-400 border border-amber-800/60">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            <span>Net Operating Deficit</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Lifetime Financial Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-xs text-slate-400">Total Lifetime Revenue</span>
                    <div class="mt-1 text-2xl font-bold text-emerald-400 font-mono">₱{{ number_format($totalRevenue, 2) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                        <span>{{ $totalOrders }} orders</span> &bull; <span>{{ $totalUnitsSold }} units sold</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-xs text-slate-400">Total Lifetime Expenses</span>
                    <div class="mt-1 text-2xl font-bold text-rose-400 font-mono">₱{{ number_format($totalExpenses, 2) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Feed, utilities, livestock & gear</div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-xs text-slate-400">Net Lifetime Operating Result</span>
                    <div class="mt-1 text-2xl font-bold font-mono {{ $netResult >= 0 ? 'text-cyan-400' : 'text-amber-400' }}">
                        ₱{{ number_format($netResult, 2) }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">Direct cash position of farm</div>
                </div>
            </div>

            <!-- Month-to-Date Performance Sub-panel -->
            <div class="p-4 rounded-xl bg-slate-950/40 border border-slate-800/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-2 text-slate-300">
                    <i data-lucide="calendar" class="w-4 h-4 text-cyan-400"></i>
                    <span class="font-semibold">Current Month Performance ({{ date('F Y') }}):</span>
                </div>
                <div class="flex flex-wrap items-center gap-4 font-mono">
                    <div>
                        <span class="text-slate-400">Revenue:</span>
                        <span class="text-emerald-400 font-bold ml-1">₱{{ number_format($monthRevenue, 2) }}</span>
                    </div>
                    <div class="text-slate-600">&bull;</div>
                    <div>
                        <span class="text-slate-400">Expenses:</span>
                        <span class="text-rose-400 font-bold ml-1">₱{{ number_format($monthExpenses, 2) }}</span>
                    </div>
                    <div class="text-slate-600">&bull;</div>
                    <div>
                        <span class="text-slate-400">Month Net:</span>
                        <span class="font-bold ml-1 {{ $monthNet >= 0 ? 'text-cyan-400' : 'text-amber-400' }}">₱{{ number_format($monthNet, 2) }}</span>
                    </div>
                    <div class="text-slate-600">&bull;</div>
                    <div>
                        <span class="text-slate-400">Avg Order:</span>
                        <span class="text-white font-bold ml-1">₱{{ number_format($averageOrderValue, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Breeding & Fry Survival Performance (Section 32) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="heart" class="w-4 h-4 text-rose-400"></i>
                    <span>Breeding & Survival Performance</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Offspring production, survival rates, and natural culling statistics</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 text-center">
                    <span class="text-xs text-slate-400">Total Fry Hatched</span>
                    <div class="mt-1 text-2xl font-bold text-white font-mono">{{ $totalInitialFry }}</div>
                    <p class="text-[10px] text-slate-500 mt-0.5">Initial cohort count</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 text-center">
                    <span class="text-xs text-slate-400">Overall Survival Rate</span>
                    <div class="mt-1 text-2xl font-bold text-cyan-400 font-mono">{{ $survivalRate }}%</div>
                    <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-cyan-500 h-full rounded-full transition-all" style="width: {{ min(100, $survivalRate) }}%"></div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 text-center">
                    <span class="text-xs text-slate-400">Natural Mortality</span>
                    <div class="mt-1 text-2xl font-bold text-rose-400 font-mono">{{ $totalDeaths }}</div>
                    <p class="text-[10px] text-slate-500 mt-0.5">Losses logged in audit</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 text-center">
                    <span class="text-xs text-slate-400">Selective Culling</span>
                    <div class="mt-1 text-2xl font-bold text-amber-400 font-mono">{{ $totalCulls }}</div>
                    <p class="text-[10px] text-slate-500 mt-0.5">Quality control removals</p>
                </div>
            </div>
        </div>

        <!-- 3. Species Inventory & Livestock Distribution (Section 33) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="fish" class="w-4 h-4 text-cyan-400"></i>
                        <span>Species Inventory & Livestock Distribution</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Active breeding adults and growing fry population broken down by species</p>
                </div>
                <div class="text-xs font-mono text-slate-300">
                    <span class="text-white font-bold">{{ $totalActiveAdults }}</span> Adults &bull; <span class="text-cyan-400 font-bold">{{ $totalActiveFry }}</span> Fry Growing
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-3 px-4">Species Name</th>
                            <th class="py-3 px-4">Scientific Name</th>
                            <th class="py-3 px-4 text-center">Active Adults</th>
                            <th class="py-3 px-4 text-center">Active Fry Batches</th>
                            <th class="py-3 px-4 text-center">Total Growing Fry</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach ($livestockBySpecies as $sp)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                        <span>{{ $sp->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 italic text-slate-400">{{ $sp->scientific_name }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-white">{{ $sp->livestock_count }}</td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-300">{{ $sp->offspring_batches_count }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-cyan-400">{{ $sp->active_fry_count ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Expense Breakdown by Category (Section 34) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-rose-400"></i>
                    <span>Expense Breakdown by Category</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Cost distribution across operational departments and farm inputs</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4 text-center">Entries</th>
                            <th class="py-3 px-4">Total Amount</th>
                            <th class="py-3 px-4">% of Expenses</th>
                            <th class="py-3 px-4">Distribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($expensesByCategory as $expCat)
                            @php
                                $percent = $totalExpenses > 0 ? round(($expCat->total_amount / $totalExpenses) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ str_replace('_', ' ', $expCat->category) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400">{{ $expCat->count }}</td>
                                <td class="py-3.5 px-4 font-mono font-bold text-rose-400">₱{{ number_format($expCat->total_amount, 2) }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">{{ $percent }}%</td>
                                <td class="py-3.5 px-4 w-48">
                                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                                        <div class="bg-rose-500 h-full rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500">No expenses recorded to calculate distribution.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
