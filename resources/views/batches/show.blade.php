<x-app-layout title="{{ $batch->batch_code }} - Batch Hub">
    <div class="space-y-6" x-data="{ mortalityModal: false, cullingModal: false, moveModal: false }">
        <!-- Breadcrumb & Actions Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <a href="{{ route('batches.index') }}" class="hover:text-blue-400 transition">Batches</a>
                <span>/</span>
                <span class="font-mono text-blue-400 font-bold">{{ $batch->batch_code }}</span>
                <span>/</span>
                <span class="text-white">{{ $batch->variety ?: $batch->species?->name }}</span>
            </div>
            <div class="flex items-center gap-2.5">
                <button @click="mortalityModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-950/70 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 text-xs font-bold rounded-lg transition">
                    <i data-lucide="skull" class="w-3.5 h-3.5 text-rose-400"></i>
                    Record Mortality
                </button>
                <button @click="cullingModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-950/70 hover:bg-amber-900/80 text-amber-300 border border-amber-800/60 text-xs font-bold rounded-lg transition">
                    <i data-lucide="scissors" class="w-3.5 h-3.5 text-amber-400"></i>
                    Record Culling
                </button>
                <button @click="moveModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">
                    <i data-lucide="shuffle" class="w-3.5 h-3.5 text-cyan-400"></i>
                    Move Tank
                </button>
                <a href="{{ route('batches.edit', $batch) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    Edit
                </a>
            </div>
        </div>

        <!-- Population KPI Dashboard Cards -->
        @php
            $survivalRate = $batch->initial_count > 0 ? round(($batch->current_count / $batch->initial_count) * 100) : 100;
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Current Population -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-blue-900/40 shadow-lg">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold block">Current Headcount</span>
                <div class="text-2xl font-bold font-mono text-white mt-1">{{ $batch->current_count }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Active in tank</div>
            </div>

            <!-- Initial Hatch -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 shadow-lg">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold block">Initial Count</span>
                <div class="text-2xl font-bold font-mono text-slate-300 mt-1">{{ $batch->initial_count }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">At birth/hatch</div>
            </div>

            <!-- Survival Rate -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 shadow-lg">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold block">Survival Rate</span>
                <div class="text-2xl font-bold font-mono mt-1
                    {{ $survivalRate >= 80 ? 'text-emerald-400' : ($survivalRate >= 50 ? 'text-amber-400' : 'text-rose-400') }}">
                    {{ $survivalRate }}%
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5">Post-hatch rate</div>
            </div>

            <!-- Deaths -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-rose-950/40 shadow-lg">
                <span class="text-[10px] uppercase tracking-wider text-rose-400 font-semibold block">Mortality Losses</span>
                <div class="text-2xl font-bold font-mono text-rose-400 mt-1">{{ $batch->death_count }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Natural/disease</div>
            </div>

            <!-- Culls -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-amber-950/40 shadow-lg">
                <span class="text-[10px] uppercase tracking-wider text-amber-400 font-semibold block">Culled Out</span>
                <div class="text-2xl font-bold font-mono text-amber-400 mt-1">{{ $batch->cull_count }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Defects/rejects</div>
            </div>

            <!-- Available for Sale -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-emerald-950/40 shadow-lg">
                <span class="text-[10px] uppercase tracking-wider text-emerald-400 font-semibold block">Available Stock</span>
                <div class="text-2xl font-bold font-mono text-emerald-400 mt-1">{{ $batch->available_count }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Commercial count</div>
            </div>
        </div>

        <!-- Overview Details & Lineage Parents -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Batch Information -->
            <div class="lg:col-span-2 bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-white tracking-tight">
                                {{ $batch->variety ?: $batch->species?->name }}
                            </h1>
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold border
                                {{ $batch->status === 'READY_FOR_SALE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/40' :
                                  ($batch->status === 'READY_FOR_GRADING' ? 'bg-amber-950/60 text-amber-400 border-amber-800/40' :
                                  ($batch->status === 'SOLD_OUT' ? 'bg-slate-800 text-slate-400 border-slate-700' : 'bg-blue-950/60 text-blue-400 border-blue-800/40')) }}">
                                {{ $batch->status }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-400 mt-1">
                            Species: <strong class="text-slate-200">{{ $batch->species?->name }}</strong> &bull;
                            Birth/Hatch: <span class="text-slate-200">{{ $batch->birth_or_hatch_date ? $batch->birth_or_hatch_date->format('M d, Y') : 'Unknown' }}</span>
                        </div>
                    </div>

                    @if($batch->grade)
                        <div class="px-3 py-1 rounded-lg bg-slate-950 border border-slate-800 text-right">
                            <span class="text-[9px] uppercase tracking-wider text-slate-500 block">Grade</span>
                            <span class="text-xs font-bold text-cyan-400">{{ $batch->grade }}</span>
                        </div>
                    @endif
                </div>

                <!-- Growout Tank Card -->
                <div class="p-3.5 rounded-lg bg-slate-950/70 border border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-cyan-950/50 border border-cyan-800/40 flex items-center justify-center text-cyan-400">
                            <i data-lucide="box" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500 uppercase font-semibold">Current Growout Tank</div>
                            @if($batch->tank)
                                <a href="{{ route('tanks.show', $batch->tank) }}" class="font-mono text-sm font-bold text-cyan-400 hover:underline">
                                    {{ $batch->tank->tank_code }} - {{ $batch->tank->name }}
                                </a>
                                <span class="text-xs text-slate-400">({{ $batch->tank->volume_liters }}L &bull; {{ $batch->tank->location ?? 'Rack' }})</span>
                            @else
                                <span class="text-xs text-slate-500 italic">No tank assigned</span>
                            @endif
                        </div>
                    </div>
                    <button @click="moveModal = true" class="text-xs text-cyan-400 hover:underline">Reassign &rarr;</button>
                </div>

                @if($batch->notes)
                    <div class="p-3.5 rounded-lg bg-slate-950/40 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line">
                        <strong class="text-slate-400">Notes:</strong> {{ $batch->notes }}
                    </div>
                @endif
            </div>

            <!-- Right: Parentage Lineage Card -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="git-commit" class="w-4 h-4 text-pink-400"></i>
                        Parent Lineage
                    </h2>
                    @if($batch->breedingEvent)
                        <a href="{{ route('breeding.show', $batch->breedingEvent) }}" class="text-xs text-pink-400 hover:underline font-mono">
                            {{ $batch->breedingEvent->breeding_code }} &rarr;
                        </a>
                    @endif
                </div>

                @if($batch->breedingEvent)
                    <!-- Male Parent -->
                    <div class="p-3 rounded-lg bg-slate-950/60 border border-blue-900/40">
                        <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <span>?</span> Sire (Father)
                        </div>
                        @if($batch->breedingEvent->maleLivestock)
                            <div class="flex items-center justify-between">
                                <a href="{{ route('livestock.show', $batch->breedingEvent->maleLivestock) }}" class="font-mono text-xs font-bold text-white hover:text-blue-400">
                                    {{ $batch->breedingEvent->maleLivestock->livestock_code }}
                                </a>
                                <span class="text-xs text-slate-300 font-medium">{{ $batch->breedingEvent->maleLivestock->variety ?: 'Standard' }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-500 italic">Colony / Unknown</span>
                        @endif
                    </div>

                    <!-- Female Parent -->
                    <div class="p-3 rounded-lg bg-slate-950/60 border border-rose-900/40">
                        <div class="text-[10px] font-bold text-rose-400 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <span>?</span> Dam (Mother)
                        </div>
                        @if($batch->breedingEvent->femaleLivestock)
                            <div class="flex items-center justify-between">
                                <a href="{{ route('livestock.show', $batch->breedingEvent->femaleLivestock) }}" class="font-mono text-xs font-bold text-white hover:text-rose-400">
                                    {{ $batch->breedingEvent->femaleLivestock->livestock_code }}
                                </a>
                                <span class="text-xs text-slate-300 font-medium">{{ $batch->breedingEvent->femaleLivestock->variety ?: 'Standard' }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-500 italic">Colony / Unknown</span>
                        @endif
                    </div>
                @else
                    <div class="py-6 text-center text-slate-500 text-xs italic">
                        Direct batch entry with no linked parental pairing record.
                    </div>
                @endif
            </div>
        </div>

        <!-- Mortality & Culling Audit Trail (Section 22) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-cyan-400"></i>
                        Mortality & Culling Log History
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Historical population adjustments and attrition records.</p>
                </div>
            </div>

            @if($batch->batchLogs->isEmpty())
                <div class="py-8 text-center text-slate-500 border border-dashed border-slate-800 rounded-xl">
                    <p class="text-xs text-slate-400">No losses or culls recorded for this batch.</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Full initial cohort ({{ $batch->initial_count }} specimens) remains intact.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/70 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                            <tr>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4">Type</th>
                                <th class="py-2.5 px-4 text-center">Quantity</th>
                                <th class="py-2.5 px-4">Reason</th>
                                <th class="py-2.5 px-4">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($batch->batchLogs as $log)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-xs font-mono text-slate-300">
                                        {{ $log->log_date ? $log->log_date->format('M d, Y') : '?' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                                            {{ $log->type === 'MORTALITY' ? 'bg-rose-950/70 text-rose-400 border-rose-800/40' : 'bg-amber-950/70 text-amber-400 border-amber-800/40' }}">
                                            {{ $log->type }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold
                                        {{ $log->type === 'MORTALITY' ? 'text-rose-400' : 'text-amber-400' }}">
                                        -{{ $log->quantity }}
                                    </td>
                                    <td class="py-3 px-4 text-xs text-slate-200 font-medium">
                                        {{ $log->reason ?: 'Unspecified' }}
                                    </td>
                                    <td class="py-3 px-4 text-xs text-slate-400">
                                        {{ $log->notes ?: '?' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- RECORD MORTALITY MODAL -->
        <div x-show="mortalityModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="mortalityModal = false" class="bg-slate-900 border border-rose-900/60 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="skull" class="w-5 h-5 text-rose-400"></i>
                    Record Offspring Mortality
                </h3>
                <p class="text-xs text-slate-400">Log natural fry deaths or disease losses. Current headcount: <strong class="text-white">{{ $batch->current_count }}</strong>.</p>

                <form action="{{ route('batches.record-log', $batch) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="MORTALITY">

                    <div>
                        <label for="mort_qty" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                            Number of Casualties <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" min="1" max="{{ $batch->current_count }}" name="quantity" id="mort_qty" required placeholder="e.g. 2" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white font-mono font-bold focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="mort_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                            Date of Loss <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" name="log_date" id="mort_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="mort_reason" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Cause / Observed Reason</label>
                        <input type="text" name="reason" id="mort_reason" placeholder="e.g. Failure to thrive, water temperature drop, fungal" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="mort_notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Notes</label>
                        <textarea name="notes" id="mort_notes" rows="2" placeholder="Husbandry adjustments made..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-rose-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="mortalityModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-lg shadow">Confirm Mortality</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RECORD CULLING MODAL -->
        <div x-show="cullingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="cullingModal = false" class="bg-slate-900 border border-amber-900/60 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="scissors" class="w-5 h-5 text-amber-400"></i>
                    Record Offspring Culling
                </h3>
                <p class="text-xs text-slate-400">Log culled specimens removed from the gene pool. Current headcount: <strong class="text-white">{{ $batch->current_count }}</strong>.</p>

                <form action="{{ route('batches.record-log', $batch) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="CULLING">

                    <div>
                        <label for="cull_qty" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                            Number Culled <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" min="1" max="{{ $batch->current_count }}" name="quantity" id="cull_qty" required placeholder="e.g. 3" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label for="cull_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                            Date Culled <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" name="log_date" id="cull_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label for="cull_reason" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Cull Selection Reason</label>
                        <input type="text" name="reason" id="cull_reason" placeholder="e.g. Bent spine, misshapen tail, stunted growth, off-color" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                    </div>

                    <div>
                        <label for="cull_notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Destination / Notes</label>
                        <textarea name="notes" id="cull_notes" rows="2" placeholder="e.g. Feeder tank, humane euthanasia..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="cullingModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold rounded-lg shadow">Confirm Culling</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- REASSIGN TANK MODAL -->
        <div x-show="moveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="moveModal = false" class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="shuffle" class="w-5 h-5 text-cyan-400"></i>
                    Move Batch to Another Tank
                </h3>
                <p class="text-xs text-slate-400">Transfer this fry or growout cohort into a larger tank or sales aquarium.</p>

                <form action="{{ route('batches.update', $batch) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <!-- Preserved required fields -->
                    <input type="hidden" name="batch_code" value="{{ $batch->batch_code }}">
                    <input type="hidden" name="species_id" value="{{ $batch->species_id }}">
                    <input type="hidden" name="breeding_event_id" value="{{ $batch->breeding_event_id }}">
                    <input type="hidden" name="variety" value="{{ $batch->variety }}">
                    <input type="hidden" name="birth_or_hatch_date" value="{{ $batch->birth_or_hatch_date?->format('Y-m-d') }}">
                    <input type="hidden" name="status" value="{{ $batch->status }}">
                    <input type="hidden" name="grade" value="{{ $batch->grade }}">

                    <div>
                        <label for="move_tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Destination Growout Tank</label>
                        <select name="tank_id" id="move_tank_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="">No Tank (Unassigned)</option>
                            @foreach(\App\Models\Tank::orderBy('tank_code')->get() as $tk)
                                <option value="{{ $tk->id }}" {{ $batch->tank_id == $tk->id ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} &bull; {{ $tk->name }} ({{ $tk->volume_liters }}L - {{ $tk->purpose }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="moveModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold rounded-lg shadow">Transfer Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
