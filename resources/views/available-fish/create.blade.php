<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('available-fish.index') }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-400"></i>
                        <span>List Fish for Sale on Catalog</span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Publish a specimen directly onto your public catalog with photos and pricing.</p>
                </div>
            </div>
            
            <a href="{{ route('catalog.index') }}" target="_blank" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1">
                <span>View Public Catalog</span>
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden" 
             x-data="{ photoPreview: null }">

            <div class="p-6 border-b border-slate-800 bg-slate-950/40 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-emerald-950 text-emerald-400 border border-emerald-800">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                    </span>
                    <div>
                        <div class="text-sm font-bold text-white">Specimen Catalog Listing Form</div>
                        <div class="text-[11px] text-slate-400">Fish published with status 'AVAILABLE' will appear instantly on your customer catalog.</div>
                    </div>
                </div>

                <span class="px-2.5 py-1 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800 text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Instant Catalog Publish
                </span>
            </div>

            @if ($errors->any())
                <div class="m-6 p-4 bg-rose-950/90 border border-rose-800 rounded-xl text-rose-200 text-xs space-y-1 shadow-lg">
                    <div class="font-bold text-rose-300 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                        <span>Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs pl-2 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('available-fish.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- Basic Identification -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="species_id" class="block text-xs font-semibold text-slate-300 mb-1">
                            Species <span class="text-rose-400">*</span>
                        </label>
                        <select name="species_id" id="species_id" required 
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="">Select Species</option>
                            @foreach ($species as $sp)
                                <option value="{{ $sp->id }}" {{ old('species_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="variety" class="block text-xs font-semibold text-slate-300 mb-1">
                            Strain / Variety Name
                        </label>
                        <input type="text" name="variety" id="variety" value="{{ old('variety') }}" 
                               placeholder="e.g. Albino Full Red Dumbo Ear, Halfmoon Koi"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label for="livestock_code" class="block text-xs font-semibold text-slate-300 mb-1">
                            Specimen Code <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="livestock_code" id="livestock_code" value="{{ old('livestock_code', $randomCode) }}" required 
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-cyan-300 font-mono focus:outline-none focus:border-cyan-500">
                    </div>
                </div>

                <!-- Pricing, Sex, Grade -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                    <div>
                        <label for="purchase_price" class="block text-xs font-bold text-emerald-400 mb-1">
                            Asking Selling Price (PHP &#8369;) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-emerald-400 font-mono text-sm font-bold">&#8369;</span>
                            <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" 
                                   value="{{ old('purchase_price') }}" required placeholder="e.g. 500.00"
                                   class="w-full bg-slate-900 border border-emerald-700/60 rounded-lg pl-8 pr-3 py-2 text-sm text-emerald-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Displayed as the price tag on the catalog.</p>
                    </div>

                    <div>
                        <label for="sex" class="block text-xs font-semibold text-slate-300 mb-1">
                            Sex / Package Type <span class="text-rose-400">*</span>
                        </label>
                        <select name="sex" id="sex" required 
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="MALE" {{ old('sex') === 'MALE' ? 'selected' : '' }}>MALE (Single Male)</option>
                            <option value="FEMALE" {{ old('sex') === 'FEMALE' ? 'selected' : '' }}>FEMALE (Single Female)</option>
                            <option value="UNKNOWN" {{ old('sex', 'UNKNOWN') === 'UNKNOWN' ? 'selected' : '' }}>PAIR / TRIO / UNSEXED</option>
                        </select>
                    </div>

                    <div>
                        <label for="grade" class="block text-xs font-semibold text-slate-300 mb-1">
                            Classification / Grade
                        </label>
                        <select name="grade" id="grade" 
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-amber-300 focus:outline-none focus:border-cyan-500">
                            <option value="Show Grade" {{ old('grade') === 'Show Grade' ? 'selected' : '' }}>Show Grade (Competition Standard)</option>
                            <option value="Breeder Grade" {{ old('grade', 'Breeder Grade') === 'Breeder Grade' ? 'selected' : '' }}>Breeder Grade (Proven Strain)</option>
                            <option value="High Grade" {{ old('grade') === 'High Grade' ? 'selected' : '' }}>High Grade</option>
                            <option value="Standard Grade" {{ old('grade') === 'Standard Grade' ? 'selected' : '' }}>Standard Grade</option>
                            <option value="Juvenile" {{ old('grade') === 'Juvenile' ? 'selected' : '' }}>Juvenile</option>
                        </select>
                    </div>
                </div>

                <!-- Location & Catalog Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="tank_id" class="block text-xs font-semibold text-slate-300 mb-1">
                            Current Holding Tank
                        </label>
                        <select name="tank_id" id="tank_id" 
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="">No Tank Assigned (Quarantine / Rack)</option>
                            @foreach ($tanks as $tk)
                                <option value="{{ $tk->id }}" {{ old('tank_id') == $tk->id ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} &mdash; {{ $tk->name ?? 'Standard Tank' }} ({{ $tk->volume_liters }}L)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-300 mb-1">
                            Listing Status <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" id="status" required 
                                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                            <option value="AVAILABLE" {{ old('status', 'AVAILABLE') === 'AVAILABLE' ? 'selected' : '' }}>AVAILABLE &bull; Show on Public Catalog</option>
                            <option value="BREEDER" {{ old('status') === 'BREEDER' ? 'selected' : '' }}>BREEDER &bull; Hidden from Catalog</option>
                            <option value="GROWOUT" {{ old('status') === 'GROWOUT' ? 'selected' : '' }}>GROWOUT &bull; Hidden from Catalog</option>
                        </select>
                    </div>
                </div>

                <!-- Photo Upload with Preview -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3">
                    <label class="block text-xs font-bold text-white">Specimen Photo (WYSIWYG for Buyers)</label>
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-24 h-24 rounded-xl bg-slate-900 border border-slate-700 overflow-hidden flex items-center justify-center flex-shrink-0">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!photoPreview">
                                <div class="text-center text-slate-600 p-2">
                                    <i data-lucide="camera" class="w-6 h-6 mx-auto mb-1"></i>
                                    <span class="text-[9px]">No photo</span>
                                </div>
                            </template>
                        </div>

                        <div class="flex-1 space-y-1">
                            <input type="file" name="photo" id="photo" accept="image/*"
                                   @change="const file = $event.target.files[0]; if (file) { photoPreview = URL.createObjectURL(file); }"
                                   class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-950 file:text-cyan-300 hover:file:bg-cyan-900 transition cursor-pointer">
                            <p class="text-[11px] text-slate-400">Upload a crisp photo taken under proper lighting. Supports JPG, PNG, WebP up to 20MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Breeder Notes / Details -->
                <div>
                    <label for="notes" class="block text-xs font-semibold text-slate-300 mb-1">
                        Breeder Notes &amp; Buyer Details (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3" 
                              placeholder="e.g. Fed with live BBS and spirulina flakes. Vigorous swimmer, high dorsal finnage, ready for conditioning or breeding."
                              class="w-full bg-slate-950 border border-slate-700 rounded-lg p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit / Cancel -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('available-fish.index') }}" 
                       class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                        Cancel
                    </a>

                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-950 transition transform hover:-translate-y-0.5">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                        <span>Publish Fish to Catalog</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
