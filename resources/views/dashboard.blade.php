<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Farm Dashboard</h1>
                <p class="text-xs text-slate-400 mt-0.5">Real-time aquatic livestock, breeding operations, and farm parameters.</p>
            </div>
            <a href="{{ route('tanks.scan') }}" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all">
                <i data-lucide="scan-line" class="w-4 h-4"></i>
                <span>Scan Tank QR</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- 6 KPI CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- 1. Total Livestock -->
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-medium">Total Livestock</span>
                    <i data-lucide="fish" class="w-4 h-4 text-cyan-400"></i>
                </div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $totalLivestock }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Active catalogued</div>
            </div>

            <!-- 2. Total Tanks -->
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-medium">Total Tanks</span>
                    <i data-lucide="box" class="w-4 h-4 text-emerald-400"></i>
                </div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $totalTanks }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Active wet setups</div>
            </div>

            <!-- 3. Active Breeding -->
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-medium">Active Breeding</span>
                    <i data-lucide="heart-handshake" class="w-4 h-4 text-rose-400"></i>
                </div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $activeBreeding }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Pairs & colonies</div>
            </div>

            <!-- 4. Available for Sale -->
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-medium">Available for Sale</span>
                    <i data-lucide="tag" class="w-4 h-4 text-amber-400"></i>
                </div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $availableForSale }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Specimens & fry</div>
            </div>

            <!-- 5. Monthly Revenue -->
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-medium">Monthly Revenue</span>
                    <i data-lucide="dollar-sign" class="w-4 h-4 text-emerald-400"></i>
                </div>
                <div class="mt-2 text-2xl font-bold text-white">₱{{ number_format($monthlyRevenue, 2) }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ now()->format('M Y') }}</div>
            </div>

            <!-- 6. Monthly Expenses -->
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-medium">Monthly Expenses</span>
                    <i data-lucide="receipt" class="w-4 h-4 text-rose-400"></i>
                </div>
                <div class="mt-2 text-2xl font-bold text-white">₱{{ number_format($monthlyExpenses, 2) }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ now()->format('M Y') }}</div>
            </div>
        </div>

        <!-- MAIN GRID: Livestock Chart & Tasks -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Livestock Species Distribution (Chart.js) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                    <div>
                        <h2 class="text-base font-semibold text-white">Livestock Overview</h2>
                        <p class="text-xs text-slate-400">Population distribution across species lines</p>
                    </div>
                    <a href="{{ route('livestock.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">View All &rarr;</a>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div class="sm:col-span-1 flex items-center justify-center p-2">
                        <div class="relative w-40 h-40">
                            <canvas id="speciesChart"></canvas>
                        </div>
                    </div>
                    <div class="sm:col-span-2 space-y-2">
                        @forelse ($speciesDistribution as $item)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                    <span class="font-medium text-slate-200">{{ $item['name'] }}</span>
                                </div>
                                <span class="font-bold text-white bg-slate-800/80 px-2 py-0.5 rounded">{{ $item['count'] }} count</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No active species records found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right: Tasks / Reminders -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                    <div>
                        <h2 class="text-base font-semibold text-white">Tasks / Reminders</h2>
                        <p class="text-xs text-slate-400">Pending husbandry chores</p>
                    </div>
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($tasks as $task)
                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800 text-xs flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-medium text-slate-200 truncate">{{ $task->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <span class="text-cyan-400 font-mono">{{ $task->tank?->tank_code ?? 'General' }}</span>
                                    <span>&bull;</span>
                                    <span>Due: {{ $task->due_date ? $task->due_date->format('M d') : 'Today' }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-amber-950/50 text-amber-300 border border-amber-800/50 flex-shrink-0">
                                PENDING
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 text-xs">
                            <i data-lucide="check-circle-2" class="w-6 h-6 mx-auto mb-1 text-slate-600"></i>
                            All maintenance tasks up to date.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TANK STATUS LIST (Cards / Table) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div>
                    <h2 class="text-base font-semibold text-white">Tank Status</h2>
                    <p class="text-xs text-slate-400">Current occupants, water metrics, and biological stability</p>
                </div>
                <a href="{{ route('tanks.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">Manage Tanks &rarr;</a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800 bg-slate-950/40">
                        <tr>
                            <th class="py-2.5 px-3">Tank ID</th>
                            <th class="py-2.5 px-3">Name & Location</th>
                            <th class="py-2.5 px-3">Occupants</th>
                            <th class="py-2.5 px-3">Water Parameters</th>
                            <th class="py-2.5 px-3">Water Status</th>
                            <th class="py-2.5 px-3">Tank Status</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($tanks as $tank)
                            @php
                                $wLog = $tank->latestWaterLog;
                                $waterStatus = $wLog ? $wLog->status : 'CHECK';
                                $statusBadgeClass = match($waterStatus) {
                                    'GOOD' => 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50',
                                    'WARNING' => 'bg-amber-950/60 text-amber-400 border-amber-800/50',
                                    default => 'bg-rose-950/60 text-rose-400 border-rose-800/50',
                                };
                            @endphp
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-cyan-400">{{ $tank->tank_code }}</td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-slate-200">{{ $tank->name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $tank->location ?? 'Unspecified' }} ({{ $tank->volume_liters }}L)</div>
                                </td>
                                <td class="py-3 px-3">
                                    @php
                                        $lNames = $tank->livestock->map(fn($l) => $l->variety ?: $l->species?->name)->all();
                                        $bNames = $tank->offspringBatches->map(fn($b) => $b->batch_code . " (" . $b->current_count . " fry)")->all();
                                        $occupantNames = implode(", ", array_slice(array_merge($lNames, $bNames), 0, 2));
                                    @endphp
                                    <div class="truncate max-w-xs">{{ $occupantNames ?: 'Empty / Ready' }}</div>
                                </td>
                                <td class="py-3 px-3 font-mono text-[11px]">
                                    @if ($wLog)
                                        <span>{{ $wLog->temperature }}°C</span> | <span>pH {{ $wLog->ph }}</span>
                                    @else
                                        <span class="text-slate-500">No test logged</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $statusBadgeClass }}">
                                        {{ $waterStatus }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ $tank->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('tanks.show', $tank) }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">View &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-500">
                                    No tanks registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LOWER SECTION: Active Breeding & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Breeding -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                    <div>
                        <h2 class="text-base font-semibold text-white">Active Breeding</h2>
                        <p class="text-xs text-slate-400">Current paired lines & gestation monitoring</p>
                    </div>
                    <a href="{{ route('breeding.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium">View All &rarr;</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($activeBreedingEvents as $event)
                        <div class="p-3.5 rounded-lg bg-slate-950/60 border border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-cyan-400">{{ $event->breeding_code }}</span>
                                    <span class="font-medium text-slate-200">{{ $event->species?->name }}</span>
                                </div>
                                <div class="text-slate-400 text-[11px] mt-1">
                                    Parents: <span class="text-slate-300 font-mono">{{ $event->maleLivestock?->livestock_code ?? 'M?' }}</span> &times; <span class="text-slate-300 font-mono">{{ $event->femaleLivestock?->livestock_code ?? 'F?' }}</span>
                                    @if($event->tank)
                                        &bull; Tank: <span class="text-cyan-400 font-mono">{{ $event->tank->tank_code }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-950/60 text-rose-300 border border-rose-800/50">
                                    {{ $event->status }}
                                </span>
                                <div class="text-[10px] text-slate-500 mt-1">Since {{ $event->start_date->format('M d') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 text-xs">
                            No active breeding events at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                    <div>
                        <h2 class="text-base font-semibold text-white">Recent Activity</h2>
                        <p class="text-xs text-slate-400">Latest operations across the farm</p>
                    </div>
                    <i data-lucide="activity" class="w-4 h-4 text-slate-400"></i>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($recentActivity as $act)
                        <div class="flex items-start gap-3 p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/60 text-xs">
                            <div class="p-1.5 rounded-md bg-slate-800 text-cyan-400 mt-0.5">
                                @if($act['type'] === 'water')
                                    <i data-lucide="droplet" class="w-3.5 h-3.5"></i>
                                @elseif($act['type'] === 'maintenance')
                                    <i data-lucide="wrench" class="w-3.5 h-3.5 text-amber-400"></i>
                                @elseif($act['type'] === 'sale')
                                    <i data-lucide="dollar-sign" class="w-3.5 h-3.5 text-emerald-400"></i>
                                @else
                                    <i data-lucide="fish" class="w-3.5 h-3.5 text-cyan-400"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-slate-200 truncate">{{ $act['title'] }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $act['subtitle'] }}</div>
                            </div>
                            <div class="text-[10px] text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($act['time'])->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 text-xs">
                            No recent logs recorded.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Species Distribution Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('speciesChart');
            if (ctx && window.Chart) {
                const labels = @json($speciesDistribution->pluck('name'));
                const data = @json($speciesDistribution->pluck('count'));

                new window.Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#06b6d4', // cyan-500
                                '#10b981', // emerald-500
                                '#f59e0b', // amber-500
                                '#ec4899', // pink-500
                                '#8b5cf6', // purple-500
                            ],
                            borderColor: '#0f172a',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: '70%',
                    }
                });
            }
        });
    </script>
</x-app-layout>