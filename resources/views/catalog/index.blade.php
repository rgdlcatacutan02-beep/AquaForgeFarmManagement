<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $owner->farm_name ?? 'AquaForge' }} ? Available Livestock & Breeder Catalog</title>
    
    <!-- OpenGraph for Facebook Sharing -->
    <meta property="og:title" content="{{ $owner->farm_name ?? 'AquaForge' }} ? Live Aquatic Catalog">
    <meta property="og:description" content="Browse our available show & breeder grade live fishes. Direct Messenger inquiry and nationwide shipping available.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|jetbrains-mono:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full flex flex-col bg-slate-950 font-sans antialiased text-slate-200" 
      x-data="{ 
          copiedToast: false,
          fbModalOpen: false,
          guideModalOpen: false,
          copyText(txt) {
              navigator.clipboard.writeText(txt).then(() => {
                  this.copiedToast = true;
                  setTimeout(() => this.copiedToast = false, 3000);
              });
          }
      }">

    <!-- Toast Notification -->
    <div x-show="copiedToast" x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-xl bg-emerald-500 text-slate-950 font-bold text-xs shadow-2xl">
        <i data-lucide="check" class="w-4 h-4"></i>
        <span>Link / Text copied to clipboard! Ready to paste on Facebook!</span>
    </div>

    <!-- TOP NAV / BANNER -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-cyan-950 text-cyan-400 border border-cyan-800 shadow-inner">
                        <i data-lucide="fish" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <div class="text-sm sm:text-base font-extrabold text-white tracking-tight flex items-center gap-2">
                            <span>{{ $owner->farm_name ?? 'AquaForge Farm' }}</span>
                            <span class="text-[10px] font-mono uppercase px-1.5 py-0.5 rounded bg-cyan-950/80 text-cyan-300 border border-cyan-800/60 font-semibold">Live Stock</span>
                        </div>
                        <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-3 h-3 text-cyan-400"></i>
                            <span>{{ $owner->farm_location ?? 'Philippines' }}</span>
                        </p>
                    </div>
                </a>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2">
                @if ($owner && $owner->messenger_url)
                    <a href="{{ $owner->messenger_url }}" target="_blank" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-sm transition">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Chat on Messenger</span>
                        <span class="sm:hidden">Messenger</span>
                    </a>
                @endif
                <button type="button" @click="copyText('{{ route('catalog.index') }}')" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                    <i data-lucide="share-2" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span class="hidden sm:inline">Copy Catalog Link</span>
                </button>
                @auth
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold shadow-sm transition">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Farm Admin</span>
                        </a>
                    @else
                        <div class="flex items-center gap-2 pl-2 border-l border-slate-800">
                            <span class="text-xs text-slate-400 hidden sm:inline">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-medium border border-slate-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium border border-slate-700">
                        <span>Staff Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('notice'))
        <div class="bg-amber-950/80 border-b border-amber-800/80 px-4 py-3 text-amber-300 text-xs flex items-center justify-center gap-2 text-center">
            <i data-lucide="shield-alert" class="w-4 h-4 text-amber-400 flex-shrink-0"></i>
            <span>{{ session('notice') }}</span>
        </div>
    @endif

    <!-- FARM HERO SPOTLIGHT -->
    <div class="relative bg-gradient-to-b from-slate-900 via-slate-900/60 to-slate-950 border-b border-slate-800/80 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-cyan-950/80 text-cyan-300 border border-cyan-800/70 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Direct from Breeder &bull; Live Arrival Guarantee
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Available Live Stocklist & Breeding Specimens
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    Browse our currently available pairs, trios, and breeder specimens. Click any fish to inspect high-resolution photos and send a direct inquiry to our Facebook Messenger.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-300">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-950/50 text-blue-300 border border-blue-800/50">
                        <i data-lucide="credit-card" class="w-3 h-3 text-blue-400"></i>
                        GCash Accepted
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-950/50 text-emerald-300 border border-emerald-800/50">
                        <i data-lucide="check-circle" class="w-3 h-3 text-emerald-400"></i>
                        Maya Accepted
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-purple-950/50 text-purple-300 border border-purple-800/50">
                        <i data-lucide="truck" class="w-3 h-3 text-purple-400"></i>
                        Lalamove &bull; Grab &bull; Busway
                    </span>
                    <button type="button" @click="guideModalOpen = true" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-cyan-800/60 transition">
                        <i data-lucide="book-open" class="w-3 h-3 text-cyan-400"></i>
                        Buyer Care & Acclimation Guide
                    </button>
                </div>
            </div>

            <!-- Share to Facebook Post Box -->
            <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl shadow-lg space-y-2.5 max-w-sm w-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-white flex items-center gap-1.5">
                        <i data-lucide="facebook" class="w-3.5 h-3.5 text-blue-400"></i>
                        Post on Facebook Groups
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">1-Click</span>
                </div>
                <p class="text-[11px] text-slate-400">
                    Selling on FB aquarium groups? Copy our pre-formatted post with direct Messenger and stocklist links!
                </p>
                <button type="button" @click="fbModalOpen = true" 
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span>Generate Facebook Post</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN CATALOG CONTAINER -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- SEARCH & FILTER CONTROLS -->
        <form method="GET" action="{{ route('catalog.index') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 shadow-sm space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <!-- Search input -->
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Search Variety or Code</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="e.g. Albino Full Red, Dumbo Ear, Discus..." 
                               class="w-full rounded-lg bg-slate-950 border border-slate-700 text-xs text-white pl-8 pr-3 py-2 focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5"></i>
                    </div>
                </div>

                <!-- Species filter -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Species</label>
                    <select name="species_id" class="w-full rounded-lg bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                        <option value="">All Species</option>
                        @foreach ($speciesList as $sp)
                            <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Grade filter -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Quality Grade</label>
                    <select name="grade" class="w-full rounded-lg bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                        <option value="">All Grades</option>
                        <option value="SHOW" {{ request('grade') === 'SHOW' ? 'selected' : '' }}>Show Grade</option>
                        <option value="BREEDER" {{ request('grade') === 'BREEDER' ? 'selected' : '' }}>Breeder Grade</option>
                        <option value="COMMERCIAL" {{ request('grade') === 'COMMERCIAL' ? 'selected' : '' }}>Commercial Grade</option>
                        <option value="CULL" {{ request('grade') === 'CULL' ? 'selected' : '' }}>Hobby / Pet</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-[11px] font-medium text-slate-400 mb-1">Sort By</label>
                    <select name="sort" class="w-full rounded-lg bg-slate-950 border border-slate-700 text-xs text-white px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Listed</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                <span class="text-xs text-slate-400">
                    Showing <strong class="text-white">{{ $livestock->total() }}</strong> available specimen(s)
                </span>
                <div class="flex items-center gap-2">
                    @if (request()->hasAny(['search', 'species_id', 'grade', 'sort']))
                        <a href="{{ route('catalog.index') }}" class="text-xs text-slate-400 hover:text-slate-200">Reset Filters</a>
                    @endif
                    <button type="submit" class="px-4 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        Apply Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- LIVESTOCK CARDS GRID -->
        @if ($livestock->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($livestock as $fish)
                    @php
                        $inquiryMessage = "Hi! I'm interested in buying: " . ($fish->variety ?: $fish->species?->name) . " (Code: {$fish->livestock_code}, ?" . number_format($fish->purchase_price, 2) . ") listed on your AquaForge catalog: " . route('catalog.show', $fish);
                        $messengerLink = $owner && $owner->messenger_username 
                            ? "https://m.me/" . ltrim($owner->messenger_username, '@') . "?text=" . urlencode($inquiryMessage)
                            : "#";
                    @endphp
                    <div class="group flex flex-col rounded-xl bg-slate-900 border border-slate-800 hover:border-cyan-700/60 overflow-hidden shadow-lg transition-all hover:shadow-cyan-950/20">
                        <!-- Photo container -->
                        <div class="relative aspect-square w-full bg-slate-950 overflow-hidden">
                            @if ($fish->photo_url)
                                <img src="{{ $fish->photo_url }}" alt="{{ $fish->variety }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-900 to-slate-950 text-slate-700">
                                    <i data-lucide="fish" class="w-12 h-12 mb-1"></i>
                                    <span class="text-[10px] uppercase font-mono tracking-wider text-slate-600">No Photo Uploaded</span>
                                </div>
                            @endif

                            <!-- Grade badge -->
                            <div class="absolute top-2.5 left-2.5">
                                @if ($fish->grade === 'SHOW')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950 shadow-md">
                                        ? SHOW GRADE
                                    </span>
                                @elseif ($fish->grade === 'BREEDER')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500 text-white shadow-md">
                                        BREEDER GRADE
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800/90 text-slate-300 border border-slate-700 backdrop-blur">
                                        {{ $fish->grade ?: 'STANDARD' }}
                                    </span>
                                @endif
                            </div>

                            <!-- Sex / Package badge -->
                            <div class="absolute top-2.5 right-2.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-950/80 text-cyan-300 border border-cyan-800/60 backdrop-blur">
                                    {{ $fish->sex }}
                                </span>
                            </div>

                            <!-- Tank code overlay -->
                            @if ($fish->tank)
                                <div class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded bg-slate-950/80 text-[10px] font-mono text-slate-400 border border-slate-800 backdrop-blur">
                                    Tank {{ $fish->tank->tank_code }}
                                </div>
                            @endif
                        </div>

                        <!-- Card details -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                                    <span>{{ $fish->species?->name ?? 'Fish' }}</span>
                                    <span class="font-mono text-cyan-400">{{ $fish->livestock_code }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-white group-hover:text-cyan-300 transition-colors line-clamp-1">
                                    {{ $fish->variety ?: $fish->species?->name }}
                                </h3>
                                @if ($fish->notes)
                                    <p class="text-[11px] text-slate-400 mt-1 line-clamp-2">{{ $fish->notes }}</p>
                                @endif
                            </div>

                            <!-- Price & Messenger Action -->
                            <div class="pt-3 border-t border-slate-800/80 space-y-2.5">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-[10px] uppercase font-bold text-slate-400">Asking Price</span>
                                    <span class="text-base font-extrabold text-emerald-400 font-mono">
                                        ?{{ number_format($fish->purchase_price, 2) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    @if ($owner && $owner->messenger_username)
                                        <a href="{{ $messengerLink }}" target="_blank" 
                                           class="flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                            <span>Inquire</span>
                                        </a>
                                    @else
                                        <button type="button" @click="copyText('{{ route('catalog.show', $fish) }}')" 
                                                class="flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            <span>Copy Link</span>
                                        </button>
                                    @endif

                                    <a href="{{ route('catalog.show', $fish) }}" 
                                       class="flex items-center justify-center gap-1 px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium border border-slate-700 transition">
                                        <span>Details</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $livestock->links() }}
            </div>
        @else
            <div class="py-16 text-center rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                <i data-lucide="fish-off" class="w-12 h-12 text-slate-600 mx-auto"></i>
                <h3 class="text-base font-bold text-white">No Available Livestock Found</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Try adjusting your filters or check back soon as our breeding pairs regularly drop new fry batches.
                </p>
                @if (request()->hasAny(['search', 'species_id', 'grade', 'sort']))
                    <a href="{{ route('catalog.index') }}" class="inline-block px-4 py-2 bg-slate-800 text-cyan-400 text-xs font-semibold rounded-lg hover:bg-slate-700">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif

        <!-- BULK & GROW-OUT BATCHES SECTION -->
        @if ($batches->isNotEmpty())
            <div class="pt-8 border-t border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="layers" class="w-4 h-4 text-indigo-400"></i>
                            Grow-out & Wholesale Batches Available
                        </h2>
                        <p class="text-xs text-slate-400">Looking for community colonies, schools, or wholesale lots? Inquire about active batches.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($batches as $b)
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-indigo-400">{{ $b->batch_code }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800/60 font-semibold">
                                    {{ $b->current_count }} pcs available
                                </span>
                            </div>
                            <div class="text-xs font-bold text-white">{{ $b->variety ?: $b->species?->name }}</div>
                            <div class="text-[11px] text-slate-400">
                                Tank: <span class="text-cyan-300 font-mono">{{ $b->tank?->tank_code ?? 'N/A' }}</span>
                                &bull; Stage: <span class="text-slate-300">{{ $b->stage }}</span>
                            </div>
                            @if ($owner && $owner->messenger_username)
                                <a href="https://m.me/{{ ltrim($owner->messenger_username, '@') }}?text={{ urlencode('Hi! I am inquiring about wholesale batch: ' . ($b->variety ?: $b->species?->name) . ' (' . $b->batch_code . ', count: ' . $b->current_count . ' pcs) from your AquaForge catalog') }}" 
                                   target="_blank" 
                                   class="mt-2 w-full flex items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-indigo-950/70 hover:bg-indigo-900 text-indigo-300 border border-indigo-800/60 text-xs font-semibold transition">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                    <span>Inquire Wholesale</span>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FARM INFORMATION & POLICIES FOOTER -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
            <div class="space-y-2">
                <div class="font-bold text-white flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                    <span>Live Arrival Guarantee (DOA)</span>
                </div>
                <p class="text-slate-400 text-[11px] leading-relaxed">
                    {{ $owner->shipping_notes ?? 'We take immense pride in healthy quarantine and oxygenated double-bag packing. In case of DOA, provide a clear, uncut unboxing video within 1 hour of delivery for replacement or credit.' }}
                </p>
            </div>

            <div class="space-y-2">
                <div class="font-bold text-white flex items-center gap-2">
                    <i data-lucide="truck" class="w-4 h-4 text-purple-400"></i>
                    <span>Delivery & Logistics</span>
                </div>
                <p class="text-slate-400 text-[11px] leading-relaxed">
                    Same-day delivery via Lalamove / Grab Express for Metro Manila and nearby provinces. Provincial orders shipped via Bus Terminal-to-Terminal (Victory Liner, Partas, etc.) or Cargo.
                </p>
            </div>

            <div class="space-y-2">
                <div class="font-bold text-white flex items-center gap-2">
                    <i data-lucide="credit-card" class="w-4 h-4 text-cyan-400"></i>
                    <span>Payment Methods</span>
                </div>
                <p class="text-slate-400 text-[11px] leading-relaxed">
                    Fast & secure cashless payments via <strong>GCash</strong>, <strong>Maya</strong>, or Bank Transfer (BDO / BPI / UnionBank). Scan-to-pay QR codes provided upon order confirmation.
                </p>
            </div>
        </div>
    </main>

    <!-- MODAL: Generate Facebook Group Post -->
    <div x-show="fbModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm">
        <div @click.away="fbModalOpen = false" class="bg-slate-900 border border-slate-800 rounded-xl p-5 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="facebook" class="w-4 h-4 text-blue-400"></i>
                    Ready-to-Post Facebook Group Template
                </h3>
                <button @click="fbModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <p class="text-xs text-slate-400">
                Copy this formatted caption and paste it directly into your favorite Facebook aquarium and fish hobbyist groups!
            </p>

            @php
                $catalogUrl = route('catalog.index');
                $messengerUrl = $owner && $owner->messenger_username ? "https://m.me/" . ltrim($owner->messenger_username, '@') : "Contact via Messenger";
                $farmName = $owner->farm_name ?? "AquaForge Aquatic Farm";
                $location = $owner->farm_location ?? "Philippines";
                
                $fbPostText = "?? AVAILABLE LIVE STOCKLIST ? {$farmName} ??\n\n"
                    . "?? Location: {$location}\n"
                    . "?? Shipping: Lalamove / Grab Express / Busway Cargo\n"
                    . "?? Payments: GCash / Maya / Bank Transfer\n\n"
                    . "?? BROWSE FULL LIVE CATALOG WITH HD PHOTOS & PRICES:\n"
                    . "{$catalogUrl}\n\n"
                    . "?? DIRECT MESSENGER INQUIRY:\n"
                    . "{$messengerUrl}\n\n"
                    . "#AquaForge #FishKeepingPH #GuppyPH #BettaPH #AquariumPH #LiveFishPhilippines";
            @endphp

            <textarea readonly rows="9" class="w-full rounded-lg bg-slate-950 border border-slate-700 text-xs font-mono text-slate-200 p-3 leading-relaxed focus:ring-0">{{ $fbPostText }}</textarea>

            <div class="flex items-center justify-between pt-2">
                <span class="text-[11px] text-slate-500">Includes your catalog link & Messenger deep link</span>
                <div class="flex gap-2">
                    <button type="button" @click="fbModalOpen = false" class="px-3 py-1.5 bg-slate-800 text-slate-300 text-xs rounded-lg hover:bg-slate-700">Close</button>
                    <button type="button" @click="copyText(`{{ addslashes($fbPostText) }}`); fbModalOpen = false" 
                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-lg flex items-center gap-1.5 shadow-sm transition">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span>Copy Post Text</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="border-t border-slate-900 bg-slate-950 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $owner->farm_name ?? 'AquaForge' }}. Managed with AquaForge Aquatic Farm & Breeding Management System.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>

    <!-- MODAL: Buyer Acclimation & Care Guide -->
    <div x-show="guideModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md overflow-y-auto">
        <div @click.away="guideModalOpen = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-cyan-950 text-cyan-400 border border-cyan-800">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                    </span>
                    <h3 class="text-base font-bold text-white">Buyer Care & Acclimation Guide</h3>
                </div>
                <button @click="guideModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="space-y-4 text-xs text-slate-300 leading-relaxed max-h-[65vh] overflow-y-auto pr-2">
                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                    <h4 class="font-bold text-cyan-300 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        Step 1: Temperature Acclimation (Float Bag 15-20 Mins)
                    </h4>
                    <p class="text-slate-400">
                        Float the unopened sealed fish bag in your prepared aquarium for 15 to 20 minutes so the water temperature equalizes gradually. Avoid placing under direct strong aquarium lights.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                    <h4 class="font-bold text-cyan-300 flex items-center gap-1.5">
                        <i data-lucide="droplet" class="w-3.5 h-3.5"></i>
                        Step 2: Water Chemistry Equalization (Drip / Cup Method)
                    </h4>
                    <p class="text-slate-400">
                        Open the bag and slowly add a small cup (about 20-30ml) of your tank water into the bag every 5 minutes for 20 minutes. This prevents osmotic and pH shock.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                    <h4 class="font-bold text-cyan-300 flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        Step 3: Gentle Release (Do Not Pour Bag Water)
                    </h4>
                    <p class="text-slate-400">
                        Gently net your fish out of the shipping bag and release them into your aquarium. Discard the shipping bag water in the sink.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                    <h4 class="font-bold text-amber-300 flex items-center gap-1.5">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        First 24 Hours Protocol
                    </h4>
                    <p class="text-slate-400">
                        Keep aquarium lights dimmed or turned off for the first 12 hours to reduce transport stress. <strong>Do not feed for the first 12-24 hours</strong> until the fish has fully settled.
                    </p>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end">
                <button type="button" @click="guideModalOpen = false" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold rounded-lg shadow-sm">
                    Got it, thanks!
                </button>
            </div>
        </div>
    </div>

</body>
</html>
