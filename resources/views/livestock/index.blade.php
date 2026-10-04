<x-app-layout title="Livestock Registry">
    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <i data-lucide="fish" class="w-6 h-6 text-cyan-400"></i>
                    Livestock Registry
                </h1>
                <p class="text-sm text-slate-400 mt-1">Individual fish, breeding pairs, show specimens, and cataloged breeders.</p>
            </div>
            <div>
                <a href="{{ route('livestock.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold rounded-lg shadow-lg shadow-cyan-950/40 text-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Livestock
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 backdrop-blur-sm">
            <form method="GET" action="{{ route('livestock.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                <!-- Search -->
                <div class="sm:col-span-2 relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-3"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, variety, notes..." class="w-full bg-slate-950/80 border border-slate-800 rounded-lg pl-9 pr-3 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                </div>

                <!-- Species Filter -->
                <div>
                    <select name="species_id" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-2.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">All Species</option>
                        @foreach($species as $sp)
                            <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-2.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">All Statuses</option>
                        @foreach(['BREEDER', 'GROWOUT', 'DISPLAY', 'QUARANTINE', 'SICK', 'AVAILABLE', 'SOLD', 'DECEASED', 'CULLED'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tank Filter -->
                <div>
                    <select name="tank_id" class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-2.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">All Tanks</option>
                        @foreach($tanks as $tk)
                            <option value="{{ $tk->id }}" {{ request('tank_id') == $tk->id ? 'selected' : '' }}>{{ $tk->tank_code }} ({{ $tk->name }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Grade Filter & Submit -->
                <div class="flex gap-2">
                    <select name="grade" class="flex-1 bg-slate-950/80 border border-slate-800 rounded-lg px-2 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">All Grades</option>
                        @foreach(['SHOW', 'BREEDER', 'MATERIAL', 'CULL'] as $gr)
                            <option value="{{ $gr }}" {{ request('grade') === $gr ? 'selected' : '' }}>{{ $gr }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">Filter</button>
                    @if(request()->hasAny(['search', 'species_id', 'status', 'tank_id', 'grade']))
                        <a href="{{ route('livestock.index') }}" class="p-2 bg-slate-800/40 hover:bg-slate-800 text-slate-400 text-xs rounded-lg flex items-center justify-center transition" title="Reset Filters">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl overflow-hidden shadow-xl">
            @if ($livestock->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-slate-800/80 flex items-center justify-center text-slate-500 mb-4">
                        <i data-lucide="fish" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-base font-semibold text-slate-300">No livestock records found</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">Start tracking your individual breeders, show specimens, or sale animals.</p>
                    <div class="mt-6">
                        <a href="{{ route('livestock.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold rounded-lg text-sm transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add First Livestock
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/70 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Specimen</th>
                                <th class="py-3 px-4">Species & Variety</th>
                                <th class="py-3 px-4 text-center">Sex</th>
                                <th class="py-3 px-4">Assigned Tank</th>
                                <th class="py-3 px-4 text-center">Grade</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach ($livestock as $item)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->photo_url)
                                                <img src="{{ $item->photo_url }}" alt="{{ $item->livestock_code }}" class="w-10 h-10 rounded-lg object-cover border border-slate-700 shadow-sm">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-slate-800 border border-slate-700/60 flex items-center justify-center text-slate-500">
                                                    <i data-lucide="fish" class="w-5 h-5"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('livestock.show', $item) }}" class="font-mono font-bold text-cyan-400 hover:underline">
                                                    {{ $item->livestock_code }}
                                                </a>
                                                @if($item->purchase_price > 0)
                                                    <div class="text-[11px] text-slate-500 font-mono">₱{{ number_format($item->purchase_price, 2) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-white">{{ $item->variety ?: $item->species?->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $item->species?->name }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($item->sex === 'MALE')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-400 bg-blue-950/60 border border-blue-800/40 px-2 py-0.5 rounded">
                                                <span>?</span> Male
                                            </span>
                                        @elseif($item->sex === 'FEMALE')
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-400 bg-rose-950/60 border border-rose-800/40 px-2 py-0.5 rounded">
                                                <span>?</span> Female
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-xs text-slate-400 bg-slate-800 px-2 py-0.5 rounded">
                                                Unknown
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($item->tank)
                                            <a href="{{ route('tanks.show', $item->tank) }}" class="font-mono text-cyan-400 hover:underline text-xs flex items-center gap-1">
                                                <i data-lucide="box" class="w-3.5 h-3.5 text-slate-500"></i>
                                                {{ $item->tank->tank_code }}
                                                <span class="text-slate-500">({{ $item->tank->name }})</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-500 italic">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($item->grade)
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold border
                                                {{ $item->grade === 'SHOW' ? 'bg-amber-950/70 text-amber-400 border-amber-700/60' :
                                                  ($item->grade === 'BREEDER' ? 'bg-cyan-950/70 text-cyan-400 border-cyan-700/60' :
                                                  ($item->grade === 'MATERIAL' ? 'bg-indigo-950/70 text-indigo-400 border-indigo-700/60' : 'bg-rose-950/70 text-rose-400 border-rose-700/60')) }}">
                                                {{ $item->grade }}
                                            </span>
                                        @else
                                            <span class="text-slate-600 text-xs">?</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold border
                                            {{ $item->status === 'BREEDER' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' :
                                              ($item->status === 'AVAILABLE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' :
                                              ($item->status === 'QUARANTINE' ? 'bg-amber-950/60 text-amber-400 border-amber-800/50' :
                                              ($item->status === 'SICK' ? 'bg-rose-950/60 text-rose-400 border-rose-800/50' :
                                              ($item->status === 'SOLD' ? 'bg-indigo-950/60 text-indigo-400 border-indigo-800/50' : 'bg-slate-800 text-slate-300 border-slate-700')))) }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('livestock.show', $item) }}" class="p-1.5 hover:bg-slate-800 text-slate-400 hover:text-cyan-400 rounded transition" title="View Specimen">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('livestock.edit', $item) }}" class="p-1.5 hover:bg-slate-800 text-slate-400 hover:text-cyan-400 rounded transition" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('livestock.destroy', $item) }}" method="POST" onsubmit="return confirm('Archive this livestock record?');" class="inline">
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
                    {{ $livestock->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
