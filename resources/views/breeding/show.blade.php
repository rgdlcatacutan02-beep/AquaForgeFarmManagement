<x-app-layout title="{{ $breeding->breeding_code }} - Breeding Hub">
    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <a href="{{ route('breeding.index') }}" class="hover:text-pink-400 transition">Breeding</a>
                <span>/</span>
                <span class="font-mono text-pink-400 font-bold">{{ $breeding->breeding_code }}</span>
                <span>/</span>
                <span class="text-white">{{ $breeding->species?->name }}</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('batches.create', ['breeding_id' => $breeding->id, 'species_id' => $breeding->species_id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-400 hover:to-cyan-500 text-slate-950 font-bold text-xs rounded-lg shadow-md transition">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Register Offspring Batch
                </a>
                <a href="{{ route('breeding.edit', $breeding) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5 text-pink-400"></i>
                    Edit Pairing
                </a>
            </div>
        </div>

        <!-- Main Pairing Overview Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-2xl font-bold text-pink-400">{{ $breeding->breeding_code }}</span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold border
                            {{ $breeding->status === 'ACTIVE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' :
                              ($breeding->status === 'PLANNED' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' :
                              ($breeding->status === 'COMPLETED' ? 'bg-blue-950/60 text-blue-400 border-blue-800/50' :
                              ($breeding->status === 'FAILED' ? 'bg-rose-950/60 text-rose-400 border-rose-800/50' : 'bg-slate-800 text-slate-400 border-slate-700'))) }}">
                            {{ $breeding->status }}
                        </span>
                    </div>
                    <div class="text-sm text-slate-400 mt-1">
                        Species: <strong class="text-white">{{ $breeding->species?->name }}</strong> &bull;
                        Tank:
                        @if($breeding->tank)
                            <a href="{{ route('tanks.show', $breeding->tank) }}" class="font-mono text-cyan-400 hover:underline">
                                {{ $breeding->tank->tank_code }} ({{ $breeding->tank->name }})
                            </a>
                        @else
                            <span class="text-slate-500 italic">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-center">
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Pairing Date</span>
                        <span class="text-xs font-bold text-slate-200 mt-0.5 block">{{ $breeding->start_date ? $breeding->start_date->format('M d, Y') : '?' }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-center">
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Expected Hatch</span>
                        <span class="text-xs font-bold text-amber-400 mt-0.5 block">{{ $breeding->expected_date ? $breeding->expected_date->format('M d, Y') : '?' }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-center col-span-2 sm:col-span-1">
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Actual Birth</span>
                        <span class="text-xs font-bold text-emerald-400 mt-0.5 block">{{ $breeding->actual_birth_or_hatch_date ? $breeding->actual_birth_or_hatch_date->format('M d, Y') : 'Pending' }}</span>
                    </div>
                </div>
            </div>

            <!-- Parents Showcase Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                <!-- Male Parent -->
                <div class="p-4 rounded-xl bg-slate-950/70 border border-blue-900/40 relative">
                    <div class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>?</span> Sire (Male Parent)
                        </span>
                        @if($breeding->maleLivestock)
                            <a href="{{ route('livestock.show', $breeding->maleLivestock) }}" class="text-[11px] text-cyan-400 hover:underline">View Profile &rarr;</a>
                        @endif
                    </div>
                    @if($breeding->maleLivestock)
                        <div class="flex items-center gap-4">
                            @if($breeding->maleLivestock->photo_url)
                                <img src="{{ $breeding->maleLivestock->photo_url }}" class="w-16 h-16 rounded-lg object-cover border border-slate-700">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 font-bold">
                                    ?
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('livestock.show', $breeding->maleLivestock) }}" class="font-mono text-base font-bold text-blue-400 hover:underline">
                                    {{ $breeding->maleLivestock->livestock_code }}
                                </a>
                                <div class="text-sm font-semibold text-white">{{ $breeding->maleLivestock->variety ?: 'Standard Variety' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Grade: <strong class="text-cyan-300">{{ $breeding->maleLivestock->grade ?: 'Unrated' }}</strong> &bull;
                                    Home Tank: {{ $breeding->maleLivestock->tank?->tank_code ?? 'Unassigned' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-500 text-xs italic">
                            No specific male individual recorded (e.g. Colony breeding or external sire).
                        </div>
                    @endif
                </div>

                <!-- Female Parent -->
                <div class="p-4 rounded-xl bg-slate-950/70 border border-rose-900/40 relative">
                    <div class="text-xs font-bold text-rose-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>?</span> Dam (Female Parent)
                        </span>
                        @if($breeding->femaleLivestock)
                            <a href="{{ route('livestock.show', $breeding->femaleLivestock) }}" class="text-[11px] text-pink-400 hover:underline">View Profile &rarr;</a>
                        @endif
                    </div>
                    @if($breeding->femaleLivestock)
                        <div class="flex items-center gap-4">
                            @if($breeding->femaleLivestock->photo_url)
                                <img src="{{ $breeding->femaleLivestock->photo_url }}" class="w-16 h-16 rounded-lg object-cover border border-slate-700">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 font-bold">
                                    ?
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('livestock.show', $breeding->femaleLivestock) }}" class="font-mono text-base font-bold text-rose-400 hover:underline">
                                    {{ $breeding->femaleLivestock->livestock_code }}
                                </a>
                                <div class="text-sm font-semibold text-white">{{ $breeding->femaleLivestock->variety ?: 'Standard Variety' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Grade: <strong class="text-pink-300">{{ $breeding->femaleLivestock->grade ?: 'Unrated' }}</strong> &bull;
                                    Home Tank: {{ $breeding->femaleLivestock->tank?->tank_code ?? 'Unassigned' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-500 text-xs italic">
                            No specific female individual recorded (e.g. Colony breeding or flock spawn).
                        </div>
                    @endif
                </div>
            </div>

            @if($breeding->notes)
                <div class="mt-5 p-3.5 rounded-lg bg-slate-950/40 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line">
                    <strong class="text-slate-400">Notes:</strong> {{ $breeding->notes }}
                </div>
            @endif
        </div>

        <!-- Produced Offspring Batches Section -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="layers" class="w-5 h-5 text-blue-400"></i>
                        Produced Offspring Batches
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Fry or juvenile batches spawned from this pairing.</p>
                </div>
                <a href="{{ route('batches.create', ['breeding_id' => $breeding->id, 'species_id' => $breeding->species_id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-lg transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Register Batch
                </a>
            </div>

            @if($breeding->offspringBatches->isEmpty())
                <div class="py-10 text-center text-slate-500 border border-dashed border-slate-800 rounded-xl">
                    <i data-lucide="layers" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                    <p class="text-sm font-medium text-slate-400">No offspring batches registered yet</p>
                    <p class="text-xs text-slate-500 mt-1">Once fry or juveniles appear, record a batch to start tracking survival and population.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/70 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Batch ID</th>
                                <th class="py-3 px-4">Variety</th>
                                <th class="py-3 px-4 text-center">Birth / Hatch</th>
                                <th class="py-3 px-4 text-center">Initial</th>
                                <th class="py-3 px-4 text-center">Current</th>
                                <th class="py-3 px-4 text-center">Survival</th>
                                <th class="py-3 px-4">Tank</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($breeding->offspringBatches as $batch)
                                @php
                                    $survivalRate = $batch->initial_count > 0 ? round(($batch->current_count / $batch->initial_count) * 100) : 100;
                                @endphp
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-400">
                                        <a href="{{ route('batches.show', $batch) }}" class="hover:underline">
                                            {{ $batch->batch_code }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 font-medium text-white">{{ $batch->variety ?: $breeding->species?->name }}</td>
                                    <td class="py-3.5 px-4 text-center text-xs text-slate-400">
                                        {{ $batch->birth_or_hatch_date ? $batch->birth_or_hatch_date->format('M d, Y') : '?' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-400">{{ $batch->initial_count }}</td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-white">{{ $batch->current_count }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold border
                                            {{ $survivalRate >= 80 ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/40' :
                                              ($survivalRate >= 50 ? 'bg-amber-950/60 text-amber-400 border-amber-800/40' : 'bg-rose-950/60 text-rose-400 border-rose-800/40') }}">
                                            {{ $survivalRate }}%
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs">
                                        {{ $batch->tank?->tank_code ?? 'Unassigned' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300">
                                            {{ $batch->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('batches.show', $batch) }}" class="inline-flex items-center gap-1 text-xs text-blue-400 hover:text-blue-300 font-semibold">
                                            <span>Manage</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
