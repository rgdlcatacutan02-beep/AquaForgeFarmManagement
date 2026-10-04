<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-cyan-400"></i>
                    <span>Customer Directory</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Fish buyers, local aquarium hobbyists, pet stores, and wholesale contacts</p>
            </div>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Add Customer</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Search Toolbar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3 shadow-sm flex items-center justify-between">
            <form method="GET" action="{{ route('customers.index') }}" class="flex items-center gap-2 w-full max-w-md">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name, phone, email..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                </div>
                <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('customers.index') }}" class="px-2 py-1.5 text-xs text-slate-400 hover:text-white">Clear</a>
                @endif
            </form>
        </div>

        <!-- Customer Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/50">
                        <tr>
                            <th class="py-3 px-4">Customer Name</th>
                            <th class="py-3 px-4">Phone / WhatsApp</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Address / City</th>
                            <th class="py-3 px-4 text-center">Orders</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($customers as $c)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-white">
                                    <a href="{{ route('customers.show', $c) }}" class="hover:text-cyan-400 transition-colors flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-cyan-950/80 border border-cyan-800/60 text-cyan-400 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($c->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $c->name }}</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    {{ $c->phone ?? '--' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    {{ $c->email ?? '--' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 max-w-xs truncate">
                                    {{ $c->address ?? '--' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded font-mono font-bold text-xs {{ $c->sales_count > 0 ? 'bg-cyan-950/60 text-cyan-400 border border-cyan-800/50' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $c->sales_count }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('customers.show', $c) }}" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-cyan-400 transition-colors" title="View Profile">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $c) }}" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-cyan-400 transition-colors" title="Edit">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Delete customer {{ addslashes($c->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded hover:bg-slate-800 text-slate-400 hover:text-rose-400 transition-colors" title="Delete">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="user-x" class="w-8 h-8 text-slate-600"></i>
                                        <p>No customers recorded yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $customers->links() }}</div>
    </div>
</x-app-layout>
