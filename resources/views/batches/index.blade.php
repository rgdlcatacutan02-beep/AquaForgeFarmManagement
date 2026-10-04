<x-app-layout title="Offspring Batches">
    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-6 h-6 text-blue-400"></i>
                    Offspring Batches
                </h1>
                <p class="text-sm text-slate-400 mt-1">Track fry, juvenile hatches, population mortality, and culling metrics.</p>
            </div>
            <div>
                <a href="{{ route('batches.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-400 hover:to-cyan-500 text-slate-950 font-semibold rounded-lg shadow-lg shadow-blue-950/40 text-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Register New Batch
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 backdrop-blur-sm">
            <form method="GET" action="{{ route('batches.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2 relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-3"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search batch code, variety, notes..." class="w-full bg-slate-950/80 border border-slate-800 rounded-lg pl-9 pr-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <div>
                    <select name="species_id" onchange="this.form.submit()" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-blue-500">
                        <option value="">All Species</option>
                        @foreach($species as $sp)
                            <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <select name="status" onchange="this.form.submit()" class="flex-1 bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-blue-500">
                        <option value="">All Statuses</option>
                        @foreach(['GROWING', 'READY_FOR_GRADING', 'READY_FOR_SALE', 'SOLD_OUT', 'COMPLETED'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['search', 'species_id', 'status', 'tank_id']))
                        <a href="{{ route('batches.index') }}" class="p-2 bg-slate-800/40 hover:bg-slate-800 text-slate-400 text-xs rounded-lg flex items-center justify-center transition" title="Clear Filters">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Batches Table View -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl overflow-hidden shadow-xl">
            @if ($batches->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-slate-800/80 flex items-center justify-center text-slate-500 mb-4">
                        <i data-lucide="layers" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-base font-semibold text-slate-300">No offspring batches registered</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">Log your first fry drops, spawnings, or juvenile cohorts to monitor growth and losses.</p>
                    <div class="mt-6">
                        <a href="{{ route('batches.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg text-sm transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Register First Batch
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/70 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Batch Code</th>
                                <th class="py-3 px-4">Species & Variety</th>
                                <th class="py-3 px-4">Parents (Pairing)</th>
                                <th class="py-3 px-4 text-center">Hatch Date</th>
                                <th class="py-3 px-4 text-center">Population</th>
                                <th class="py-3 px-4 text-center">Deaths / Culls</th>
                                <th class="py-3 px-4 text-center">Survival</th>
                                <th class="py-3 px-4">Growout Tank</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach ($batches as $batch)
                                @php
                                    $survivalRate = $batch->initial_count > 0 ? round(($batch->current_count / $batch->initial_count) * 100) : 100;
                                @endphp
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-400">
                                        <a href="{{ route('batches.show', $batch) }}" class="hover:underline">
                                            {{ $batch->batch_code }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-white">{{ $batch->variety ?: $batch->species?->name }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $batch->species?->name }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs">
                                        @if($batch->breedingEvent)
                                            <a href="{{ route('breeding.show', $batch->breedingEvent) }}" class="text-cyan-400 hover:underline font-mono">
                                                {{ $batch->breedingEvent->breeding_code }}
                                            </a>
                                            <div class="text-[10px] text-slate-500">
                                                {{ $batch->breedingEvent->maleLivestock?->livestock_code ?? '?' }} &times; {{ $batch->breedingEvent->femaleLivestock?->livestock_code ?? '?' }}
                                            </div>
                                        @else
                                            <span class="text-slate-500 italic">Direct Entry</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-xs text-slate-300">
                                        {{ $batch->birth_or_hatch_date ? $batch->birth_or_hatch_date->format('M d, Y') : '?' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono">
                                        <span class="font-bold text-white text-base">{{ $batch->current_count }}</span>
                                        <span class="text-[10px] text-slate-500 block">of {{ $batch->initial_count }} initial</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono text-xs">
                                        <span class="text-rose-400 font-semibold">{{ $batch->death_count }}</span>
                                        <span class="text-slate-500">/</span>
                                        <span class="text-amber-400 font-semibold">{{ $batch->cull_count }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold border
                                            {{ $survivalRate >= 80 ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/40' :
                                              ($survivalRate >= 50 ? 'bg-amber-950/60 text-amber-400 border-amber-800/40' : 'bg-rose-950/60 text-rose-400 border-rose-800/40') }}">
                                            {{ $survivalRate }}%
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs">
                                        @if($batch->tank)
                                            <a href="{{ route('tanks.show', $batch->tank) }}" class="text-cyan-400 hover:underline">
                                                {{ $batch->tank->tank_code }}
                                            </a>
                                        @else
                                            <span class="text-slate-500 italic">None</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                                            {{ $batch->status === 'READY_FOR_SALE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/40' :
                                              ($batch->status === 'READY_FOR_GRADING' ? 'bg-amber-950/60 text-amber-400 border-amber-800/40' :
                                              ($batch->status === 'SOLD_OUT' ? 'bg-slate-800 text-slate-400 border-slate-700' : 'bg-blue-950/60 text-blue-400 border-blue-800/40')) }}">
                                            {{ $batch->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('batches.show', $batch) }}" class="p-1.5 hover:bg-slate-800 text-slate-400 hover:text-blue-400 rounded transition" title="View Batch & Log Losses">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('batches.edit', $batch) }}" class="p-1.5 hover:bg-slate-800 text-slate-400 hover:text-blue-400 rounded transition" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Archive batch {{ $batch->batch_code }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 hover:bg-rose-950/40 text-slate-400 hover:text-rose-400 rounded transition" title="Archive">
                                                    <i data-lucide="archive" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-800/80">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
