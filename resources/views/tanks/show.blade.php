<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('tanks.index') }}" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-bold font-mono text-cyan-400">{{ $tank->tank_code }}</span>
                        <span class="text-slate-400">&bull;</span>
                        <h1 class="text-xl font-bold text-white tracking-tight">{{ $tank->name }}</h1>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $tank->location ?? 'Unspecified' }} | {{ $tank->volume_liters }} Liters | {{ $tank->tank_type }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tanks.print-label', $tank) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-300 text-xs font-semibold rounded-lg border border-cyan-700/50 transition">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Print QR Sticker</span>
                </a>
                <a href="{{ route('tanks.edit', $tank) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Edit Tank</span>
                </a>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-md border {{ $tank->status === 'ACTIVE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' : 'bg-slate-800 text-slate-300 border-slate-700' }}">
                    {{ $tank->status }}
                </span>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-md border bg-slate-900 text-cyan-300 border-slate-800">
                    {{ $tank->purpose }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- MOBILE QUICK ACTIONS BAR (Section 43) -->
    <div class="mb-6 p-4 rounded-xl bg-slate-900/90 border border-slate-800 shadow-sm" x-data="{ activeModal: null }">
        <div class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
            <span>Quick Tank Actions</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <button @click="activeModal = 'waterTest'" 
                    class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg bg-cyan-950/70 hover:bg-cyan-900/80 text-cyan-300 border border-cyan-800/60 text-xs font-semibold transition-all">
                <i data-lucide="droplet" class="w-4 h-4 text-cyan-400"></i>
                <span>Water Test</span>
            </button>
            <button @click="activeModal = 'feeding'" 
                    class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg bg-indigo-950/70 hover:bg-indigo-900/80 text-indigo-300 border border-indigo-800/60 text-xs font-semibold transition-all">
                <i data-lucide="utensils" class="w-4 h-4 text-indigo-400"></i>
                <span>Feeding</span>
            </button>
            <button @click="activeModal = 'waterChange'" 
                    class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg bg-teal-950/70 hover:bg-teal-900/80 text-teal-300 border border-teal-800/60 text-xs font-semibold transition-all">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-teal-400"></i>
                <span>Water Change</span>
            </button>
            <button @click="activeModal = 'maintenance'" 
                    class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg bg-amber-950/70 hover:bg-amber-900/80 text-amber-300 border border-amber-800/60 text-xs font-semibold transition-all">
                <i data-lucide="wrench" class="w-4 h-4 text-amber-400"></i>
                <span>Maintenance</span>
            </button>
        </div>

        <!-- MODAL: Record Water Test -->
        <div x-show="activeModal === 'waterTest'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="activeModal = null" class="bg-slate-900 border border-slate-800 rounded-xl p-5 max-w-md w-full shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="droplet" class="w-4 h-4 text-cyan-400"></i>
                        Record Water Parameters
                    </h3>
                    <button @click="activeModal = null" class="text-slate-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('water-logs.store') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="tank_id" value="{{ $tank->id }}">
                    <input type="hidden" name="recorded_at" value="{{ now()->format('Y-m-d H:i:s') }}">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-slate-300">Temp (?C)</label>
                            <input type="number" step="0.1" name="temperature" placeholder="26.5" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-300">pH</label>
                            <input type="number" step="0.1" name="ph" placeholder="7.2" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-300">Ammonia (ppm)</label>
                            <input type="number" step="0.01" name="ammonia" placeholder="0.00" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-300">Nitrite (ppm)</label>
                            <input type="number" step="0.01" name="nitrite" placeholder="0.00" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-300">Nitrate (ppm)</label>
                            <input type="number" step="1" name="nitrate" placeholder="20" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-300">TDS (Optional)</label>
                            <input type="number" step="1" name="tds" placeholder="250" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Notes</label>
                        <input type="text" name="notes" placeholder="Parameters check..." class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="activeModal = null" class="px-3 py-1.5 rounded bg-slate-800 text-slate-300 text-xs">Cancel</button>
                        <button type="submit" class="px-3 py-1.5 rounded bg-cyan-600 text-white font-semibold text-xs">Save Log</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Quick Feeding -->
        <div x-show="activeModal === 'feeding'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="activeModal = null" class="bg-slate-900 border border-slate-800 rounded-xl p-5 max-w-md w-full shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="utensils" class="w-4 h-4 text-indigo-400"></i>
                        Record Tank Feeding
                    </h3>
                    <button @click="activeModal = null" class="text-slate-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('feeding.store') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="tank_id" value="{{ $tank->id }}">
                    <input type="hidden" name="fed_at" value="{{ now()->format('Y-m-d H:i:s') }}">
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Food Type *</label>
                        <input type="text" name="food" required placeholder="Guppy micro pellets / Live BBS" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Quantity</label>
                        <input type="text" name="quantity" placeholder="1 pinch / 5ml pipette" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Notes</label>
                        <input type="text" name="notes" placeholder="Fed eagerly..." class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="activeModal = null" class="px-3 py-1.5 rounded bg-slate-800 text-slate-300 text-xs">Cancel</button>
                        <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-semibold text-xs">Save Feeding</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Water Change / Maintenance -->
        <div x-show="activeModal === 'waterChange' || activeModal === 'maintenance'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="activeModal = null" class="bg-slate-900 border border-slate-800 rounded-xl p-5 max-w-md w-full shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="wrench" class="w-4 h-4 text-teal-400"></i>
                        Record Maintenance Task
                    </h3>
                    <button @click="activeModal = null" class="text-slate-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('maintenance.store') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="tank_id" value="{{ $tank->id }}">
                    <input type="hidden" name="performed_at" value="{{ now()->format('Y-m-d H:i:s') }}">
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Maintenance Type *</label>
                        <select name="maintenance_type" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                            <option value="WATER_CHANGE" :selected="activeModal === 'waterChange'">Water Change</option>
                            <option value="FILTER_CLEANING">Filter Cleaning</option>
                            <option value="TANK_CLEANING">Tank Cleaning</option>
                            <option value="EQUIPMENT_CHECK">Equipment Check</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Water Change % (if applicable)</label>
                        <input type="number" min="0" max="100" name="water_change_percentage" placeholder="30" class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-300">Notes</label>
                        <input type="text" name="notes" placeholder="Conditioner added, sponge squeezed..." class="mt-1 w-full bg-slate-950 border border-slate-800 rounded px-2.5 py-1.5 text-xs text-white">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="activeModal = null" class="px-3 py-1.5 rounded bg-slate-800 text-slate-300 text-xs">Cancel</button>
                        <button type="submit" class="px-3 py-1.5 rounded bg-teal-600 text-white font-semibold text-xs">Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN TANK METRICS & QR CODE GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- TANK DETAILS & WATER PARAMETERS (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Latest Water Parameters Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i data-lucide="droplet" class="w-4 h-4 text-cyan-400"></i>
                        Latest Water Chemistry
                    </h2>
                    @if ($tank->latestWaterLog)
                        <span class="text-[11px] text-slate-400">Tested {{ $tank->latestWaterLog->recorded_at->diffForHumans() }}</span>
                    @endif
                </div>

                @if ($tank->latestWaterLog)
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                        <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase">Temp</span>
                            <div class="mt-1 text-lg font-bold text-white font-mono">{{ $tank->latestWaterLog->temperature ?? '--' }}?C</div>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase">pH</span>
                            <div class="mt-1 text-lg font-bold text-white font-mono">{{ $tank->latestWaterLog->ph ?? '--' }}</div>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase">Ammonia</span>
                            <div class="mt-1 text-lg font-bold font-mono {{ ($tank->latestWaterLog->ammonia ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ $tank->latestWaterLog->ammonia ?? '--' }}</div>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase">Nitrite</span>
                            <div class="mt-1 text-lg font-bold font-mono {{ ($tank->latestWaterLog->nitrite ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ $tank->latestWaterLog->nitrite ?? '--' }}</div>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase">Nitrate</span>
                            <div class="mt-1 text-lg font-bold font-mono {{ ($tank->latestWaterLog->nitrate ?? 0) > 40 ? 'text-amber-400' : 'text-slate-200' }}">{{ $tank->latestWaterLog->nitrate ?? '--' }}</div>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 uppercase">Status</span>
                            <div class="mt-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $tank->latestWaterLog->status === 'GOOD' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' : 'bg-amber-950/60 text-amber-400 border-amber-800/50' }}">
                                    {{ $tank->latestWaterLog->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center text-slate-500 text-xs">
                        No water parameter logs on record yet. Use the Quick Action above to record one.
                    </div>
                @endif

                <!-- Last Water Change Info -->
                <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-teal-400"></i>
                        <span>Last Water Change:</span>
                        <strong class="text-slate-200">{{ $tank->lastWaterChange ? $tank->lastWaterChange->performed_at->format('M d, Y') . ' (' . $tank->lastWaterChange->water_change_percentage . '%)' : 'None recorded' }}</strong>
                    </div>
                    <span class="text-[11px] text-slate-500">{{ $tank->notes ?? '' }}</span>
                </div>
            </div>

            <!-- CURRENT OCCUPANTS: Livestock & Batches -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i data-lucide="fish" class="w-4 h-4 text-cyan-400"></i>
                        Current Tank Occupants
                    </h2>
                    <span class="text-xs text-slate-400">{{ $tank->livestock->count() }} Specimen(s) | {{ $tank->offspringBatches->count() }} Batch(es)</span>
                </div>

                <div class="mt-4 space-y-4">
                    <!-- Individual Livestock -->
                    @if ($tank->livestock->isNotEmpty())
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Individual Specimens</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($tank->livestock as $animal)
                                    <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-cyan-400">{{ $animal->livestock_code }}</span>
                                                <span class="font-semibold text-white">{{ $animal->variety ?: $animal->species?->name }}</span>
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-1">
                                                {{ $animal->sex }} &bull; Grade: {{ $animal->grade ?? 'None' }} &bull; {{ $animal->status }}
                                            </div>
                                        </div>
                                        <a href="{{ route('livestock.show', $animal) }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">View &rarr;</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Offspring Batches -->
                    @if ($tank->offspringBatches->isNotEmpty())
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Offspring Batches</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($tank->offspringBatches as $b)
                                    <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-indigo-400">{{ $b->batch_code }}</span>
                                                <span class="font-semibold text-white">{{ $b->variety ?: $b->species?->name }}</span>
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-1">
                                                Pop: <strong class="text-emerald-400">{{ $b->current_count }}</strong> (Avail: {{ $b->available_count }}) &bull; {{ $b->status }}
                                            </div>
                                        </div>
                                        <a href="{{ route('batches.show', $b) }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">View &rarr;</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($tank->livestock->isEmpty() && $tank->offspringBatches->isEmpty())
                        <div class="py-6 text-center text-slate-500 text-xs">
                            No livestock or batches currently assigned to this tank.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Feeding & Maintenance Activity Section (Phase 4) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Feeding History -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3">
                        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                            <i data-lucide="utensils" class="w-4 h-4 text-indigo-400"></i>
                            Feeding History
                        </h3>
                        <button type="button" @click="activeModal = 'feeding'" class="text-xs text-indigo-400 hover:underline">+ Log Feed</button>
                    </div>
                    @if($tank->feedingLogs->isEmpty())
                        <p class="text-xs text-slate-500 italic py-4 text-center">No recent feeding events logged.</p>
                    @else
                        <div class="divide-y divide-slate-800/60 text-xs">
                            @foreach($tank->feedingLogs->take(5) as $fLog)
                                <div class="py-2 flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-slate-200">{{ $fLog->food }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $fLog->fed_at?->format('M d, H:i') }}</div>
                                    </div>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $fLog->quantity }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Maintenance History -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3">
                        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                            <i data-lucide="wrench" class="w-4 h-4 text-teal-400"></i>
                            Maintenance History
                        </h3>
                        <button type="button" @click="activeModal = 'maintenance'" class="text-xs text-teal-400 hover:underline">+ Log Task</button>
                    </div>
                    @if($tank->maintenanceLogs->isEmpty())
                        <p class="text-xs text-slate-500 italic py-4 text-center">No maintenance tasks recorded.</p>
                    @else
                        <div class="divide-y divide-slate-800/60 text-xs">
                            @foreach($tank->maintenanceLogs->take(5) as $mLog)
                                <div class="py-2 flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-slate-200">{{ $mLog->maintenance_type }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $mLog->performed_at?->format('M d, H:i') }}</div>
                                    </div>
                                    @if($mLog->water_change_percentage)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-950/60 text-teal-400 border border-teal-800/40">{{ $mLog->water_change_percentage }}%</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COL: TANK QR CODE (Section 17) & ACTIVE BREEDING -->
        <div class="space-y-6">
            <!-- TANK QR CODE CARD -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm text-center">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 text-left">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i data-lucide="qr-code" class="w-4 h-4 text-cyan-400"></i>
                        Tank QR Hub
                    </h2>
                    <span class="text-[10px] text-slate-500 font-mono">{{ $tank->tank_code }}</span>
                </div>

                <div class="my-5 flex flex-col items-center justify-center">
                    <div class="p-3 rounded-xl bg-white shadow-xl shadow-cyan-950/60 inline-block">
                        {!! $qrCodeSvg !!}
                    </div>
                    <div class="mt-3 text-xs font-mono font-bold text-cyan-300">{{ route('tanks.show', $tank) }}</div>
                    <p class="mt-1 text-[11px] text-slate-400 max-w-xs leading-relaxed">
                        Scan with your smartphone camera while in the fishroom to immediately open this tank and log feeding or water parameters.
                    </p>
                    <a href="{{ route('tanks.print-label', $tank) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-cyan-950 transition">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Print Tank Sticker Tag</span>
                    </a>
                </div>
            </div>

            <!-- ACTIVE BREEDING IN THIS TANK -->
            @if ($tank->breedingEvents->isNotEmpty())
                <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                        <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                            <i data-lucide="heart-handshake" class="w-4 h-4 text-rose-400"></i>
                            Active Breeding Pair
                        </h2>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach ($tank->breedingEvents as $ev)
                            <div class="p-3 rounded-lg bg-slate-950/70 border border-slate-800 text-xs">
                                <div class="font-mono font-bold text-rose-400">{{ $ev->breeding_code }} ({{ $ev->species?->name }})</div>
                                <div class="text-[11px] text-slate-300 mt-1">
                                    Male: {{ $ev->maleLivestock?->livestock_code ?? 'M?' }} &times; Female: {{ $ev->femaleLivestock?->livestock_code ?? 'F?' }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1">Started {{ $ev->start_date->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Automatic QR Code Sticker Modal (opens automatically when tank is created) -->
    <div x-data="{ qrModalOpen: {{ session('show_qr_print_modal') ? 'true' : 'false' }} }"
         x-show="qrModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         @keydown.escape.window="qrModalOpen = false">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl relative text-center space-y-4"
             @click.away="qrModalOpen = false">
            
            <div class="w-12 h-12 rounded-full bg-emerald-950/80 border border-emerald-800/80 text-emerald-400 flex items-center justify-center mx-auto">
                <i data-lucide="check" class="w-6 h-6"></i>
            </div>

            <div>
                <h2 class="text-base font-bold text-white">Tank Registered Successfully!</h2>
                <p class="text-xs text-slate-400 mt-1">A unique QR code tag has been automatically generated for <strong class="text-white">{{ $tank->tank_code }} ({{ $tank->name }})</strong>.</p>
            </div>

            <!-- Tag Preview Card -->
            <div class="p-4 rounded-xl bg-white text-slate-950 border-2 border-slate-900 shadow-md text-left flex items-center gap-3">
                <div class="w-24 h-24 p-1 rounded-lg border border-slate-900 flex-shrink-0 flex items-center justify-center">
                    {!! $qrCodeSvg !!}
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[9px] font-mono text-cyan-700 font-extrabold block uppercase">{{ $tank->purpose }} TANK</span>
                    <div class="text-lg font-black font-mono text-slate-950 leading-tight">{{ $tank->tank_code }}</div>
                    <div class="text-xs font-bold text-slate-800 truncate">{{ $tank->name }}</div>
                    <div class="text-[11px] font-mono text-slate-600 mt-1 font-semibold">{{ $tank->volume_liters }} Liters</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-2">
                <a href="{{ route('tanks.print-label', $tank) }}?print=1" target="_blank"
                   @click="qrModalOpen = false"
                   class="w-full sm:w-auto px-5 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Print QR Sticker Now</span>
                </a>
                <button type="button" @click="qrModalOpen = false" class="w-full sm:w-auto px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                    Continue to Tank Hub
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
