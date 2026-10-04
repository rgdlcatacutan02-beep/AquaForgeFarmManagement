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
            <div>
                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white transition-all shadow-md shadow-cyan-900/40">
                                Open Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-medium text-slate-300 hover:text-white transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700 transition-all">
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
                Purpose-built for Guppies, Mollies, Flowerhorn, and Crayfish. Track livestock lineage, offspring batches, tank water parameters, feeding, and farm sales.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-sm shadow-xl shadow-cyan-900/40 transition-all flex items-center justify-center gap-2">
                    <span>Enter AquaForge</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                    <span>Demo Account: admin@aquaforge.test / password</span>
                </div>
            </div>

            <!-- Features Highlights Grid -->
            <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-4 text-left w-full">
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <i data-lucide="fish" class="w-5 h-5 text-cyan-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Livestock & Grading</div>
                    <div class="text-[11px] text-slate-400 mt-1">Individual fish and breeding pairs.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <i data-lucide="layers" class="w-5 h-5 text-teal-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Batch Fry Tracking</div>
                    <div class="text-[11px] text-slate-400 mt-1">Track fry counts, mortality & culls.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <i data-lucide="box" class="w-5 h-5 text-indigo-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Tank QR Hub</div>
                    <div class="text-[11px] text-slate-400 mt-1">Scan at the tank to log water & feed.</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-amber-400 mb-2"></i>
                    <div class="text-xs font-bold text-white">Sales & Husbandry</div>
                    <div class="text-[11px] text-slate-400 mt-1">Turn breeding hobby into operation.</div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 border-t border-slate-800 text-center text-xs text-slate-500">
            AquaForge &copy; {{ date('Y') }} - Aquatic Farm & Breeding Management System
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.createIcons) { window.createIcons(); }
            });
        </script>
    </body>
</html>