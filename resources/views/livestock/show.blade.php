<x-app-layout title="{{ $livestock->livestock_code }} - {{ $livestock->variety ?: $livestock->species?->name }}">
    <div class="space-y-6" x-data="{ reassignModal: false, statusModal: false }">
        <!-- Breadcrumb & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <a href="{{ route('livestock.index') }}" class="hover:text-cyan-400 transition">Livestock</a>
                <span>/</span>
                <span class="font-mono text-cyan-400 font-bold">{{ $livestock->livestock_code }}</span>
                <span>/</span>
                <span class="text-white">{{ $livestock->variety ?: $livestock->species?->name }}</span>
            </div>
            <div class="flex items-center gap-2.5">
                <button @click="reassignModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">
                    <i data-lucide="shuffle" class="w-3.5 h-3.5 text-cyan-400"></i>
                    Move Tank
                </button>
                <button @click="statusModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">
                    <i data-lucide="tag" class="w-3.5 h-3.5 text-amber-400"></i>
                    Status
                </button>
                <a href="{{ route('livestock.edit', $livestock) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold rounded-lg shadow-md shadow-cyan-950/40 transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    Edit Specimen
                </a>
            </div>
        </div>

        <!-- Main Specimen Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl overflow-hidden shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <!-- Left: Photograph / Avatar -->
                <div class="md:col-span-1 flex flex-col items-center">
                    <div class="w-full aspect-square max-w-[280px] rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden shadow-inner relative group">
                        @if($livestock->photo_url)
                            <img src="{{ $livestock->photo_url }}" alt="{{ $livestock->livestock_code }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center text-slate-600 p-4">
                                <i data-lucide="fish" class="w-16 h-16 mx-auto mb-2 text-slate-700"></i>
                                <span class="text-xs text-slate-500">No Photo Uploaded</span>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-950/80 text-cyan-400 border border-slate-700/80 backdrop-blur-sm">
                                {{ $livestock->livestock_code }}
                            </span>
                        </div>
                    </div>

                    @if($livestock->grade)
                        <div class="mt-4 w-full max-w-[280px] p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-center">
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 block font-semibold">Quality Grade</span>
                            <span class="text-sm font-bold mt-0.5 inline-block
                                {{ $livestock->grade === 'SHOW' ? 'text-amber-400' :
                                  ($livestock->grade === 'BREEDER' ? 'text-cyan-400' :
                                  ($livestock->grade === 'MATERIAL' ? 'text-indigo-400' : 'text-rose-400')) }}">
                                {{ $livestock->grade }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Right: Vital Information & Husbandry -->
                <div class="md:col-span-2 space-y-5">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-white tracking-tight">
                                {{ $livestock->variety ?: $livestock->species?->name }}
                            </h1>
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold border
                                {{ $livestock->status === 'BREEDER' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' :
                                  ($livestock->status === 'AVAILABLE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' :
                                  ($livestock->status === 'QUARANTINE' ? 'bg-amber-950/60 text-amber-400 border-amber-800/50' :
                                  ($livestock->status === 'SICK' ? 'bg-rose-950/60 text-rose-400 border-rose-800/50' :
                                  ($livestock->status === 'SOLD' ? 'bg-indigo-950/60 text-indigo-400 border-indigo-800/50' : 'bg-slate-800 text-slate-300 border-slate-700')))) }}">
                                {{ $livestock->status }}
                            </span>
                        </div>
                        <div class="text-sm text-cyan-400/90 font-medium mt-0.5">
                            Species: <a href="{{ route('species.show', $livestock->species) }}" class="hover:underline">{{ $livestock->species?->name }}</a>
                            @if($livestock->species?->scientific_name)
                                <span class="italic text-slate-500 font-mono text-xs">({{ $livestock->species->scientific_name }})</span>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Sex</span>
                            <span class="text-sm font-semibold mt-0.5 block
                                {{ $livestock->sex === 'MALE' ? 'text-blue-400' : ($livestock->sex === 'FEMALE' ? 'text-rose-400' : 'text-slate-300') }}">
                                {{ $livestock->sex === 'MALE' ? '? Male' : ($livestock->sex === 'FEMALE' ? '? Female' : 'Unknown') }}
                            </span>
                        </div>

                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Current Tank</span>
                            @if($livestock->tank)
                                <a href="{{ route('tanks.show', $livestock->tank) }}" class="text-sm font-mono font-bold text-cyan-400 hover:underline mt-0.5 block">
                                    {{ $livestock->tank->tank_code }}
                                </a>
                            @else
                                <span class="text-sm text-slate-500 mt-0.5 block">Unassigned</span>
                            @endif
                        </div>

                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Acquired / Age</span>
                            <span class="text-xs text-slate-300 mt-1 block">
                                {{ $livestock->date_acquired ? $livestock->date_acquired->format('M d, Y') : ($livestock->date_of_birth ? $livestock->date_of_birth->format('M d, Y') : 'Unknown') }}
                            </span>
                        </div>

                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Purchase / Value</span>
                            <span class="text-sm font-mono font-bold text-emerald-400 mt-0.5 block">
                                ₱{{ number_format($livestock->purchase_price, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Lineage & Source -->
                    <div class="p-3.5 rounded-lg bg-slate-950/40 border border-slate-800/80 space-y-1">
                        <div class="text-xs text-slate-400">
                            <strong class="text-slate-200">Source:</strong> {{ $livestock->source ?: 'Homebred / Farm stock' }}
                        </div>
                        @if($livestock->notes)
                            <div class="text-xs text-slate-300 leading-relaxed whitespace-pre-line mt-2 pt-2 border-t border-slate-800/60">
                                {{ $livestock->notes }}
                            </div>
                        @endif
                    </div>

                    <!-- Grading Trait Breakdown (Section 13) -->
                    @if(!empty($livestock->grading_scores))
                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800/80 space-y-3">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                                <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                Trait Grading Breakdown (1 - 5 Scale)
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach(['body' => 'Body Form', 'color' => 'Color / Luster', 'tail' => 'Tail / Caudal', 'dorsal' => 'Dorsal Fin', 'pattern' => 'Pattern', 'overall' => 'Overall'] as $traitKey => $traitLabel)
                                    @php $score = $livestock->grading_scores[$traitKey] ?? null; @endphp
                                    @if($score !== null)
                                        <div class="p-2 rounded bg-slate-900 border border-slate-800">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-slate-400">{{ $traitLabel }}</span>
                                                <span class="font-bold text-cyan-400 font-mono">{{ $score }}/5</span>
                                            </div>
                                            <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-cyan-400 h-1.5 rounded-full" style="width: {{ ($score / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Breeding & Feeding History Tabs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Breeding Lineage & Records -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <i data-lucide="git-merge" class="w-4 h-4 text-cyan-400"></i>
                        Breeding Pairings & History
                    </h2>
                    <a href="{{ route('breeding.create', [$livestock->sex === 'MALE' ? 'male_id' : 'female_id' => $livestock->id]) }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        New Pairing
                    </a>
                </div>

                @php
                    $allBreedings = $livestock->maleBreedingEvents->merge($livestock->femaleBreedingEvents)->sortByDesc('start_date');
                @endphp

                @if($allBreedings->isEmpty())
                    <p class="text-xs text-slate-500 italic py-6 text-center">This specimen has not been assigned to any breeding events yet.</p>
                @else
                    <div class="divide-y divide-slate-800/60">
                        @foreach($allBreedings as $event)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('breeding.show', $event) }}" class="font-mono text-xs font-bold text-cyan-400 hover:underline">
                                            {{ $event->breeding_code }}
                                        </a>
                                        <span class="text-xs text-slate-300">
                                            Pair: {{ $event->maleLivestock?->livestock_code ?? '?' }} &times; {{ $event->femaleLivestock?->livestock_code ?? '?' }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Started {{ $event->start_date ? $event->start_date->format('M d, Y') : 'Unknown' }} &bull; Tank {{ $event->tank?->tank_code ?? '?' }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                                    {{ $event->status === 'ACTIVE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/40' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                                    {{ $event->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Feeding History -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <i data-lucide="utensils" class="w-4 h-4 text-indigo-400"></i>
                        Recent Feeding Logs
                    </h2>
                    <a href="{{ route('feeding.create', ['livestock_id' => $livestock->id]) }}" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Log Feeding
                    </a>
                </div>

                @if($livestock->feedingLogs->isEmpty())
                    <p class="text-xs text-slate-500 italic py-6 text-center">No specific feeding logs recorded for this individual.</p>
                @else
                    <div class="divide-y divide-slate-800/60">
                        @foreach($livestock->feedingLogs->take(5) as $fLog)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-medium text-slate-200">{{ $fLog->food }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $fLog->fed_at?->format('M d, Y H:i') }}</div>
                                </div>
                                @if($fLog->quantity)
                                    <span class="text-xs font-mono text-slate-400">{{ $fLog->quantity }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- REASSIGN TANK MODAL -->
        <div x-show="reassignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="reassignModal = false" class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="shuffle" class="w-5 h-5 text-cyan-400"></i>
                    Reassign Tank: {{ $livestock->livestock_code }}
                </h3>
                <p class="text-xs text-slate-400">Select the destination aquarium, tub, or growout vat for this animal.</p>

                <form action="{{ route('livestock.update', $livestock) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <!-- Preserve required attributes -->
                    <input type="hidden" name="livestock_code" value="{{ $livestock->livestock_code }}">
                    <input type="hidden" name="species_id" value="{{ $livestock->species_id }}">
                    <input type="hidden" name="sex" value="{{ $livestock->sex }}">
                    <input type="hidden" name="status" value="{{ $livestock->status }}">
                    <input type="hidden" name="grade" value="{{ $livestock->grade }}">
                    <input type="hidden" name="variety" value="{{ $livestock->variety }}">

                    <div>
                        <label for="modal_tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Destination Tank</label>
                        <select name="tank_id" id="modal_tank_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="">No Tank (Unassigned)</option>
                            @foreach(\App\Models\Tank::orderBy('tank_code')->get() as $tk)
                                <option value="{{ $tk->id }}" {{ $livestock->tank_id == $tk->id ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} &bull; {{ $tk->name }} ({{ $tk->volume_liters }}L - {{ $tk->purpose }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="reassignModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold rounded-lg shadow">Move Specimen</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CHANGE STATUS MODAL (Section 35) -->
        <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="statusModal = false" class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5 text-amber-400"></i>
                    Update Specimen Status
                </h3>
                <p class="text-xs text-slate-400">Keep your livestock inventory accurate without deleting historical records.</p>

                <form action="{{ route('livestock.update', $livestock) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <!-- Preserve required attributes -->
                    <input type="hidden" name="livestock_code" value="{{ $livestock->livestock_code }}">
                    <input type="hidden" name="species_id" value="{{ $livestock->species_id }}">
                    <input type="hidden" name="sex" value="{{ $livestock->sex }}">
                    <input type="hidden" name="tank_id" value="{{ $livestock->tank_id }}">
                    <input type="hidden" name="grade" value="{{ $livestock->grade }}">
                    <input type="hidden" name="variety" value="{{ $livestock->variety }}">

                    <div>
                        <label for="modal_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">New Status</label>
                        <select name="status" id="modal_status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            @foreach(['BREEDER', 'GROWOUT', 'DISPLAY', 'QUARANTINE', 'SICK', 'AVAILABLE', 'SOLD', 'DECEASED', 'CULLED'] as $st)
                                <option value="{{ $st }}" {{ $livestock->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="statusModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold rounded-lg shadow">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
