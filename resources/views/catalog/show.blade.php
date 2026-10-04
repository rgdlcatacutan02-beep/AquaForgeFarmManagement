<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $livestock->variety ?: $livestock->species?->name }} ({{ $livestock->livestock_code }}) ? {{ $owner->farm_name ?? 'AquaForge' }}</title>
    
    <!-- OpenGraph for Facebook Link Previews -->
    <meta property="og:title" content="{{ $livestock->variety ?: $livestock->species?->name }} [{{ $livestock->grade ?? 'Breeder Grade' }}] ? ?{{ number_format($livestock->purchase_price, 2) }}">
    <meta property="og:description" content="Available live specimen at {{ $owner->farm_name ?? 'AquaForge' }}. Sex: {{ $livestock->sex }}. Click to inspect high-resolution photos and message breeder directly on Messenger.">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($livestock->photo_url)
        <meta property="og:image" content="{{ $livestock->photo_url }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|jetbrains-mono:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full flex flex-col bg-slate-950 font-sans antialiased text-slate-200"
      x-data="{
          activePhoto: '{{ $livestock->photo_url ?? '' }}',
          copiedToast: false,
          copyText(txt) {
              navigator.clipboard.writeText(txt).then(() => {
                  this.copiedToast = true;
                  setTimeout(() => this.copiedToast = false, 3000);
              });
          }
      }">

    <!-- Toast Notification -->
    <div x-show="copiedToast" x-cloak 
         class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-xl bg-emerald-500 text-slate-950 font-bold text-xs shadow-2xl">
        <i data-lucide="check" class="w-4 h-4"></i>
        <span>Link / Text copied to clipboard! Ready to paste into Facebook!</span>
    </div>

    <!-- HEADER -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white transition">
                <i data-lucide="arrow-left" class="w-4 h-4 text-cyan-400"></i>
                <span>Back to Full Stocklist</span>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" @click="copyText('{{ url()->current() }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                    <i data-lucide="share-2" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Share Fish</span>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN PRODUCT / SPECIMEN DISPLAY -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            <!-- PHOTOS COLUMN -->
            <div class="space-y-4">
                <!-- Main Featured Photo -->
                <div class="relative aspect-square w-full rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-2xl">
                    <template x-if="activePhoto">
                        <img :src="activePhoto" alt="{{ $livestock->variety }}" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!activePhoto">
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-600">
                            <i data-lucide="fish" class="w-16 h-16 mb-2"></i>
                            <span class="text-xs">No Photo Available</span>
                        </div>
                    </template>

                    <!-- Grade Badge -->
                    <div class="absolute top-4 left-4">
                        @if ($livestock->grade === 'SHOW')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-slate-950 shadow-lg">
                                ? SHOW GRADE
                            </span>
                        @elseif ($livestock->grade === 'BREEDER')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500 text-white shadow-lg">
                                BREEDER GRADE
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-900/90 text-slate-200 border border-slate-700 backdrop-blur">
                                {{ $livestock->grade ?: 'STANDARD' }}
                            </span>
                        @endif
                    </div>

                    <!-- Sex Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-slate-950/90 text-cyan-300 border border-cyan-800/80 backdrop-blur">
                            {{ $livestock->sex }}
                        </span>
                    </div>
                </div>

                <!-- Gallery Thumbnails (Including Tank Photos) -->
                @php
                    $allPhotos = collect();
                    if ($livestock->photo_path) {
                        $allPhotos->push(['url' => $livestock->photo_url, 'caption' => 'Profile Avatar']);
                    }
                    if ($livestock->tankPhotos) {
                        foreach ($livestock->tankPhotos as $tp) {
                            if ($tp->photo_path !== $livestock->photo_path) {
                                $allPhotos->push(['url' => $tp->photo_url, 'caption' => $tp->caption]);
                            }
                        }
                    }
                @endphp

                @if ($allPhotos->count() > 1)
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Photo Gallery (Click to inspect)</div>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($allPhotos as $p)
                                <button type="button" @click="activePhoto = '{{ $p['url'] }}'" 
                                        :class="activePhoto === '{{ $p['url'] }}' ? 'border-cyan-500 ring-2 ring-cyan-500/50' : 'border-slate-800 opacity-70 hover:opacity-100'"
                                        class="aspect-square rounded-lg bg-slate-900 border overflow-hidden transition">
                                    <img src="{{ $p['url'] }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- SPECS & INQUIRY COLUMN -->
            <div class="space-y-6">
                <div>
                    <div class="flex items-center gap-2 text-xs font-mono text-cyan-400 mb-1">
                        <span>{{ $livestock->species?->name }}</span>
                        <span>&bull;</span>
                        <span>Code: {{ $livestock->livestock_code }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        {{ $livestock->variety ?: $livestock->species?->name }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Currently conditioned in <span class="text-cyan-300 font-mono font-semibold">{{ $livestock->tank?->tank_code ?? 'Main Rack' }}</span> at {{ $owner->farm_location ?? 'Farm' }}.
                    </p>
                </div>

                <!-- Price Box -->
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-1">
                    <span class="text-[11px] uppercase font-bold text-slate-400">Asking Price</span>
                    <div class="text-3xl font-extrabold text-emerald-400 font-mono">
                        ?{{ number_format($livestock->purchase_price, 2) }}
                    </div>
                    <p class="text-[11px] text-slate-500">Price is in Philippine Peso. Delivery / shipping fee calculated upon booking.</p>
                </div>

                <!-- Spec Table -->
                <div class="rounded-xl bg-slate-900 border border-slate-800 divide-y divide-slate-800/80 text-xs">
                    <div class="p-3 flex items-center justify-between">
                        <span class="text-slate-400">Species</span>
                        <span class="font-semibold text-white">{{ $livestock->species?->name }}</span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <span class="text-slate-400">Sex / Package</span>
                        <span class="font-semibold text-cyan-300 font-mono">{{ $livestock->sex }}</span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <span class="text-slate-400">Quality Classification</span>
                        <span class="font-semibold text-amber-400">{{ $livestock->grade ?? 'Breeder Grade' }}</span>
                    </div>
                    @if ($livestock->date_of_birth)
                        <div class="p-3 flex items-center justify-between">
                            <span class="text-slate-400">Age / Hatch Date</span>
                            <span class="text-white">{{ $livestock->date_of_birth->format('M d, Y') }} ({{ $livestock->date_of_birth->diffInMonths() }} mos)</span>
                        </div>
                    @endif
                    <div class="p-3 flex items-center justify-between">
                        <span class="text-slate-400">Availability</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-800/60">
                            {{ $livestock->status }}
                        </span>
                    </div>
                </div>

                @if ($livestock->notes)
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-1">
                        <h4 class="text-xs font-bold text-slate-300">Breeder Notes</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ $livestock->notes }}</p>
                    </div>
                @endif

                <!-- MESSENGER INQUIRY CTA BUTTON -->
                @php
                    $inquiryMsg = "Hi! I'm interested in buying: " . ($livestock->variety ?: $livestock->species?->name) . " (Code: {$livestock->livestock_code}, ?" . number_format($livestock->purchase_price, 2) . ") listed on your AquaForge catalog: " . url()->current();
                    $messengerUrl = $owner && $owner->messenger_username 
                        ? "https://m.me/" . ltrim($owner->messenger_username, '@') . "?text=" . urlencode($inquiryMsg)
                        : null;
                @endphp

                <div class="space-y-3 pt-2">
                    @if ($messengerUrl)
                        <a href="{{ $messengerUrl }}" target="_blank" 
                           class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-xl shadow-blue-900/30 transition transform hover:-translate-y-0.5">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            <span>Inquire Directly on Facebook Messenger</span>
                        </a>
                    @endif

                    @php
                        $singleFbPost = "?? SPOTLIGHT: " . ($livestock->variety ?: $livestock->species?->name) . " ??\n\n"
                            . "?? Grade: " . ($livestock->grade ?? 'Show Grade') . "\n"
                            . "? Sex: {$livestock->sex}\n"
                            . "?? Price: ?" . number_format($livestock->purchase_price, 2) . "\n"
                            . "?? Location: " . ($owner->farm_location ?? 'Philippines') . "\n"
                            . "?? Payments: GCash / Maya\n"
                            . "?? Shipping: Lalamove / Grab / Busway\n\n"
                            . "?? View HD Photos & Full Lineage Specs:\n"
                            . url()->current() . "\n\n"
                            . "?? Direct Messenger:\n"
                            . ($owner && $owner->messenger_username ? "https://m.me/" . ltrim($owner->messenger_username, '@') : "") . "\n\n"
                            . "#AquaForge #FishKeepingPH #GuppyPH #BettaPH";
                    @endphp

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="copyText(`{{ addslashes($singleFbPost) }}`)" 
                                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                            <i data-lucide="facebook" class="w-3.5 h-3.5 text-blue-400"></i>
                            <span>Copy FB Post Text</span>
                        </button>
                        <button type="button" @click="copyText('{{ url()->current() }}')" 
                                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                            <i data-lucide="link" class="w-3.5 h-3.5 text-cyan-400"></i>
                            <span>Copy Fish Link</span>
                        </button>
                    </div>
                </div>

                <!-- Breeder contact and payment info -->
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs space-y-2">
                    <div class="flex items-center justify-between text-slate-300">
                        <span class="font-bold flex items-center gap-1.5">
                            <i data-lucide="store" class="w-3.5 h-3.5 text-cyan-400"></i>
                            {{ $owner->farm_name ?? 'AquaForge Farm' }}
                        </span>
                        <span>{{ $owner->farm_location ?? 'Philippines' }}</span>
                    </div>
                    @if ($owner && $owner->contact_number)
                        <div class="text-[11px] text-slate-400">
                            Mobile / Viber: <strong class="text-slate-200">{{ $owner->contact_number }}</strong>
                        </div>
                    @endif
                    <div class="pt-1 flex items-center gap-2 text-[10px] text-slate-400">
                        <span class="px-1.5 py-0.5 rounded bg-blue-950 text-blue-300 border border-blue-900">GCash</span>
                        <span class="px-1.5 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-900">Maya</span>
                        <span class="px-1.5 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-900">Lalamove</span>
                        <span class="px-1.5 py-0.5 rounded bg-teal-950 text-teal-300 border border-teal-900">Grab</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- RELATED SPECIMENS -->
        @if ($related->isNotEmpty())
            <div class="mt-12 pt-8 border-t border-slate-800 space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="fish" class="w-4 h-4 text-cyan-400"></i>
                    More Available {{ $livestock->species?->name }}
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($related as $rel)
                        <a href="{{ route('catalog.show', $rel) }}" class="group block rounded-xl bg-slate-900 border border-slate-800 p-3 hover:border-cyan-700/60 transition">
                            <div class="aspect-square rounded-lg bg-slate-950 overflow-hidden mb-2">
                                @if ($rel->photo_url)
                                    <img src="{{ $rel->photo_url }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-700"><i data-lucide="fish" class="w-6 h-6"></i></div>
                                @endif
                            </div>
                            <div class="text-xs font-bold text-white group-hover:text-cyan-300 truncate">{{ $rel->variety ?: $rel->species?->name }}</div>
                            <div class="flex items-center justify-between text-[11px] mt-1">
                                <span class="font-mono text-emerald-400 font-bold">?{{ number_format($rel->purchase_price, 2) }}</span>
                                <span class="text-slate-400 font-mono">{{ $rel->sex }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>

    <footer class="border-t border-slate-900 bg-slate-950 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $owner->farm_name ?? 'AquaForge' }}. Managed with AquaForge Farm Management.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
