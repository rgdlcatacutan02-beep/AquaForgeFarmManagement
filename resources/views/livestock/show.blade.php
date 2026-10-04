<x-app-layout title="{{ $livestock->livestock_code }} - {{ $livestock->variety ?: $livestock->species?->name }}">
    <div class="space-y-6" x-data="{ reassignModal: false, statusModal: false, socialCardModal: false, askingPrice: '{{ number_format($livestock->purchase_price, 2, '.', '') }}\', copiedToast: false }">
        <!-- Breadcrumb & Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <a href="{{ route('livestock.index') }}" class="hover:text-cyan-400 transition">Livestock</a>
                <span>/</span>
                <span class="font-mono text-cyan-400 font-bold">{{ $livestock->livestock_code }}</span>
                <span>/</span>
                <span class="text-white">{{ $livestock->variety ?: $livestock->species?->name }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button @click="socialCardModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600/30 hover:bg-blue-600/50 text-blue-300 text-xs font-semibold rounded-lg border border-blue-800/60 transition shadow-sm">
                    <i data-lucide="share-2" class="w-3.5 h-3.5 text-blue-400"></i>
                    <span>FB / Messenger Card</span>
                </button>
                <a href="{{ route('catalog.show', $livestock) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700 transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Public View</span>
                </a>
                <button @click="reassignModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">
                    <i data-lucide="shuffle" class="w-3.5 h-3.5 text-cyan-400"></i>
                    <span>Move Tank</span>
                </button>
                <button @click="statusModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition">
                    <i data-lucide="tag" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Status</span>
                </button>
                <a href="{{ route('livestock.edit', $livestock) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold rounded-lg shadow-md shadow-cyan-950/40 transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    <span>Edit</span>
                </a>
            </div>
        </div>

        <!-- Main Specimen Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl overflow-hidden shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <!-- Left: Photograph / Avatar -->
                <div class="md:col-span-1 flex flex-col items-center">
                    <div class="w-full aspect-square max-w-[280px] rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden shadow-inner relative group">
                        @if($livestock->photo_url)
                            <img src="{{ $livestock->photo_url }}" alt="{{ $livestock->livestock_code }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center text-slate-600 p-4">
                                <i data-lucide="fish" class="w-16 h-16 mx-auto mb-2 text-slate-700"></i>
                                <span class="text-xs text-slate-500">No Photo Uploaded</span>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-950/80 text-cyan-400 border border-slate-700/80 backdrop-blur-sm">
                                {{ $livestock->livestock_code }}
                            </span>
                        </div>
                    </div>

                    @if($livestock->grade)
                        <div class="mt-4 w-full max-w-[280px] p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-center">
                            <span class="text-[10px] uppercase tracking-wider text-slate-400 block font-semibold">Quality Grade</span>
                            <span class="text-sm font-bold mt-0.5 inline-block
                                {{ $livestock->grade === 'SHOW' ? 'text-amber-400' :
                                  ($livestock->grade === 'BREEDER' ? 'text-cyan-400' :
                                  ($livestock->grade === 'MATERIAL' ? 'text-indigo-400' : 'text-rose-400')) }}">
                                {{ $livestock->grade }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Right: Vital Information & Husbandry -->
                <div class="md:col-span-2 space-y-5">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-white tracking-tight">
                                {{ $livestock->variety ?: $livestock->species?->name }}
                            </h1>
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold border
                                {{ $livestock->status === 'BREEDER' ? 'bg-cyan-950/60 text-cyan-400 border-cyan-800/50' :
                                  ($livestock->status === 'AVAILABLE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/50' :
                                  ($livestock->status === 'QUARANTINE' ? 'bg-amber-950/60 text-amber-400 border-amber-800/50' :
                                  ($livestock->status === 'SICK' ? 'bg-rose-950/60 text-rose-400 border-rose-800/50' :
                                  ($livestock->status === 'SOLD' ? 'bg-indigo-950/60 text-indigo-400 border-indigo-800/50' : 'bg-slate-800 text-slate-300 border-slate-700')))) }}">
                                {{ $livestock->status }}
                            </span>
                        </div>
                        <div class="text-sm text-cyan-400/90 font-medium mt-0.5">
                            Species: <a href="{{ route('species.show', $livestock->species) }}" class="hover:underline">{{ $livestock->species?->name }}</a>
                            @if($livestock->species?->scientific_name)
                                <span class="italic text-slate-500 font-mono text-xs">({{ $livestock->species->scientific_name }})</span>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Sex</span>
                            <span class="text-sm font-semibold mt-0.5 block
                                {{ $livestock->sex === 'MALE' ? 'text-blue-400' : ($livestock->sex === 'FEMALE' ? 'text-rose-400' : 'text-slate-300') }}">
                                {{ $livestock->sex === 'MALE' ? '? Male' : ($livestock->sex === 'FEMALE' ? '? Female' : 'Unknown') }}
                            </span>
                        </div>

                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Current Tank</span>
                            @if($livestock->tank)
                                <a href="{{ route('tanks.show', $livestock->tank) }}" class="text-sm font-mono font-bold text-cyan-400 hover:underline mt-0.5 block">
                                    {{ $livestock->tank->tank_code }}
                                </a>
                            @else
                                <span class="text-sm text-slate-500 mt-0.5 block">Unassigned</span>
                            @endif
                        </div>

                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Acquired / Age</span>
                            <span class="text-xs text-slate-300 mt-1 block">
                                {{ $livestock->date_acquired ? $livestock->date_acquired->format('M d, Y') : ($livestock->date_of_birth ? $livestock->date_of_birth->format('M d, Y') : 'Unknown') }}
                            </span>
                        </div>

                        <div class="p-3 rounded-lg bg-slate-950/60 border border-slate-800/80">
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-semibold block">Purchase / Value</span>
                            <span class="text-sm font-mono font-bold text-emerald-400 mt-0.5 block">
                                ₱{{ number_format($livestock->purchase_price, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Lineage & Source -->
                    <div class="p-3.5 rounded-lg bg-slate-950/40 border border-slate-800/80 space-y-1">
                        <div class="text-xs text-slate-400">
                            <strong class="text-slate-200">Source:</strong> {{ $livestock->source ?: 'Homebred / Farm stock' }}
                        </div>
                        @if($livestock->notes)
                            <div class="text-xs text-slate-300 leading-relaxed whitespace-pre-line mt-2 pt-2 border-t border-slate-800/60">
                                {{ $livestock->notes }}
                            </div>
                        @endif
                    </div>

                    <!-- Grading Trait Breakdown (Section 13) -->
                    @if(!empty($livestock->grading_scores))
                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800/80 space-y-3">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                                <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                Trait Grading Breakdown (1 - 5 Scale)
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach(['body' => 'Body Form', 'color' => 'Color / Luster', 'tail' => 'Tail / Caudal', 'dorsal' => 'Dorsal Fin', 'pattern' => 'Pattern', 'overall' => 'Overall'] as $traitKey => $traitLabel)
                                    @php $score = $livestock->grading_scores[$traitKey] ?? null; @endphp
                                    @if($score !== null)
                                        <div class="p-2 rounded bg-slate-900 border border-slate-800">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-slate-400">{{ $traitLabel }}</span>
                                                <span class="font-bold text-cyan-400 font-mono">{{ $score }}/5</span>
                                            </div>
                                            <div class="w-full bg-slate-950 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-cyan-400 h-1.5 rounded-full" style="width: {{ ($score / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Breeding & Feeding History Tabs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Breeding Lineage & Records -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <i data-lucide="git-merge" class="w-4 h-4 text-cyan-400"></i>
                        Breeding Pairings & History
                    </h2>
                    <a href="{{ route('breeding.create', [$livestock->sex === 'MALE' ? 'male_id' : 'female_id' => $livestock->id]) }}" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        New Pairing
                    </a>
                </div>

                @php
                    $allBreedings = $livestock->maleBreedingEvents->merge($livestock->femaleBreedingEvents)->sortByDesc('start_date');
                @endphp

                @if($allBreedings->isEmpty())
                    <p class="text-xs text-slate-500 italic py-6 text-center">This specimen has not been assigned to any breeding events yet.</p>
                @else
                    <div class="divide-y divide-slate-800/60">
                        @foreach($allBreedings as $event)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('breeding.show', $event) }}" class="font-mono text-xs font-bold text-cyan-400 hover:underline">
                                            {{ $event->breeding_code }}
                                        </a>
                                        <span class="text-xs text-slate-300">
                                            Pair: {{ $event->maleLivestock?->livestock_code ?? '?' }} &times; {{ $event->femaleLivestock?->livestock_code ?? '?' }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Started {{ $event->start_date ? $event->start_date->format('M d, Y') : 'Unknown' }} &bull; Tank {{ $event->tank?->tank_code ?? '?' }}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                                    {{ $event->status === 'ACTIVE' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-800/40' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                                    {{ $event->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Feeding History -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-white flex items-center gap-2">
                        <i data-lucide="utensils" class="w-4 h-4 text-indigo-400"></i>
                        Recent Feeding Logs
                    </h2>
                    <a href="{{ route('feeding.create', ['livestock_id' => $livestock->id]) }}" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Log Feeding
                    </a>
                </div>

                @if($livestock->feedingLogs->isEmpty())
                    <p class="text-xs text-slate-500 italic py-6 text-center">No specific feeding logs recorded for this individual.</p>
                @else
                    <div class="divide-y divide-slate-800/60">
                        @foreach($livestock->feedingLogs->take(5) as $fLog)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-medium text-slate-200">{{ $fLog->food }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $fLog->fed_at?->format('M d, Y H:i') }}</div>
                                </div>
                                @if($fLog->quantity)
                                    <span class="text-xs font-mono text-slate-400">{{ $fLog->quantity }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- REASSIGN TANK MODAL -->
        <div x-show="reassignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="reassignModal = false" class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="shuffle" class="w-5 h-5 text-cyan-400"></i>
                    Reassign Tank: {{ $livestock->livestock_code }}
                </h3>
                <p class="text-xs text-slate-400">Select the destination aquarium, tub, or growout vat for this animal.</p>

                <form action="{{ route('livestock.update', $livestock) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <!-- Preserve required attributes -->
                    <input type="hidden" name="livestock_code" value="{{ $livestock->livestock_code }}">
                    <input type="hidden" name="species_id" value="{{ $livestock->species_id }}">
                    <input type="hidden" name="sex" value="{{ $livestock->sex }}">
                    <input type="hidden" name="status" value="{{ $livestock->status }}">
                    <input type="hidden" name="grade" value="{{ $livestock->grade }}">
                    <input type="hidden" name="variety" value="{{ $livestock->variety }}">

                    <div>
                        <label for="modal_tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Destination Tank</label>
                        <select name="tank_id" id="modal_tank_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="">No Tank (Unassigned)</option>
                            @foreach(\App\Models\Tank::orderBy('tank_code')->get() as $tk)
                                <option value="{{ $tk->id }}" {{ $livestock->tank_id == $tk->id ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} &bull; {{ $tk->name }} ({{ $tk->volume_liters }}L - {{ $tk->purpose }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="reassignModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold rounded-lg shadow">Move Specimen</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CHANGE STATUS MODAL (Section 35) -->
        <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="statusModal = false" class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5 text-amber-400"></i>
                    Update Specimen Status
                </h3>
                <p class="text-xs text-slate-400">Keep your livestock inventory accurate without deleting historical records.</p>

                <form action="{{ route('livestock.update', $livestock) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <!-- Preserve required attributes -->
                    <input type="hidden" name="livestock_code" value="{{ $livestock->livestock_code }}">
                    <input type="hidden" name="species_id" value="{{ $livestock->species_id }}">
                    <input type="hidden" name="sex" value="{{ $livestock->sex }}">
                    <input type="hidden" name="tank_id" value="{{ $livestock->tank_id }}">
                    <input type="hidden" name="grade" value="{{ $livestock->grade }}">
                    <input type="hidden" name="variety" value="{{ $livestock->variety }}">

                    <div>
                        <label for="modal_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">New Status</label>
                        <select name="status" id="modal_status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            @foreach(['BREEDER', 'GROWOUT', 'DISPLAY', 'QUARANTINE', 'SICK', 'AVAILABLE', 'SOLD', 'DECEASED', 'CULLED'] as $st)
                                <option value="{{ $st }}" {{ $livestock->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="statusModal = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold rounded-lg shadow">Update Status</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SOCIAL MEDIA FISH CARD & FACEBOOK POST MODAL -->
        @php
            $farmOwner = auth()->user() ?? \App\Models\User::first();
            $catalogSingleUrl = route('catalog.show', $livestock);
            $messengerDeepUrl = $farmOwner && $farmOwner->messenger_username ? "https://m.me/" . ltrim($farmOwner->messenger_username, '@') : "";
            $speciesName = $livestock->species?->name ?? 'Fish';
            $strainName = $livestock->variety ?: $speciesName;
            $tankCode = $livestock->tank?->tank_code ?? 'Tank A-01';
            $farmTitle = $farmOwner->farm_name ?? 'AquaForge Farm';
            $farmLoc = $farmOwner->farm_location ?? 'Philippines';
        @endphp
        <div x-show="socialCardModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md overflow-y-auto">
            <div @click.away="socialCardModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-5 my-8">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-blue-950 text-blue-400 border border-blue-800">
                            <i data-lucide="facebook" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-white">Social Media Fish Card & Messenger Post</h3>
                            <p class="text-xs text-slate-400">Generate a branded graphic card and 1-click caption for Facebook groups and Messenger.</p>
                        </div>
                    </div>
                    <button @click="socialCardModal = false" class="text-slate-400 hover:text-white text-lg">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                    <!-- Left: Graphic Card Preview (Canvas) -->
                    <div class="space-y-3">
                        <div class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center justify-between">
                            <span>Image Card Preview</span>
                            <span class="text-[10px] text-cyan-400 font-mono">PNG 800x800</span>
                        </div>
                        <div class="aspect-square w-full rounded-xl bg-slate-950 border border-slate-800 overflow-hidden shadow-inner flex items-center justify-center">
                            <canvas id="fishSocialCanvas" width="800" height="800" class="w-full h-full object-contain"></canvas>
                        </div>
                        <button type="button" @click="downloadCard()" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold rounded-lg shadow-sm transition">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Download Branded Card (PNG)</span>
                        </button>
                    </div>

                    <!-- Right: Facebook Post Caption & Customizer -->
                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Asking Price (in Philippine Peso ?)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-emerald-400 font-bold font-mono">?</span>
                                <input type="number" step="50" x-model="askingPrice" @input="renderCard()"
                                       class="w-full pl-8 pr-3 py-1.5 rounded-lg bg-slate-950 border border-slate-700 text-white font-mono text-sm focus:ring-1 focus:ring-cyan-500">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-slate-300 font-medium">Facebook Group Post Text</label>
                                <span class="text-[10px] text-slate-500">Ready to copy</span>
                            </div>
                            <textarea id="fbPostCaptionText" readonly rows="7" 
                                      class="w-full rounded-lg bg-slate-950 border border-slate-700 text-[11px] font-mono text-slate-300 p-2.5 leading-relaxed focus:ring-0"
                                      :value="`?? AVAILABLE: {{ $strainName }} ({{ $speciesName }}) ??\n\n` +
                                              `?? Quality Grade: {{ $livestock->grade ?? 'Show Grade' }}\n` +
                                              `? Sex / Package: {{ $livestock->sex }}\n` +
                                              `?? Price: ?${parseFloat(askingPrice || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}\n` +
                                              `?? Location: {{ $farmLoc }}\n` +
                                              `?? Payments: GCash / Maya Accepted\n` +
                                              `?? Delivery: Lalamove / Grab / Busway\n\n` +
                                              `?? View HD Photos & Specs on our Catalog:\n{{ $catalogSingleUrl }}\n\n` +
                                              `?? Direct Messenger Inquiry:\n{{ $messengerDeepUrl }}\n\n` +
                                              `#AquaForge #GuppyPH #BettaPH #FishKeepingPH #AquariumPhilippines`"></textarea>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="navigator.clipboard.writeText(document.getElementById('fbPostCaptionText').value); copiedToast = true; setTimeout(() => copiedToast = false, 3000)" 
                                    class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span>Copy FB Caption</span>
                            </button>
                            @if ($farmOwner && $farmOwner->messenger_username)
                                <a href="{{ $messengerDeepUrl }}" target="_blank" 
                                   class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium text-xs rounded-lg border border-slate-700 transition" title="Open Messenger">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5 text-blue-400"></i>
                                </a>
                            @endif
                        </div>

                        <div x-show="copiedToast" x-cloak class="p-2 rounded-lg bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-[11px] flex items-center gap-1.5 font-semibold">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Caption copied! Paste directly into Facebook post or Messenger!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas Card Rendering Script -->
        <script>
            function renderCard() {
                const canvas = document.getElementById('fishSocialCanvas');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const w = canvas.width;
                const h = canvas.height;

                // Background
                const bgGrad = ctx.createLinearGradient(0, 0, w, h);
                bgGrad.addColorStop(0, '#030712');
                bgGrad.addColorStop(0.5, '#082f49');
                bgGrad.addColorStop(1, '#020617');
                ctx.fillStyle = bgGrad;
                ctx.fillRect(0, 0, w, h);

                // Top Header Banner
                ctx.fillStyle = 'rgba(15, 23, 42, 0.7)';
                ctx.fillRect(40, 30, w - 80, 70);
                ctx.strokeStyle = '#0284c7';
                ctx.lineWidth = 2;
                ctx.strokeRect(40, 30, w - 80, 70);

                // Farm Name
                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 24px sans-serif';
                ctx.fillText('{{ addslashes($farmTitle) }}', 60, 72);

                // Location / Direct Badge
                ctx.fillStyle = '#94a3b8';
                ctx.font = '16px sans-serif';
                ctx.textAlign = 'right';
                ctx.fillText('?? {{ addslashes($farmLoc) }}', w - 60, 72);
                ctx.textAlign = 'left';

                // Image placeholder or loaded image
                const fishImg = new Image();
                fishImg.crossOrigin = 'anonymous';
                const photoSrc = '{{ $livestock->photo_url ?? "" }}';

                function drawForeground() {
                    // Variety / Strain Title
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 36px sans-serif';
                    ctx.fillText('{{ addslashes($strainName) }}', 40, 580);

                    // Subtitle / Species & Sex
                    ctx.fillStyle = '#38bdf8';
                    ctx.font = 'bold 22px sans-serif';
                    ctx.fillText('{{ addslashes($speciesName) }} ? {{ $livestock->sex }} ? {{ $tankCode }}', 40, 615);

                    // Grade Badge
                    ctx.fillStyle = '#f59e0b';
                    ctx.fillRect(40, 635, 180, 35);
                    ctx.fillStyle = '#0f172a';
                    ctx.font = 'bold 16px sans-serif';
                    ctx.fillText('? {{ $livestock->grade ?? "SHOW GRADE" }}', 55, 658);

                    // Asking Price
                    const priceVal = parseFloat(window.Alpine ? Alpine.$data(document.querySelector('[x-data]')).askingPrice : '{{ $livestock->purchase_price }}') || 0;
                    ctx.fillStyle = '#10b981';
                    ctx.font = 'bold 44px monospace';
                    ctx.textAlign = 'right';
                    ctx.fillText('?' + priceVal.toLocaleString('en-US', {minimumFractionDigits: 2}), w - 40, 660);
                    ctx.textAlign = 'left';

                    // Bottom Bar
                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(40, 700, w - 80, 70);
                    ctx.strokeStyle = '#334155';
                    ctx.strokeRect(40, 700, w - 80, 70);

                    ctx.fillStyle = '#e2e8f0';
                    ctx.font = 'bold 16px sans-serif';
                    ctx.fillText('?? GCash / Maya Accepted  ?  ?? Lalamove / Grab', 60, 742);

                    ctx.fillStyle = '#38bdf8';
                    ctx.font = 'bold 15px sans-serif';
                    ctx.textAlign = 'right';
                    ctx.fillText('?? Inquire via Messenger', w - 60, 742);
                    ctx.textAlign = 'left';
                }

                if (photoSrc) {
                    fishImg.onload = function() {
                        // Draw rounded image container in center
                        ctx.save();
                        ctx.beginPath();
                        ctx.roundRect(40, 120, w - 80, 420, 16);
                        ctx.clip();
                        ctx.drawImage(fishImg, 40, 120, w - 80, 420);
                        ctx.restore();
                        drawForeground();
                    };
                    fishImg.onerror = function() {
                        drawDefaultImage();
                        drawForeground();
                    };
                    fishImg.src = photoSrc;
                } else {
                    drawDefaultImage();
                    drawForeground();
                }

                function drawDefaultImage() {
                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(40, 120, w - 80, 420);
                    ctx.fillStyle = '#475569';
                    ctx.font = 'bold 24px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText('?? {{ addslashes($strainName) }}', w / 2, 330);
                    ctx.font = '16px sans-serif';
                    ctx.fillText('Live Fish Specimen', w / 2, 360);
                    ctx.textAlign = 'left';
                }
            }

            function downloadCard() {
                const canvas = document.getElementById('fishSocialCanvas');
                const link = document.createElement('a');
                link.download = '{{ $livestock->livestock_code }}-social-card.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }

            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(renderCard, 300);
            });
        </script>

    </div>
</x-app-layout>
