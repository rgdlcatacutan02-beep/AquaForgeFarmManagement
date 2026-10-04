<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Maintenance History</h1>
                <p class="text-xs text-slate-400 mt-0.5">Water changes, filter service, and equipment upkeep</p>
            </div>
            <a href="{{ route('maintenance.create') }}" class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white flex items-center gap-1.5 shadow-md shadow-cyan-950 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Log Maintenance</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/40">
                    <tr>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Tank</th>
                        <th class="py-3 px-4">Maintenance Type</th>
                        <th class="py-3 px-4">Water Change %</th>
                        <th class="py-3 px-4">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 text-slate-400">{{ $log->performed_at->format('M d, Y H:i') }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">{{ $log->tank?->tank_code }}</td>
                            <td class="py-3 px-4 font-semibold text-white">{{ str_replace('_', ' ', $log->maintenance_type) }}</td>
                            <td class="py-3 px-4 font-mono">{{ $log->water_change_percentage ? $log->water_change_percentage . '%' : '--' }}</td>
                            <td class="py-3 px-4 text-slate-400">{{ $log->notes ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-500">No maintenance records logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $logs->links() }}</div>
    </div>
</x-app-layout>
