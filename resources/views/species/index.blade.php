<x-app-layout title="Species Management">
    <div class="space-y-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-6 h-6 text-cyan-400"></i>
                    Species Catalog
                </h1>
                <p class="text-sm text-slate-400 mt-1">Manage kept and bred species, descriptions, and lineage categories.</p>
            </div>
            <div>
                <a href="{{ route('species.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold rounded-lg shadow-lg shadow-cyan-950/40 text-sm transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Species
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 backdrop-blur-sm">
            <form method="GET" action="{{ route('species.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-3"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search species by common or scientific name..." class="w-full bg-slate-950/80 border border-slate-800 rounded-lg pl-9 pr-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                </div>
                <div class="flex gap-2">
                    <select name="status" class="bg-slate-950/80 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-cyan-500 flex-1">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium rounded-lg transition">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('species.index') }}" class="px-3 py-2 bg-slate-800/40 hover:bg-slate-800 text-slate-400 text-sm rounded-lg flex items-center justify-center transition" title="Clear Filters">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Species List -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl overflow-hidden shadow-xl">
            @if ($species->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-slate-800/80 flex items-center justify-center text-slate-500 mb-4">
                        <i data-lucide="layers" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-base font-semibold text-slate-300">No species found</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">Get started by defining the aquatic species you currently keep or breed on your farm.</p>
                    <div class="mt-6">
                        <a href="{{ route('species.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold rounded-lg text-sm transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add First Species
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950/70 border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Species Name</th>
                                <th class="py-3 px-4">Scientific Name</th>
                                <th class="py-3 px-4 text-center">Livestock</th>
                                <th class="py-3 px-4 text-center">Batches</th>
                                <th class="py-3 px-4 text-center">Breedings</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach ($species as $item)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 font-medium text-white">
                                        <a href="{{ route('species.show', $item) }}" class="hover:text-cyan-400 transition flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ $item->active ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                            {{ $item->name }}
                                        </a>
                                        @if($item->description)
                                            <div class="text-xs text-slate-500 truncate max-w-xs mt-0.5">{{ Str::limit($item->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 italic text-slate-400 font-mono text-xs">
                                        {{ $item->scientific_name ?: '?' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-950/80 text-cyan-400 border border-cyan-800/40">
                                            {{ $item->livestock_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-950/80 text-blue-400 border border-blue-800/40">
                                            {{ $item->offspring_batches_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-950/80 text-indigo-400 border border-indigo-800/40">
                                            {{ $item->breeding_events_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($item->active)
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">ACTIVE</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">INACTIVE</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('species.show', $item) }}" class="p-1.5 hover:bg-slate-800 text-slate-400 hover:text-cyan-400 rounded transition" title="View details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('species.edit', $item) }}" class="p-1.5 hover:bg-slate-800 text-slate-400 hover:text-cyan-400 rounded transition" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('species.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this species?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 hover:bg-rose-950/40 text-slate-400 hover:text-rose-400 rounded transition" title="Delete">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
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
                    {{ $species->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
