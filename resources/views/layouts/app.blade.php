<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AquaForge') }} - {{ $title ?? 'Aquatic Farm & Breeding Management System' }}</title>

        <!-- PWA Manifest & Mobile Web App Meta -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0891b2">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="AquaForge">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">
        <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="h-full bg-slate-950 text-slate-100 antialiased" x-data="{ mobileNavOpen: false, ...tankScanner() }">
        <div class="flex h-screen overflow-hidden">
            <!-- Desktop Sidebar -->
            <div class="hidden md:flex md:flex-shrink-0">
                @include('layouts.sidebar')
            </div>

            <!-- Mobile Off-Canvas Drawer -->
            <div x-show="mobileNavOpen" 
                 x-cloak 
                 class="relative z-50 md:hidden" 
                 role="dialog" 
                 aria-modal="true">
                <!-- Backdrop -->
                <div x-show="mobileNavOpen" 
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="mobileNavOpen = false" 
                     class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

                <!-- Drawer Content -->
                <div class="fixed inset-0 flex z-50">
                    <div x-show="mobileNavOpen"
                         x-transition:enter="transition ease-in-out duration-200 transform"
                         x-transition:enter-start="-translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in-out duration-200 transform"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="-translate-x-full"
                         class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-900 shadow-2xl">
                        <!-- Close button -->
                        <div class="absolute top-3 right-3">
                            <button @click="mobileNavOpen = false" class="p-2 text-slate-400 hover:text-white rounded-lg focus:outline-none">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        @include('layouts.sidebar')
                    </div>
                </div>
            </div>

            <!-- Main Content Container -->
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden bg-slate-950">
                <!-- Top Header -->
                <header class="h-16 flex items-center justify-between px-4 sm:px-6 border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <!-- Mobile Hamburger Button -->
                        <button type="button" 
                                @click="mobileNavOpen = true" 
                                class="p-2 text-slate-400 hover:text-white md:hidden rounded-lg hover:bg-slate-800">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <div>
                            @if (isset($header))
                                {{ $header }}
                            @else
                                <div class="text-lg font-bold text-white tracking-tight">AquaForge</div>
                            @endif
                        </div>
                    </div>

                    <!-- Top Right Quick Actions & Status -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Camera Quick Scan Button -->
                        <button type="button" 
                                @click="scannerModalOpen = true" 
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-300 border border-cyan-800/50 transition-all flex items-center gap-1.5 shadow-sm"
                                title="Open Camera to Scan Tank QR">
                            <i data-lucide="qr-code" class="w-4 h-4 text-cyan-400"></i>
                            <span class="hidden sm:inline">Scan Tank</span>
                        </button>

                        <div class="hidden lg:flex items-center gap-2 text-xs text-slate-400 bg-slate-900 px-3 py-1.5 rounded-full border border-slate-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Online</span>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="p-2 text-slate-400 hover:text-cyan-400 rounded-lg hover:bg-slate-800/80 transition-colors" title="Settings">
                            <i data-lucide="settings" class="w-5 h-5"></i>
                        </a>
                    </div>
                </header>

                <!-- Page Body -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-950 text-slate-100">
                    <!-- Session Flash Messages -->
                    @if (session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-950/40 border border-emerald-800/60 text-emerald-300 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400 flex-shrink-0"></i>
                            <div class="text-sm font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-rose-950/40 border border-rose-800/60 text-rose-300 flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400 flex-shrink-0"></i>
                            <div class="text-sm font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Global In-Browser Camera QR Scanner Modal -->
        <div x-show="scannerModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
             @keydown.escape.window="scannerModalOpen = false">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-sm w-full p-5 shadow-2xl space-y-4 text-center relative"
                 @click.away="scannerModalOpen = false">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="qr-code" class="w-4 h-4 text-cyan-400"></i>
                        <span>Scan Tank Sticker</span>
                    </h3>
                    <button @click="scannerModalOpen = false" class="p-1 rounded text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="relative w-full aspect-square bg-slate-950 rounded-xl overflow-hidden flex items-center justify-center border border-slate-800">
                    <div id="qr-reader" class="w-full h-full"></div>
                    
                    <!-- Scanner Laser Beam -->
                    <div x-show="scanning" class="absolute inset-x-6 top-1/2 h-0.5 bg-cyan-400 shadow-[0_0_12px_#22d3ee] animate-pulse pointer-events-none"></div>

                    <!-- Success feedback -->
                    <div x-show="successMessage" x-cloak class="absolute inset-0 flex flex-col items-center justify-center bg-emerald-950/95 p-4 text-emerald-200">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 animate-bounce mb-2"></i>
                        <p class="text-xs font-bold" x-text="successMessage"></p>
                    </div>

                    <!-- Error feedback -->
                    <div x-show="errorMessage" x-cloak class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/95 p-4 text-rose-300">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-rose-400 mb-1"></i>
                        <p class="text-xs" x-text="errorMessage"></p>
                        <a href="{{ route('tanks.scan') }}" class="mt-2 text-[11px] underline text-cyan-400">Try Dedicated Scan Page</a>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <a href="{{ route('tanks.scan') }}" class="text-cyan-400 hover:underline">Full-screen scanner</a>
                    <button type="button" @click="scannerModalOpen = false" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.createIcons) {
                    window.createIcons();
                }
            });
        </script>
    </body>
</html>
