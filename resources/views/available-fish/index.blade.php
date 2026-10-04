<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5 text-emerald-400"></i>
                    <span>Available Fish &amp; Catalog Manager</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Control which fishes appear on your public stocklist, manage asking prices, and add new fish for sale.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('catalog.index') }}" target="_blank" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Preview Public Catalog</span>
                </a>

                <a href="{{ route('available-fish.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-950 transition transform hover:-translate-y-0.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>+ Add Fish for Sale</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ priceModalOpen: false, currentFishId: null, currentFishCode: '', currentFishPrice: 0 }">
        
        <!-- Alerts -->
        @if (session('success'))
            <div class="p-4 bg-emerald-950/80 border border-emerald-800/80 rounded-xl text-emerald-300 text-xs flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 flex-shrink-0"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <a href="{{ route('catalog.index') }}" target="_blank" class="text-xs underline text-emerald-400 hover:text-emerald-200 font-bold">
                    View Live Catalog &rarr;
                </a>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-900 border border-emerald-800/50 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400">Live on Catalog</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <div class="mt-2 text-2xl font-black text-white font-mono">{{ $catalogCount }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Specimens for sale to customers</div>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Catalog Inventory Value</div>
                <div class="mt-2 text-2xl font-black text-cyan-400 font-mono">&#8369;{{ number_format($catalogValue, 2) }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Total listed asking value</div>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Farm Stock</div>
                <div class="mt-2 text-2xl font-black text-white font-mono">{{ $totalLivestockCount }}</div>
                <div class="text-[10px] text-slate-400 mt-1">All breeders, growouts &amp; stock</div>
            </div>

            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 shadow-sm">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Fry Batches</div>
                <div class="mt-2 text-2xl font-black text-amber-400 font-mono">{{ $activeBatchesCount }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Available juvenile batches</div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-4">
            <!-- Tabs -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-3">
                <div class="flex items-center gap-1.5 text-xs font-semibold">
                    <a href="{{ route('available-fish.index', ['tab' => 'catalog']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ $tab === 'catalog' ? 'bg-emerald-950 text-emerald-300 border border-emerald-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        Live on Catalog ({{ $catalogCount }})
                    </a>
                    <a href="{{ route('available-fish.index', ['tab' => 'all']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ $tab === 'all' ? 'bg-cyan-950 text-cyan-300 border border-cyan-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        All Farm Fishes ({{ $totalLivestockCount }})
                    </a>
                    <a href="{{ route('available-fish.index', ['tab' => 'not_listed']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ $tab === 'not_listed' ? 'bg-slate-800 text-white border border-slate-700' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        Not for Sale ({{ max(0, $totalLivestockCount - $catalogCount) }})
                    </a>
                    <a href="{{ route('available-fish.index', ['tab' => 'batches']) }}" 
                       class="px-3 py-1.5 rounded-lg transition {{ $tab === 'batches' ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        Offspring Batches ({{ $activeBatchesCount }})
                    </a>
                </div>

                <div class="text-[11px] text-slate-400">
                    Showing <strong>{{ $livestock->count() }}</strong> records
                </div>
            </div>

            <!-- Search & Dropdown Filters -->
            @if ($tab !== 'batches')
                <form method="GET" action="{{ route('available-fish.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    <div class="relative flex-1 w-full">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by variety, strain, or specimen code..." 
                               class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-9 pr-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    </div>

                    <div class="w-full sm:w-48">
                        <select name="species_id" onchange="this.form.submit()" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                            <option value="">All Species</option>
                            @foreach ($speciesList as $sp)
                                <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'species_id']))
                        <a href="{{ route('available-fish.index', ['tab' => $tab]) }}" class="text-xs text-slate-400 hover:text-white">
                            Reset
                        </a>
                    @endif
                </form>
            @endif
        </div>

        @if ($tab === 'batches')
            <!-- Offspring Batches Section -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
                <div class="p-4 border-b border-slate-800 bg-slate-950/40 flex items-center justify-between">
                    <div class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-amber-400"></i>
                        <span>Active Fry Batches for Sale</span>
                    </div>
                    <a href="{{ route('batches.create') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold">
                        + Record New Batch
                    </a>
                </div>

                @if ($batches->isEmpty())
                    <div class="p-12 text-center text-slate-500 text-xs">
                        <i data-lucide="layers" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                        <p>No active offspring batches found.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="py-3 px-4">Batch Code</th>
                                    <th class="py-3 px-4">Species</th>
                                    <th class="py-3 px-4">Tank Location</th>
                                    <th class="py-3 px-4 text-center">Available Fry Count</th>
                                    <th class="py-3 px-4">Hatch Date</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($batches as $batch)
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="py-3 px-4 font-mono font-bold text-white">
                                            <a href="{{ route('batches.show', $batch) }}" class="text-cyan-400 hover:underline">
                                                {{ $batch->batch_code }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-white font-semibold">{{ $batch->species?->name }}</td>
                                        <td class="py-3 px-4 font-mono text-cyan-300">{{ $batch->tank?->tank_code ?? 'None' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800 text-[11px] font-bold">
                                                {{ $batch->current_count }} pcs
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-slate-400">{{ $batch->spawn_date?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-right">
                                            <a href="{{ route('batches.show', $batch) }}" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                                                Manage Batch
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @else
            <!-- Livestock / Individual Available Fishes Table -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
                @if ($livestock->isEmpty())
                    <div class="p-16 text-center text-slate-500 text-xs space-y-3">
                        <i data-lucide="tag" class="w-10 h-10 mx-auto text-slate-600"></i>
                        <p class="text-sm font-semibold text-slate-400">No fish found for this filter.</p>
                        @if ($tab === 'catalog')
                            <p class="text-slate-500 max-w-sm mx-auto">
                                You currently have no fishes marked as AVAILABLE. Click the button below to add your first fish for sale!
                            </p>
                            <a href="{{ route('available-fish.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                <span>+ Add Fish for Sale</span>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="py-3 px-4">Specimen / Variety</th>
                                    <th class="py-3 px-4">Species &amp; Code</th>
                                    <th class="py-3 px-4 text-center">Sex &amp; Grade</th>
                                    <th class="py-3 px-4 text-right">Asking Price</th>
                                    <th class="py-3 px-4 text-center">Catalog Status</th>
                                    <th class="py-3 px-4 text-center">1-Click Toggle</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($livestock as $fish)
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <!-- Specimen / Variety + Photo -->
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-lg bg-slate-950 border border-slate-800 overflow-hidden flex-shrink-0">
                                                    @if ($fish->photo_url)
                                                        <img src="{{ $fish->photo_url }}" alt="{{ $fish->variety }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-slate-700">
                                                            <i data-lucide="fish" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-white text-sm truncate">
                                                        {{ $fish->variety ?: $fish->species?->name }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                        <i data-lucide="box" class="w-3 h-3 text-cyan-400"></i>
                                                        <span>Tank: {{ $fish->tank?->tank_code ?? 'Unassigned' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Species & Code -->
                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-white">{{ $fish->species?->name }}</div>
                                            <div class="text-[10px] font-mono text-cyan-400">{{ $fish->livestock_code }}</div>
                                        </td>

                                        <!-- Sex & Grade -->
                                        <td class="py-3 px-4 text-center">
                                            <div class="inline-block px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-950 border border-slate-800 text-slate-200">
                                                {{ $fish->sex }}
                                            </div>
                                            <div class="text-[10px] text-amber-400 font-semibold mt-0.5">
                                                {{ $fish->grade ?? 'Breeder Grade' }}
                                            </div>
                                        </td>

                                        <!-- Asking Price -->
                                        <td class="py-3 px-4 text-right">
                                            <div class="font-bold text-sm font-mono text-emerald-400">
                                                &#8369;{{ number_format($fish->purchase_price, 2) }}
                                            </div>
                                            <button type="button" 
                                                    @click="currentFishId = {{ $fish->id }}; currentFishCode = '{{ $fish->livestock_code }}'; currentFishPrice = {{ $fish->purchase_price }}; priceModalOpen = true"
                                                    class="text-[10px] text-slate-400 hover:text-cyan-400 transition underline">
                                                Edit Price
                                            </button>
                                        </td>

                                        <!-- Catalog Status -->
                                        <td class="py-3 px-4 text-center">
                                            @if ($fish->status === 'AVAILABLE')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-950 text-emerald-300 border border-emerald-700 shadow-sm">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                    <span>Live on Catalog</span>
                                                </span>
                                            @else
                                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                                    {{ $fish->status }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 1-Click Toggle -->
                                        <td class="py-3 px-4 text-center">
                                            <form method="POST" action="{{ route('available-fish.toggle-catalog', $fish) }}">
                                                @csrf
                                                @if ($fish->status === 'AVAILABLE')
                                                    <button type="submit" 
                                                            title="Click to remove from public catalog" 
                                                            class="px-2.5 py-1 rounded-lg bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 text-[11px] font-semibold transition flex items-center gap-1 mx-auto">
                                                        <i data-lucide="eye-off" class="w-3.5 h-3.5"></i>
                                                        <span>Hide from Catalog</span>
                                                    </button>
                                                @else
                                                    <button type="submit" 
                                                            title="Click to publish onto public catalog" 
                                                            class="px-2.5 py-1 rounded-lg bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 border border-emerald-700 text-[11px] font-bold transition flex items-center gap-1 mx-auto shadow-sm">
                                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-emerald-400"></i>
                                                        <span>+ Put on Catalog</span>
                                                    </button>
                                                @endif
                                            </form>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3 px-4 text-right space-x-1">
                                            @if ($fish->status === 'AVAILABLE')
                                                <a href="{{ route('catalog.show', $fish) }}" target="_blank" 
                                                   title="View Customer Showcase Page"
                                                   class="inline-block p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-cyan-400 transition">
                                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('livestock.edit', $fish) }}" 
                                               title="Edit Full Specimen Record"
                                               class="inline-block p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 border-t border-slate-800">
                        {{ $livestock->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Quick Price Modal -->
        <div x-show="priceModalOpen" x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
             @keydown.escape.window="priceModalOpen = false">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4"
                 @click.away="priceModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="tag" class="w-4 h-4 text-emerald-400"></i>
                        <span>Update Asking Price</span>
                    </h3>
                    <button type="button" @click="priceModalOpen = false" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form :action="'{{ url('available-fish') }}/' + currentFishId + '/price'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <div class="text-xs text-slate-400 mb-1">
                            Specimen: <strong class="text-white font-mono" x-text="currentFishCode"></strong>
                        </div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">New Asking Price (PHP &#8369;)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-500 font-mono text-sm">&#8369;</span>
                            <input type="number" step="0.01" min="0" name="purchase_price" 
                                   x-model="currentFishPrice" required 
                                   class="w-full bg-slate-950 border border-slate-700 rounded-lg pl-8 pr-3 py-2 text-sm text-white font-mono focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="priceModalOpen = false" 
                                class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 text-xs hover:bg-slate-700">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">
                            Save Price
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
