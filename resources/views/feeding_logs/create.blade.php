<x-app-layout title="Log Feeding">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('feeding.index') }}" class="hover:text-indigo-400 transition">Feeding</a>
            <span>/</span>
            <span class="text-white">Record Tank Feeding</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                food: '{{ old('food', 'Guppy Micro Pellets') }}',
                setFood(val) { this.food = val; }
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="utensils" class="w-5 h-5 text-indigo-400"></i>
                Log Aquarium Feeding
            </h1>
            <p class="text-sm text-slate-400 mb-6">Track dietary rations and feeding routines across your tanks.</p>

            <form action="{{ route('feeding.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Tank Selection -->
                <div>
                    <label for="tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Target Tank <span class="text-rose-400">*</span>
                    </label>
                    <select name="tank_id" id="tank_id" required class="w-full bg-slate-950 border @error('tank_id') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                        <option value="">Select Tank</option>
                        @foreach($tanks as $tk)
                            <option value="{{ $tk->id }}" {{ (old('tank_id', $selectedTankId) == $tk->id) ? 'selected' : '' }}>
                                {{ $tk->tank_code }} &bull; {{ $tk->name }} ({{ $tk->volume_liters }}L)
                            </option>
                        @endforeach
                    </select>
                    @error('tank_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Food Type & Quick Presets -->
                <div>
                    <label for="food" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Food Type / Diet <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="food" id="food" x-model="food" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    
                    <!-- Quick Presets -->
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" @click="setFood('Live Baby Brine Shrimp (BBS)')" class="text-[11px] px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition">Live BBS</button>
                        <button type="button" @click="setFood('Guppy Micro Pellets (50% Protein)')" class="text-[11px] px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition">High-Protein Pellets</button>
                        <button type="button" @click="setFood('Frozen Bloodworms')" class="text-[11px] px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition">Frozen Bloodworms</button>
                        <button type="button" @click="setFood('Live Microworms')" class="text-[11px] px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition">Microworms</button>
                        <button type="button" @click="setFood('Spirulina Flakes')" class="text-[11px] px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition">Spirulina Flakes</button>
                        <button type="button" @click="setFood('Sinking Crayfish Pellets')" class="text-[11px] px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition">Crayfish Pellets</button>
                    </div>
                </div>

                <!-- Quantity & Timestamp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Portion / Quantity <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <input type="text" name="quantity" id="quantity" value="{{ old('quantity', '1 pinch') }}" placeholder="e.g. 1 pinch, 5ml pipette, 2 cubes" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="fed_at" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Fed At <span class="text-rose-400">*</span>
                        </label>
                        <input type="datetime-local" name="fed_at" id="fed_at" value="{{ old('fed_at', now()->format('Y-m-d\TH:i')) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <!-- Observations & Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Feeding Response & Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Fed voraciously within 2 minutes, fry active..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('feeding.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-indigo-950/40 transition">Save Feeding</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
