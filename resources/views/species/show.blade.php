<x-app-layout title="{{ $species->name }}">
    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <a href="{{ route('species.index') }}" class="hover:text-cyan-400 transition">Species</a>
                <span>/</span>
                <span class="text-white font-medium">{{ $species->name }}</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('species.edit', $species) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium rounded-lg transition">
                    <i data-lucide="pencil" class="w-4 h-4 text-cyan-400"></i>
                    Edit Species
                </a>
                <a href="{{ route('livestock.create', ['species_id' => $species->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold text-sm rounded-lg transition">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Livestock
                </a>
            </div>
        </div>

        <!-- Overview Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-white tracking-tight">{{ $species->name }}</h1>
                        @if($species->active)
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">ACTIVE</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700">INACTIVE</span>
                        @endif
                    </div>
                    @if($species->scientific_name)
                        <div class="text-sm font-mono italic text-cyan-400 mt-1">{{ $species->scientific_name }}</div>
                    @endif
                    @if($species->description)
                        <div class="text-sm text-slate-300 mt-3 max-w-3xl leading-relaxed whitespace-pre-line">{{ $species->description }}</div>
                    @else
                        <div class="text-sm text-slate-500 italic mt-2">No specific notes recorded for this species.</div>
                    @endif
                </div>

                <!-- Fast Stats -->
                <div class="grid grid-cols-3 gap-3 min-w-[280px]">
                    <div class="bg-slate-950/80 border border-slate-800/80 rounded-lg p-3 text-center">
                        <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Livestock</div>
                        <div class="text-xl font-bold text-cyan-400 mt-0.5">{{ $species->livestock_count }}</div>
                    </div>
                    <div class="bg-slate-950/80 border border-slate-800/80 rounded-lg p-3 text-center">
                        <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Batches</div>
                        <div class="text-xl font-bold text-blue-400 mt-0.5">{{ $species->offspring_batches_count }}</div>
                    </div>
                    <div class="bg-slate-950/80 border border-slate-800/80 rounded-lg p-3 text-center">
                        <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Pairs</div>
                        <div class="text-xl font-bold text-indigo-400 mt-0.5">{{ $species->breeding_events_count }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linked Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Livestock -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <i data-lucide="fish" class="w-4 h-4 text-cyan-400"></i>
                        Recent Livestock ({{ $species->name }})
                    </h2>
                    <a href="{{ route('livestock.index', ['species_id' => $species->id]) }}" class="text-xs text-cyan-400 hover:text-cyan-300">View All &rarr;</a>
                </div>
                @if($livestock->isEmpty())
                    <p class="text-sm text-slate-500 italic py-6 text-center">No individual livestock registered for this species yet.</p>
                @else
                    <div class="divide-y divide-slate-800/60">
                        @foreach($livestock as $item)
                            <div class="py-2.5 flex items-center justify-between hover:bg-slate-800/30 px-2 rounded transition">
                                <div class="flex items-center gap-3">
                                    <div class="font-mono text-sm font-bold text-cyan-400">
                                        <a href="{{ route('livestock.show', $item) }}" class="hover:underline">{{ $item->livestock_code }}</a>
                                    </div>
                                    <div>
                                        <div class="text-sm text-slate-200 font-medium">{{ $item->variety ?: $species->name }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ $item->sex }} &bull; Tank: {{ $item->tank ? $item->tank->tank_code : 'Unassigned' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-800 text-slate-300">
                                    {{ $item->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Active Batches -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-blue-400"></i>
                        Recent Batches ({{ $species->name }})
                    </h2>
                    <a href="{{ route('batches.index', ['species_id' => $species->id]) }}" class="text-xs text-blue-400 hover:text-blue-300">View All &rarr;</a>
                </div>
                @if($batches->isEmpty())
                    <p class="text-sm text-slate-500 italic py-6 text-center">No offspring batches recorded for this species yet.</p>
                @else
                    <div class="divide-y divide-slate-800/60">
                        @foreach($batches as $batch)
                            <div class="py-2.5 flex items-center justify-between hover:bg-slate-800/30 px-2 rounded transition">
                                <div class="flex items-center gap-3">
                                    <div class="font-mono text-sm font-bold text-blue-400">
                                        <a href="{{ route('batches.show', $batch) }}" class="hover:underline">{{ $batch->batch_code }}</a>
                                    </div>
                                    <div>
                                        <div class="text-sm text-slate-200 font-medium">{{ $batch->variety ?: 'Batch' }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            Count: {{ $batch->current_count }} &bull; Tank: {{ $batch->tank ? $batch->tank->tank_code : 'Unassigned' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-950 text-blue-400 border border-blue-800/40">
                                    {{ $batch->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
