<aside class="flex flex-col flex-shrink-0 w-64 bg-slate-900 border-r border-slate-800 text-slate-300 min-h-screen">
    @php
        $sidebarUser = auth()->user();
        $farmName = $sidebarUser?->farm_name ?: 'AquaForge';
        $farmLogo = $sidebarUser?->farm_logo_url;
    @endphp
    <!-- Brand Header -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-800/80 bg-slate-950/40">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0 w-full">
            @if($farmLogo)
                <img src="{{ $farmLogo }}" alt="{{ $farmName }} Logo" class="w-10 h-10 object-contain rounded-lg p-1 bg-slate-900 border border-cyan-500/30 shadow-md shadow-cyan-950/50 flex-shrink-0">
            @else
                <x-application-logo class="w-9 h-9 flex-shrink-0 shadow-md shadow-cyan-900/40" />
            @endif
            <div class="min-w-0 flex-1">
                <div class="text-sm font-bold text-white tracking-wide group-hover:text-cyan-400 transition-colors truncate" title="{{ $farmName }}">{{ $farmName }}</div>
                <div class="text-[9px] text-cyan-400/90 font-medium tracking-tight truncate">Aquatic Farm & Breeding Management System</div>
                <div class="text-[9px] text-cyan-300 font-semibold tracking-wider uppercase flex items-center gap-1 mt-0.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span class="truncate">Powered by AquaForge</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto text-sm">
        <!-- Main / Dashboard -->
        <div>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-cyan-400"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Section: LIVESTOCK -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Livestock</div>
            <div class="space-y-1">
                <a href="{{ route('livestock.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('livestock.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="fish" class="w-4 h-4"></i>
                    <span>Livestock</span>
                </a>
                <a href="{{ route('batches.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('batches.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span>Batches</span>
                </a>
                <a href="{{ route('species.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('species.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="dna" class="w-4 h-4"></i>
                    <span>Species</span>
                </a>
            </div>
        </div>

        <!-- Section: FARM -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Farm</div>
            <div class="space-y-1">
                <a href="{{ route('tanks.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('tanks.index', 'tanks.show', 'tanks.create', 'tanks.edit') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="box" class="w-4 h-4"></i>
                    <span>Tanks</span>
                </a>
                <a href="{{ route('tanks.scan') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('tanks.scan') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="scan-line" class="w-4 h-4 text-cyan-400"></i>
                    <span>Scan Tank QR</span>
                </a>
                <a href="{{ route('breeding.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('breeding.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                    <span>Breeding</span>
                </a>
                <a href="{{ route('water-logs.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('water-logs.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="droplet" class="w-4 h-4"></i>
                    <span>Water Logs</span>
                </a>
                <a href="{{ route('feeding.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('feeding.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="utensils" class="w-4 h-4"></i>
                    <span>Feeding</span>
                </a>
                <a href="{{ route('maintenance.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('maintenance.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                    <span>Maintenance</span>
                </a>
            </div>
        </div>

        <!-- Section: BUSINESS -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Business</div>
            <div class="space-y-1">
                <a href="{{ route('inventory.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('inventory.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Inventory</span>
                </a>
                <a href="{{ route('sales.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('sales.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Sales</span>
                </a>
                <a href="{{ route('customers.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('customers.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Customers</span>
                </a>
                <a href="{{ route('expenses.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('expenses.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Expenses</span>
                </a>
            </div>
        </div>

        <!-- Section: PUBLIC STOREFRONT -->
        <div class="pt-2 border-t border-slate-800/80">
            <a href="{{ route('catalog.index') }}" target="_blank"
               class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-all text-cyan-300 bg-cyan-950/40 hover:bg-cyan-900/60 border border-cyan-800/40 group">
                <div class="flex items-center gap-3">
                    <i data-lucide="store" class="w-4 h-4 text-cyan-400"></i>
                    <span>Public Catalog</span>
                </div>
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-cyan-500 group-hover:text-cyan-300 transition"></i>
            </a>
        </div>

        <!-- Section: REPORTS & SETTINGS -->
        <div class="pt-2 border-t border-slate-800/80 space-y-1">
            <a href="{{ route('reports.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('reports.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-all {{ request()->routeIs('profile.*') ? 'bg-cyan-950/70 text-cyan-300 border border-cyan-800/50' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i data-lucide="settings" class="w-4 h-4"></i>
                <span>Settings</span>
            </a>
        </div>
    </nav>

    <!-- Platform Engine Watermark -->
    <div class="px-4 py-2 border-t border-slate-800/60 bg-slate-950/80 text-center">
        <div class="text-[10px] text-slate-500 font-mono tracking-tight flex items-center justify-center gap-1.5">
            <i data-lucide="shield-check" class="w-3 h-3 text-cyan-500/70"></i>
            <span>AquaForge System &bull; rgdlcTech</span>
        </div>
    </div>

    <!-- User Profile Bottom Bar -->
    <div class="p-3 border-t border-slate-800/80 bg-slate-950/40">
        <div class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-slate-900/80 border border-slate-800">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-cyan-900/60 text-cyan-300 flex items-center justify-center font-bold text-xs uppercase border border-cyan-700/50 flex-shrink-0">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="truncate">
                    <div class="text-xs font-semibold text-slate-200 truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'admin@aquaforge.test' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>