<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Water Quality Logs</h1>
                <p class="text-xs text-slate-400 mt-0.5">Parameters history: Temperature, pH, Ammonia, Nitrite, Nitrate</p>
            </div>
            <a href="{{ route('water-logs.create') }}" class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white flex items-center gap-1.5 shadow-md shadow-cyan-950 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Log Water Test</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/40">
                    <tr>
                        <th class="py-3 px-4">Date & Time</th>
                        <th class="py-3 px-4">Tank</th>
                        <th class="py-3 px-4">Temp (°C)</th>
                        <th class="py-3 px-4">pH</th>
                        <th class="py-3 px-4">Ammonia</th>
                        <th class="py-3 px-4">Nitrite</th>
                        <th class="py-3 px-4">Nitrate</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 text-slate-400">{{ $log->recorded_at->format('M d, Y H:i') }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-cyan-400">{{ $log->tank?->tank_code }}</td>
                            <td class="py-3 px-4 font-mono">{{ $log->temperature ?? '--' }}</td>
                            <td class="py-3 px-4 font-mono">{{ $log->ph ?? '--' }}</td>
                            <td class="py-3 px-4 font-mono {{ ($log->ammonia ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ $log->ammonia ?? '--' }}</td>
                            <td class="py-3 px-4 font-mono {{ ($log->nitrite ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ $log->nitrite ?? '--' }}</td>
                            <td class="py-3 px-4 font-mono">{{ $log->nitrate ?? '--' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $log->status === 'GOOD' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' : 'bg-amber-950/60 text-amber-400 border-amber-800/50' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">No water logs on record.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $logs->links() }}</div>
    </div>
</x-app-layout>
