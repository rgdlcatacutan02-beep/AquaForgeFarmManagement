<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $owner->farm_name ?? 'AquaForge' }} &bull; Aquatic Farm &amp; Live Fish Catalog</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    </head>
    <body class="min-h-full bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-cyan-500 selection:text-white">
        
        <!-- Top Navigation (Clean Public View - No Register/Login buttons) -->
        <header class="max-w-7xl mx-auto w-full px-6 py-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                @if($owner && $owner->farm_logo_url)
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-600 via-teal-500 to-emerald-400 p-0.5 shadow-lg shadow-cyan-950/60 group-hover:scale-105 transition transform flex-shrink-0">
                        <img src="{{ $owner->farm_logo_url }}" alt="{{ $owner->farm_name ?? 'Farm' }} Logo" class="w-full h-full object-contain rounded-[14px] bg-slate-950 p-1">
                    </div>
                @else
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-600 via-teal-500 to-emerald-400 p-0.5 shadow-lg shadow-cyan-950/60 group-hover:scale-105 transition transform flex-shrink-0">
                        <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-cyan-400">
                            <i data-lucide="waves" class="w-6 h-6"></i>
                        </div>
                    </div>
                @endif
                <div>
                    <div class="text-lg font-black text-white tracking-wide group-hover:text-cyan-300 transition-colors">
                        {{ $owner->farm_name ?? 'AquaForge Farm' }}
                    </div>
                    <div class="text-[11px] text-cyan-400 font-semibold flex items-center gap-2">
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3 text-cyan-400"></i>
                            <span>{{ $owner->farm_location ?? 'Philippines' }}</span>
                        </span>
                        <span class="text-slate-600">&bull;</span>
                        <span class="text-[10px] text-cyan-300/80 font-mono uppercase tracking-wider">Powered by AquaForge</span>
                    </div>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('catalog.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-500 hover:from-cyan-500 hover:to-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-950 transition transform hover:-translate-y-0.5">
                    <i data-lucide="store" class="w-4 h-4 text-slate-950"></i>
                    <span>Browse Fish Catalog</span>
                </a>

                @auth
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-cyan-300 border border-cyan-800/60 transition shadow-sm">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-cyan-400"></i>
                            <span>Farm Dashboard</span>
                        </a>
                    @endif
                @endauth
            </div>
        </header>

        <!-- Main Branded Hero Section -->
        <main class="max-w-5xl mx-auto px-6 py-10 flex-1 flex flex-col items-center justify-center text-center">
            
            <!-- Farm Logo Crest -->
            <div class="relative mb-6">
                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl bg-gradient-to-tr from-cyan-500 via-teal-400 to-emerald-400 p-1 shadow-2xl shadow-cyan-500/20">
                    <div class="w-full h-full bg-slate-950 rounded-[22px] flex flex-col items-center justify-center text-cyan-400 p-3 overflow-hidden">
                        @if($owner && $owner->farm_logo_url)
                            <img src="{{ $owner->farm_logo_url }}" alt="{{ $owner->farm_name ?? 'Farm' }} Logo" class="w-full h-full object-contain p-1">
                        @else
                            <i data-lucide="fish" class="w-12 h-12 text-cyan-400"></i>
                            <span class="text-[9px] font-black tracking-widest text-teal-300 uppercase mt-1">EST. 2026</span>
                        @endif
                    </div>
                </div>
                <div class="absolute -bottom-2 inset-x-0 flex justify-center">
                    <span class="px-3 py-0.5 rounded-full bg-emerald-950 border border-emerald-500 text-emerald-300 text-[10px] font-extrabold uppercase tracking-wider shadow flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Stock Available
                    </span>
                </div>
            </div>

            <!-- Platform Powered-by pill -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/90 border border-cyan-500/30 text-cyan-300 text-xs font-semibold mb-4 shadow-sm">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>Powered by AquaForge System</span>
            </div>

            <!-- Farm Branding Titles -->
            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-tight mt-3">
                {{ $owner->farm_name ?? 'AquaForge Aquatic Farm' }}
            </h1>

            <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed font-medium">
                Premium captive-bred aquatic specimens, quality show strains, and conditioned breeders from our farm in 
                <span class="text-cyan-400 font-bold">{{ $owner->farm_location ?? 'the Philippines' }}</span>.
            </p>

            <!-- Customer Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                <a href="{{ route('catalog.index') }}" 
                   class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-500 to-teal-400 hover:from-cyan-400 hover:to-teal-300 text-slate-950 font-black text-sm shadow-xl shadow-cyan-500/25 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2.5">
                    <i data-lucide="shopping-cart" class="w-5 h-5 text-slate-950"></i>
                    <span>Browse Available Fish Stocklist &amp; Prices</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>

                @if ($owner && $owner->messenger_url)
                    <a href="{{ $owner->messenger_url }}" target="_blank" 
                       class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-blue-600/90 hover:bg-blue-600 text-white font-bold text-sm shadow-xl shadow-blue-900/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span>Direct Facebook Messenger Inquiry</span>
                    </a>
                @endif
            </div>

            <!-- Featured Stocklist Teaser (If fish available) -->
            @if(isset($featuredFish) && $featuredFish->isNotEmpty())
                <div class="mt-16 w-full text-left">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="sparkles" class="w-4 h-4 text-cyan-400"></i>
                            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Recently Added Specimens</h2>
                        </div>
                        <a href="{{ route('catalog.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1">
                            <span>View All ({{ $featuredFish->count() }}+ Available)</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach ($featuredFish as $fish)
                            <a href="{{ route('catalog.show', $fish) }}" class="group block rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/50 p-3 transition shadow-lg">
                                <div class="aspect-square rounded-xl bg-slate-950 overflow-hidden mb-2.5 relative">
                                    @if ($fish->photo_url)
                                        <img src="{{ $fish->photo_url }}" alt="{{ $fish->variety }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-700">
                                            <i data-lucide="fish" class="w-8 h-8"></i>
                                        </div>
                                    @endif
                                    <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-slate-950/80 backdrop-blur text-[10px] font-mono font-bold text-cyan-300 border border-slate-800">
                                        {{ $fish->livestock_code }}
                                    </span>
                                </div>
                                <div class="text-xs font-bold text-white group-hover:text-cyan-300 truncate">
                                    {{ $fish->variety ?: $fish->species?->name }}
                                </div>
                                <div class="flex items-center justify-between text-[11px] mt-1">
                                    <span class="font-mono text-emerald-400 font-extrabold">&#8369;{{ number_format($fish->purchase_price, 2) }}</span>
                                    <span class="text-slate-400 text-[10px] font-medium">{{ $fish->sex }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Customer Trust & Delivery Assurances -->
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left w-full">
                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-cyan-800/50 transition">
                    <div class="w-8 h-8 rounded-lg bg-cyan-950 text-cyan-400 flex items-center justify-center mb-2.5">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-bold text-white">Live Arrival Guarantee</div>
                    <div class="text-[11px] text-slate-400 mt-1">Healthy fish conditioned and packed with pure oxygen for safe transit.</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-teal-800/50 transition">
                    <div class="w-8 h-8 rounded-lg bg-teal-950 text-teal-400 flex items-center justify-center mb-2.5">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-bold text-white">Metro &amp; Provincial Cargo</div>
                    <div class="text-[11px] text-slate-400 mt-1">Same-day via Lalamove / Grab Express or provincial bus terminal cargo.</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-indigo-800/50 transition">
                    <div class="w-8 h-8 rounded-lg bg-indigo-950 text-indigo-400 flex items-center justify-center mb-2.5">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-bold text-white">WYSIWYG Photos</div>
                    <div class="text-[11px] text-slate-400 mt-1">What You See Is What You Get. Real individual photos of active specimens.</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-emerald-800/50 transition">
                    <div class="w-8 h-8 rounded-lg bg-emerald-950 text-emerald-400 flex items-center justify-center mb-2.5">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs font-bold text-white">GCash &amp; Maya Scan-to-Pay</div>
                    <div class="text-[11px] text-slate-400 mt-1">Verified payments processed after live order confirmation.</div>
                </div>
            </div>
        </main>

        <!-- Footer with subtle Admin Portal Access -->
        <footer class="py-6 border-t border-slate-900 bg-slate-950/80 text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    {{ $owner->farm_name ?? 'AquaForge' }} &copy; {{ date('Y') }} &bull; Aquatic Farm & Breeding Management System &bull; Powered by <span class="text-cyan-400 font-semibold">AquaForge System</span> &bull; Built with pride by <span class="text-cyan-400 font-semibold">rgdlcTech</span>
                </div>

                <!-- Hidden Admin Portal Access -->
                <div>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-1.5 text-[11px] text-slate-700 hover:text-slate-400 transition" 
                       title="Restricted Farm Admin Access">
                        <i data-lucide="lock" class="w-3 h-3 text-slate-600"></i>
                        <span>Admin Portal</span>
                    </a>
                </div>
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        </script>
    </body>
</html>
