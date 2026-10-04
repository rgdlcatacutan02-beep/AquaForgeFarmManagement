<x-app-layout title="Tanks & Setups">
    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <i data-lucide="box" class="w-6 h-6 text-cyan-400"></i>
                    Aquariums & Tank Setups
                </h1>
                <p class="text-sm text-slate-400 mt-1">Manage aquariums, breeding tubs, growout vats, and filtration systems.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('tanks.scan') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-cyan-300 font-semibold rounded-lg border border-slate-700 text-sm transition-all shadow-sm">
                    <i data-lucide="scan-line" class="w-4 h-4 text-cyan-400"></i>
                    Scan QR
                </a>
                <a href="{{ route('tanks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold rounded-lg shadow-lg shadow-cyan-950/40 text-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Tank
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 backdrop-blur-sm">
            <form method="GET" action="{{ route('tanks.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2 relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-3"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tank code, name, location..." class="w-full bg-slate-950/80 border border-slate-800 rounded-lg pl-9 pr-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">All Statuses</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="EMPTY" {{ request('status') === 'EMPTY' ? 'selected' : '' }}>Empty</option>
                        <option value="MAINTENANCE" {{ request('status') === 'MAINTENANCE' ? 'selected' : '' }}>Maintenance</option>
                        <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <select name="purpose" onchange="this.form.submit()" class="flex-1 bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">All Purposes</option>
                        <option value="BREEDING" {{ request('purpose') === 'BREEDING' ? 'selected' : '' }}>Breeding</option>
                        <option value="GROWOUT" {{ request('purpose') === 'GROWOUT' ? 'selected' : '' }}>Growout</option>
                        <option value="DISPLAY" {{ request('purpose') === 'DISPLAY' ? 'selected' : '' }}>Display</option>
                        <option value="QUARANTINE" {{ request('purpose') === 'QUARANTINE' ? 'selected' : '' }}>Quarantine</option>
                        <option value="HOLDING" {{ request('purpose') === 'HOLDING' ? 'selected' : '' }}>Holding</option>
                        <option value="SICK" {{ request('purpose') === 'SICK' ? 'selected' : '' }}>Sick / Hospital</option>
                        <option value="SALES" {{ request('purpose') === 'SALES' ? 'selected' : '' }}>Sales</option>
                    </select>
                    @if(request()->hasAny(['search', 'status', 'purpose']))
                        <a href="{{ route('tanks.index') }}" class="p-2 bg-slate-800/40 hover:bg-slate-800 text-slate-400 text-xs rounded-lg flex items-center justify-center transition" title="Clear Filters">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tanks Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($tanks as $tank)
                @php
                    $wLog = $tank->latestWaterLog;
                    $statusColor = match($wLog?->status) {
                        'GOOD' => 'text-emerald-400 bg-emerald-950/60 border-emerald-800/40',
                        'WARNING' => 'text-amber-400 bg-amber-950/60 border-amber-800/40',
                        default => 'text-rose-400 bg-rose-950/60 border-rose-800/40',
                    };
                @endphp
                <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-xl hover:border-cyan-500/40 transition-all flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('tanks.show', $tank) }}" class="font-mono text-lg font-bold text-cyan-400 hover:underline">
                                    {{ $tank->tank_code }}
                                </a>
                                <h3 class="text-sm font-semibold text-white mt-0.5 leading-snug">{{ $tank->name }}</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $tank->location ?? 'Unspecified' }} &bull; <strong class="text-slate-200">{{ $tank->volume_liters }}L</strong> ({{ $tank->tank_type }})
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded border
                                    {{ $tank->status === 'ACTIVE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' :
                                      ($tank->status === 'EMPTY' ? 'bg-slate-800 text-slate-400 border-slate-700' :
                                      ($tank->status === 'MAINTENANCE' ? 'bg-amber-950/60 text-amber-400 border-amber-800/50' : 'bg-rose-950/60 text-rose-400 border-rose-800/50')) }}">
                                    {{ $tank->status }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-950 text-cyan-300 border border-slate-800">
                                    {{ $tank->purpose }}
                                </span>
                            </div>
                        </div>

                        <!-- Occupants & Parameters Preview -->
                        <div class="mt-4 pt-3 border-t border-slate-800/80 space-y-2">
                            <!-- Occupants -->
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 flex items-center gap-1.5">
                                    <i data-lucide="fish" class="w-3.5 h-3.5 text-slate-500"></i>
                                    Occupants:
                                </span>
                                <span class="font-semibold text-slate-200">
                                    {{ $tank->livestock->count() }} adult(s) &bull; {{ $tank->offspringBatches->sum('current_count') }} fry
                                </span>
                            </div>

                            <!-- Water Snapshot -->
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 flex items-center gap-1.5">
                                    <i data-lucide="droplet" class="w-3.5 h-3.5 text-cyan-400"></i>
                                    Water:
                                </span>
                                @if($wLog)
                                    <span class="font-mono text-[11px] text-slate-300">
                                        {{ $wLog->temperature }}?C | pH {{ $wLog->ph }}
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border ml-1 {{ $statusColor }}">
                                            {{ $wLog->status }}
                                        </span>
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-500 italic">No tests logged</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Links -->
                    <div class="mt-5 pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tanks.edit', $tank) }}" class="p-1.5 text-slate-400 hover:text-cyan-400 hover:bg-slate-800 rounded transition" title="Edit Setup">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('tanks.destroy', $tank) }}" method="POST" onsubmit="return confirm('Archive tank {{ $tank->tank_code }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 rounded transition" title="Archive Tank">
                                    <i data-lucide="archive" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tanks.print-label', $tank) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-cyan-400 transition" title="Print QR Sticker Tag">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('tanks.show', $tank) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-950/70 hover:bg-cyan-900/80 text-cyan-400 border border-cyan-800/60 text-xs font-semibold transition">
                                <span>Open Hub</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/40 rounded-xl border border-dashed border-slate-800">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-800/60 flex items-center justify-center text-slate-500 mb-3">
                        <i data-lucide="box" class="w-6 h-6"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-300">No tanks yet.</p>
                    <p class="text-xs text-slate-500 mt-1">Add your first aquarium or growout vat to begin managing your aquatic setup.</p>
                    <div class="mt-5">
                        <a href="{{ route('tanks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold rounded-lg text-xs transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add First Tank
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-800/80">
            {{ $tanks->links() }}
        </div>
    </div>
</x-app-layout>
