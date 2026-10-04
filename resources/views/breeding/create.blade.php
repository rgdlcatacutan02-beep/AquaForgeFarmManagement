<x-app-layout title="New Breeding Event">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('breeding.index') }}" class="hover:text-pink-400 transition">Breeding</a>
            <span>/</span>
            <span class="text-white">New Pairing</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                breedingCode: '{{ old('breeding_code', 'BR-' . sprintf('%03d', \App\Models\BreedingEvent::max('id') + 1)) }}',
                startDate: '{{ old('start_date', date('Y-m-d')) }}',
                expectedDate: '{{ old('expected_date', date('Y-m-d', strtotime('+28 days'))) }}'
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="heart-handshake" class="w-5 h-5 text-pink-400"></i>
                Register Breeding Pairing
            </h1>
            <p class="text-sm text-slate-400 mb-6">Pair selected male and female breeders and designate a spawning tank.</p>

            <form action="{{ route('breeding.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Identification & Species -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="breeding_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Breeding Event ID <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="breeding_code" id="breeding_code" x-model="breedingCode" required class="w-full bg-slate-950 border @error('breeding_code') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-pink-400 font-mono font-bold focus:outline-none focus:border-pink-500 transition">
                        @error('breeding_code')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="species_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Target Species <span class="text-rose-400">*</span>
                        </label>
                        <select name="species_id" id="species_id" required class="w-full bg-slate-950 border @error('species_id') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                            <option value="">Select Species</option>
                            @foreach($species as $sp)
                                <option value="{{ $sp->id }}" {{ (old('species_id', request('species_id')) == $sp->id) ? 'selected' : '' }}>
                                    {{ $sp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('species_id')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Parents Selection -->
                <div class="p-4 bg-slate-950/60 border border-slate-800/80 rounded-xl space-y-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-pink-400 flex items-center gap-1.5">
                        <i data-lucide="git-commit" class="w-4 h-4"></i>
                        Selected Parents (Lineage)
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Male Parent -->
                        <div>
                            <label for="male_livestock_id" class="block text-xs font-semibold text-blue-400 mb-1.5 flex items-center gap-1">
                                <span>♂</span> Male Breeder
                            </label>
                            <select name="male_livestock_id" id="male_livestock_id" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                                <option value="">None / Colony Sire</option>
                                @foreach($males as $male)
                                    <option value="{{ $male->id }}" {{ (old('male_livestock_id', request('male_id')) == $male->id) ? 'selected' : '' }}>
                                        {{ $male->livestock_code }} - {{ $male->variety ?: $male->species?->name }} ({{ $male->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Female Parent -->
                        <div>
                            <label for="female_livestock_id" class="block text-xs font-semibold text-rose-400 mb-1.5 flex items-center gap-1">
                                <span>♀</span> Female Breeder
                            </label>
                            <select name="female_livestock_id" id="female_livestock_id" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-rose-500 transition">
                                <option value="">None / Colony Dam</option>
                                @foreach($females as $female)
                                    <option value="{{ $female->id }}" {{ (old('female_livestock_id', request('female_id')) == $female->id) ? 'selected' : '' }}>
                                        {{ $female->livestock_code }} - {{ $female->variety ?: $female->species?->name }} ({{ $female->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tank & Dates -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Spawning / Pair Tank
                        </label>
                        <select name="tank_id" id="tank_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                            <option value="">No Tank Assigned</option>
                            @foreach($tanks as $tk)
                                <option value="{{ $tk->id }}" {{ (old('tank_id', request('tank_id')) == $tk->id) ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} - {{ $tk->name }} ({{ $tk->volume_liters }}L)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Pairing Date <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" x-model="startDate" required class="w-full bg-slate-950 border @error('start_date') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500">
                    </div>

                    <div>
                        <label for="expected_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Expected Birth / Hatch
                        </label>
                        <input type="date" name="expected_date" id="expected_date" x-model="expectedDate" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500">
                    </div>
                </div>

                <!-- Status & Notes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Pairing Status <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-pink-500 transition">
                            <option value="ACTIVE" {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Pair is together)</option>
                            <option value="PLANNED" {{ old('status') === 'PLANNED' ? 'selected' : '' }}>PLANNED (Future project)</option>
                            <option value="COMPLETED" {{ old('status') === 'COMPLETED' ? 'selected' : '' }}>COMPLETED (Spawned & separated)</option>
                            <option value="FAILED" {{ old('status') === 'FAILED' ? 'selected' : '' }}>FAILED (No fertilization)</option>
                            <option value="CANCELLED" {{ old('status') === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                        </select>
                    </div>

                    <div>
                        <label for="actual_birth_or_hatch_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Actual Birth / Hatch Date <span class="text-slate-500 lowercase">(if already occurred)</span>
                        </label>
                        <input type="date" name="actual_birth_or_hatch_date" id="actual_birth_or_hatch_date" value="{{ old('actual_birth_or_hatch_date') }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Pairing Goals & Husbandry Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Intended genetic traits, water temperature adjustments, live feed conditioning notes..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('breeding.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-pink-500 hover:bg-pink-400 text-white text-sm font-semibold rounded-lg shadow-lg shadow-pink-950/40 transition">Start Breeding Event</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
