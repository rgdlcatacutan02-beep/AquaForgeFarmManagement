<x-app-layout title="Breeding Events">
    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <i data-lucide="git-merge" class="w-6 h-6 text-pink-400"></i>
                    Breeding Management
                </h1>
                <p class="text-sm text-slate-400 mt-1">Track pairings, spawning dates, gestation, and lineage across all species.</p>
            </div>
            <div>
                <a href="{{ route('breeding.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-400 hover:to-rose-500 text-white font-semibold rounded-lg shadow-lg shadow-pink-950/40 text-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    New Breeding Pair
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 backdrop-blur-sm">
            <form method="GET" action="{{ route('breeding.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search breeding code or notes..." class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">
                </div>
                <div>
                    <select name="species_id" onchange="this.form.submit()" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-pink-500">
                        <option value="">All Species</option>
                        @foreach($species as $sp)
                            <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-pink-500">
                        <option value="">All Statuses</option>
                        @foreach(['PLANNED', 'ACTIVE', 'COMPLETED', 'FAILED', 'CANCELLED'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <select name="tank_id" onchange="this.form.submit()" class="flex-1 bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-pink-500">
                        <option value="">All Tanks</option>
                        @foreach($tanks as $tk)
                            <option value="{{ $tk->id }}" {{ request('tank_id') == $tk->id ? 'selected' : '' }}>{{ $tk->tank_code }}</option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['search', 'species_id', 'status', 'tank_id']))
                        <a href="{{ route('breeding.index') }}" class="p-2 bg-slate-800/40 hover:bg-slate-800 text-slate-400 text-xs rounded-lg flex items-center justify-center transition" title="Clear Filters">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Breeding Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($events as $event)
                <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-xl hover:border-pink-500/40 transition-all flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('breeding.show', $event) }}" class="font-mono text-lg font-bold text-pink-400 hover:underline">
                                    {{ $event->breeding_code }}
                                </a>
                                <div class="text-xs text-slate-400 font-medium mt-0.5">{{ $event->species?->name }}</div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold border
                                {{ $event->status === 'ACTIVE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' :
                                  ($event->status === 'PLANNED' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' :
                                  ($event->status === 'COMPLETED' ? 'bg-blue-950/60 text-blue-400 border-blue-800/50' :
                                  ($event->status === 'FAILED' ? 'bg-rose-950/60 text-rose-400 border-rose-800/50' : 'bg-slate-800 text-slate-400 border-slate-700'))) }}">
                                {{ $event->status }}
                            </span>
                        </div>

                        <!-- Parents Breakdown -->
                        <div class="mt-4 p-3 rounded-lg bg-slate-950/60 border border-slate-800/80 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-blue-400 font-semibold flex items-center gap-1.5">
                                    <span>♂</span> Male Parent:
                                </span>
                                @if($event->maleLivestock)
                                    <a href="{{ route('livestock.show', $event->maleLivestock) }}" class="font-mono text-slate-200 hover:text-cyan-400 font-bold">
                                        {{ $event->maleLivestock->livestock_code }}
                                        <span class="font-sans font-normal text-slate-400 text-[11px]">({{ $event->maleLivestock->variety ?: 'Standard' }})</span>
                                    </a>
                                @else
                                    <span class="text-slate-500 italic">Unspecified</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-800/60">
                                <span class="text-rose-400 font-semibold flex items-center gap-1.5">
                                    <span>♀</span> Female Parent:
                                </span>
                                @if($event->femaleLivestock)
                                    <a href="{{ route('livestock.show', $event->femaleLivestock) }}" class="font-mono text-slate-200 hover:text-cyan-400 font-bold">
                                        {{ $event->femaleLivestock->livestock_code }}
                                        <span class="font-sans font-normal text-slate-400 text-[11px]">({{ $event->femaleLivestock->variety ?: 'Standard' }})</span>
                                    </a>
                                @else
                                    <span class="text-slate-500 italic">Unspecified</span>
                                @endif
                            </div>
                        </div>

                        <!-- Dates & Tank -->
                        <div class="mt-3.5 space-y-1 text-xs text-slate-400">
                            <div class="flex items-center justify-between">
                                <span>Breeding Tank:</span>
                                @if($event->tank)
                                    <a href="{{ route('tanks.show', $event->tank) }}" class="font-mono text-cyan-400 hover:underline">
                                        {{ $event->tank->tank_code }} ({{ $event->tank->name }})
                                    </a>
                                @else
                                    <span class="text-slate-500 italic">None</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Pairing Date:</span>
                                <span class="text-slate-200">{{ $event->start_date ? $event->start_date->format('M d, Y') : '?' }}</span>
                            </div>
                            @if($event->expected_date)
                                <div class="flex items-center justify-between">
                                    <span>Expected Birth/Hatch:</span>
                                    <span class="text-amber-400">{{ $event->expected_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-5 pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <div class="text-xs text-slate-400">
                            Batches: <strong class="text-pink-400">{{ $event->offspringBatches->count() }}</strong>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('breeding.edit', $event) }}" class="p-1.5 text-slate-400 hover:text-pink-400 hover:bg-slate-800 rounded transition" title="Edit Pairing">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('breeding.show', $event) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-pink-950/70 hover:bg-pink-900/80 text-pink-300 border border-pink-800/60 text-xs font-semibold transition">
                                <span>View Pairing</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/40 rounded-xl border border-dashed border-slate-800">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-800/60 flex items-center justify-center text-slate-500 mb-3">
                        <i data-lucide="git-merge" class="w-6 h-6"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-300">No breeding events recorded.</p>
                    <p class="text-xs text-slate-500 mt-1">Pair up male and female breeders to start tracking lineage and offspring survival.</p>
                    <div class="mt-5">
                        <a href="{{ route('breeding.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-pink-500 hover:bg-pink-400 text-white font-semibold rounded-lg text-xs transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Start First Breeding Event
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-800/80">
            {{ $events->links() }}
        </div>
    </div>
</x-app-layout>
