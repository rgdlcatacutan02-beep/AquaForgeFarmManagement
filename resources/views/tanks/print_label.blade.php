<!DOCTYPE html>
<html lang="en" class="bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Tank QR Label - {{ $tank->tank_code }} ({{ $tank->name }})</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        @media print {
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-sheet {
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }
            .tank-label {
                box-shadow: none !important;
                border: 2px solid black !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="min-h-screen p-4 sm:p-8 bg-slate-950" x-data="{ format: 'single', autoPrint: true }" x-init="if (autoPrint && new URLSearchParams(window.location.search).get('print') === '1') { setTimeout(() => window.print(), 400); }">
    <!-- Non-Printing Navigation & Action Toolbar -->
    <div class="no-print max-w-xl mx-auto mb-6 p-4 rounded-xl bg-slate-900 border border-slate-800 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('tanks.show', $tank) }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Back to Tank Hub">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4 text-cyan-400"></i>
                    <span>Print Tank Sticker Tag</span>
                </h1>
                <p class="text-xs text-slate-400 font-mono">{{ $tank->tank_code }} &bull; {{ $tank->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <!-- Format selector -->
            <select x-model="format" class="bg-slate-950 border border-slate-700 text-xs text-slate-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-cyan-500">
                <option value="single">Single Sticker (4" x 2.5")</option>
                <option value="compact">Compact Tag (3" x 2")</option>
                <option value="sheet">4-Tag Sheet (A4 / Letter)</option>
            </select>

            <button type="button" onclick="window.print()" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print Tag</span>
            </button>
        </div>
    </div>

    <!-- Printable Container -->
    <div class="print-sheet max-w-xl mx-auto flex flex-col items-center justify-center gap-6">
        <!-- Template Tag Macro Component -->
        <template x-for="i in (format === 'sheet' ? 4 : 1)" :key="i">
            <div :class="format === 'compact' ? 'w-[320px] p-3' : 'w-[400px] p-4'"
                 class="tank-label bg-white text-slate-950 rounded-xl border-2 border-slate-900 shadow-2xl relative overflow-hidden transition-all">
                
                <!-- Tag Header: Brand & Tank Code -->
                <div class="flex items-start justify-between border-b-2 border-slate-900 pb-2 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded bg-slate-950 text-white flex items-center justify-center font-black text-xs">
                            <i data-lucide="waves" class="w-4 h-4 text-cyan-400"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black tracking-widest uppercase text-slate-600 leading-none">AQUAFORGE</div>
                            <div class="text-xs font-bold text-slate-900 uppercase">Tank Sticker Tag</div>
                        </div>
                    </div>

                    <!-- High Contrast Tank Code -->
                    <div class="text-right">
                        <div class="text-2xl font-black font-mono tracking-tight text-slate-950 leading-none">
                            {{ $tank->tank_code }}
                        </div>
                        <div class="text-[9px] uppercase tracking-wider font-extrabold text-cyan-700 font-mono mt-0.5">
                            {{ $tank->purpose }}
                        </div>
                    </div>
                </div>

                <!-- Tag Body: QR Code & Specifications -->
                <div class="flex items-center gap-3">
                    <!-- Crisp Scannable SVG QR Code -->
                    <div class="p-1.5 rounded-lg border-2 border-slate-900 bg-white flex-shrink-0 flex items-center justify-center"
                         :class="format === 'compact' ? 'w-24 h-24' : 'w-28 h-28'">
                        <div class="w-full h-full flex items-center justify-center">
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>

                    <!-- Tank Details Column -->
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div>
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block">Tank Name</span>
                            <div class="text-sm font-extrabold text-slate-950 leading-tight truncate">
                                {{ $tank->name }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-1.5 pt-1 text-[11px] font-mono">
                            <div class="bg-slate-100 rounded px-1.5 py-1 border border-slate-200">
                                <span class="text-[8px] font-bold text-slate-500 uppercase block font-sans">Water Vol</span>
                                <span class="font-extrabold text-slate-950">{{ $tank->volume_liters }} L</span>
                                <span class="text-[9px] text-slate-500">({{ $volumeGallons }}g)</span>
                            </div>

                            <div class="bg-slate-100 rounded px-1.5 py-1 border border-slate-200">
                                <span class="text-[8px] font-bold text-slate-500 uppercase block font-sans">Setup Type</span>
                                <span class="font-extrabold text-slate-950 truncate block">{{ $tank->tank_type }}</span>
                            </div>
                        </div>

                        @if($tank->location)
                            <div class="text-[10px] text-slate-600 truncate flex items-center gap-1">
                                <span class="font-bold text-slate-800">Rack:</span>
                                <span>{{ $tank->location }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tag Footer Scan Callout -->
                <div class="mt-3 pt-2 border-t border-dashed border-slate-300 flex items-center justify-between text-[9px] text-slate-500 font-medium">
                    <span class="flex items-center gap-1">
                        <i data-lucide="scan" class="w-3 h-3 text-slate-700"></i>
                        <span>Scan to log Feeding, Water Test & Maint.</span>
                    </span>
                    <span class="font-mono text-slate-400">#{{ $tank->id }}</span>
                </div>
            </div>
        </template>
    </div>

    <!-- Fishroom Print Advice (No Print) -->
    <div class="no-print max-w-xl mx-auto mt-6 text-center text-xs text-slate-400 space-y-1">
        <p>?? <strong>Tip for Fishroom:</strong> Print on waterproof sticker paper or regular paper sealed with clear packaging tape on the glass.</p>
        <p>Scanning this QR code with any smartphone camera instantly opens this tank's water logging and feeding hub.</p>
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
