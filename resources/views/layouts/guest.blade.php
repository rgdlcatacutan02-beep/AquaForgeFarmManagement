<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon & Mobile Icons -->
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AquaForge') }} &bull; Farm Admin Portal</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    </head>
    <body class="min-h-full bg-slate-950 text-slate-100 antialiased flex flex-col justify-center items-center p-6 selection:bg-cyan-500 selection:text-white">
        <div class="w-full max-w-md space-y-6">
            @php
                $adminUser = \App\Models\User::getFarmOwner();
                $farmName = $adminUser?->farm_name ?: 'AquaForge';
                $farmLogo = $adminUser?->farm_logo_url;
            @endphp
            <div class="text-center">
                <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                    @if($farmLogo)
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-600 via-teal-500 to-emerald-400 p-0.5 shadow-xl shadow-cyan-950/60 group-hover:scale-105 transition transform">
                            <img src="{{ $farmLogo }}" alt="{{ $farmName }} Logo" class="w-full h-full object-contain bg-slate-950 rounded-[14px] p-1.5">
                        </div>
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 via-teal-500 to-emerald-400 p-0.5 shadow-xl shadow-cyan-950/60 group-hover:scale-105 transition transform">
                            <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-cyan-400">
                                <i data-lucide="waves" class="w-7 h-7"></i>
                            </div>
                        </div>
                    @endif
                    <div class="mt-2 text-xl font-black text-white tracking-wide">
                        {{ $farmName }}
                    </div>
                </a>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 mt-2 rounded-full bg-slate-900 border border-slate-800 text-[11px] text-cyan-400 font-mono">
                    <i data-lucide="shield-check" class="w-3 h-3 text-cyan-400"></i>
                    <span>Powered by AquaForge &bull; Admin Portal</span>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur">
                {{ $slot }}
            </div>

            <div class="text-center">
                <a href="{{ route('catalog.index') }}" class="text-xs text-slate-500 hover:text-cyan-400 transition flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to Live Fish Catalog</span>
                </a>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        </script>
    </body>
</html>
