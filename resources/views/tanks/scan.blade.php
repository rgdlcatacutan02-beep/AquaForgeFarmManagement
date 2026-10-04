<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('tanks.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="scan-line" class="w-5 h-5 text-cyan-400"></i>
                    <span>Scan Tank QR Code</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Point camera at any tank sticker to immediately open its hub</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto space-y-4" x-data="tankScanner()" x-init="startScanner('dedicated-qr-reader')">
        <!-- Live Camera Viewfinder Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl overflow-hidden relative">
            <div class="relative w-full aspect-square bg-slate-950 rounded-xl overflow-hidden flex items-center justify-center border border-slate-800">
                <!-- Video Element Target -->
                <div id="dedicated-qr-reader" class="w-full h-full"></div>

                <!-- Laser scanning indicator line -->
                <div x-show="scanning" class="absolute inset-x-8 top-1/2 h-0.5 bg-cyan-400 shadow-[0_0_12px_#22d3ee] animate-pulse pointer-events-none"></div>

                <!-- Corner Viewfinder Overlays -->
                <div class="absolute inset-6 pointer-events-none border-2 border-cyan-400/40 rounded-xl flex flex-col justify-between p-2">
                    <div class="flex justify-between">
                        <div class="w-5 h-5 border-t-2 border-l-2 border-cyan-400 rounded-tl"></div>
                        <div class="w-5 h-5 border-t-2 border-r-2 border-cyan-400 rounded-tr"></div>
                    </div>
                    <div class="flex justify-between">
                        <div class="w-5 h-5 border-b-2 border-l-2 border-cyan-400 rounded-bl"></div>
                        <div class="w-5 h-5 border-b-2 border-r-2 border-cyan-400 rounded-br"></div>
                    </div>
                </div>

                <!-- Loading / Initializing Placeholder -->
                <div x-show="!scanning && !errorMessage" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 gap-3 text-slate-400">
                    <i data-lucide="camera" class="w-10 h-10 text-cyan-400 animate-pulse"></i>
                    <span class="text-xs">Initializing camera feed...</span>
                </div>

                <!-- Error Message Overlay -->
                <div x-show="errorMessage" x-cloak class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/95 p-6 text-center text-rose-300 gap-2">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-400"></i>
                    <p class="text-xs font-medium" x-text="errorMessage"></p>
                    <button @click="startScanner('dedicated-qr-reader')" class="mt-2 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded text-white border border-slate-700">
                        Retry Camera
                    </button>
                </div>

                <!-- Success Match Overlay -->
                <div x-show="successMessage" x-cloak class="absolute inset-0 flex flex-col items-center justify-center bg-emerald-950/95 p-6 text-center text-emerald-200 gap-2">
                    <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400 animate-bounce"></i>
                    <p class="text-sm font-bold" x-text="successMessage"></p>
                    <p class="text-xs font-mono text-emerald-300" x-text="scannedCode"></p>
                </div>
            </div>

            <!-- Scanner Controls -->
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    <span>Rear camera active</span>
                </span>
                <button type="button" @click="stopScanner(); setTimeout(() => startScanner('dedicated-qr-reader'), 300)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors flex items-center gap-1">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Restart</span>
                </button>
            </div>
        </div>

        <!-- Manual Code Lookup Fallback Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 shadow-sm space-y-2">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Can't scan sticker? Enter Tank Code manually</span>
            <form action="{{ route('tanks.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="e.g. T001, T002" required
                    class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white placeholder-slate-500 uppercase font-mono focus:outline-none focus:border-cyan-500">
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span>Find</span>
                </button>
            </form>
        </div>

        <!-- Quick Tank Shortcuts -->
        <div class="text-center text-xs text-slate-500 pt-2">
            <span>Tip: Print tank QR tags from any tank detail page using the <strong>Print QR Tag</strong> button.</span>
        </div>
    </div>
</x-app-layout>
