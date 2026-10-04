<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AquaForge - Aquatic Farm & Breeding Management System</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    </head>
    <body class="h-full bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-cyan-500 selection:text-white">
        <!-- Top Nav -->
        <header class="max-w-7xl mx-auto w-full px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10 shadow-lg shadow-cyan-950" />
                <div>
                    <div class="text-lg font-bold text-white tracking-wide">AquaForge</div>
                    <div class="text-[11px] text-cyan-400 font-medium">Aquatic Farm & Breeding Management System</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-cyan-950 text-cyan-300 border border-cyan-800/80 hover:bg-cyan-900 transition-all shadow-sm">
                    <i data-lucide="store" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Available Fish Catalog</span>
                </a>

                @if (Route::has('login'))
                    <div class="flex items-center gap-2">
                        @auth
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white transition-all shadow-md shadow-cyan-900/40 flex items-center gap-1.5">
                                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                                    <span>Farm Dashboard</span>
                                </a>
                            @else
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-slate-400 hover:text-white px-2 py-1">
                                        Log Out
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-medium text-slate-300 hover:text-white transition-colors px-2 py-1">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-4xl mx-auto px-6 py-12 text-center flex-1 flex flex-col items-center justify-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-950/80 border border-cyan-800/60 text-cyan-300 text-xs font-medium mb-6">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span>Practical Aquatic Husbandry & Small-Farm System</span>
            </div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Precision Breeding & Farm Management for <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300">Aquatic Hobbyists</span>
            </h1>

            <p class="mt-4 text-base sm:text-lg text-slate-400 max-w-2xl leading-relaxed">
                Track livestock lineage, offspring batches, tank water parameters, feeding, and farm sales.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                <a href="{{ route('catalog.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-sm shadow-xl shadow-cyan-900/40 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="store" class="w-4 h-4"></i>
                    <span>Browse Available Fish Stocklist</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700 font-semibold text-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                    <span>Farm Management Login</span>
                </a>
            </div>

            <!-- Features Highlights Grid -->
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left w-full">
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-cyan-800/50 transition">
                    <i data-lucide="store" class="w-5 h-5 text-cyan-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Live Stocklist & Social Card</div>
                    <div class="text-[11px] text-slate-400 mt-1">Showcase available fish with 1-click Facebook posts and Messenger inquiries.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-teal-800/50 transition">
                    <i data-lucide="qr-code" class="w-5 h-5 text-teal-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Tank QR Stickers & Hub</div>
                    <div class="text-[11px] text-slate-400 mt-1">Printable QR codes to scan right at the tank to log water chemistry & feed.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-indigo-800/50 transition">
                    <i data-lucide="layers" class="w-5 h-5 text-indigo-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Breeding & Batch Fry</div>
                    <div class="text-[11px] text-slate-400 mt-1">Pairing records, fry population counters, survival rate % and quality grading.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-emerald-800/50 transition">
                    <i data-lucide="credit-card" class="w-5 h-5 text-emerald-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">GCash & Maya Invoices</div>
                    <div class="text-[11px] text-slate-400 mt-1">Scan-to-pay QR invoices with 1-click mobile number copy for quick sales.</div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 border-t border-slate-800 text-center text-xs text-slate-500">
            AquaForge &copy; {{ date('Y') }} - Aquatic Farm & Breeding Management System &bull; Built with pride by <span class="text-cyan-400 font-semibold">rgdlcTech</span>
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
