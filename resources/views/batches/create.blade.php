<x-app-layout title="Register Offspring Batch">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('batches.index') }}" class="hover:text-blue-400 transition">Batches</a>
            <span>/</span>
            <span class="text-white">Register Fry / Juvenile Cohort</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                batchCode: '{{ old('batch_code', 'BAT-' . sprintf('%03d', \App\Models\OffspringBatch::max('id') + 1)) }}',
                birthDate: '{{ old('birth_or_hatch_date', date('Y-m-d')) }}',
                initialCount: '{{ old('initial_count', 30) }}'
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-blue-400"></i>
                Register Offspring Batch
            </h1>
            <p class="text-sm text-slate-400 mb-6">Record group spawnings, guppy fry drops, or crayfish clutches without tracking each fry individually.</p>

            <form action="{{ route('batches.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Identification & Species -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="batch_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Batch Code <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="batch_code" id="batch_code" x-model="batchCode" required class="w-full bg-slate-950 border @error('batch_code') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-blue-400 font-mono font-bold focus:outline-none focus:border-blue-500 transition">
                        @error('batch_code')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="species_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Species <span class="text-rose-400">*</span>
                        </label>
                        <select name="species_id" id="species_id" required class="w-full bg-slate-950 border @error('species_id') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500 transition">
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

                <!-- Parents & Variety -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="breeding_event_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Parentage / Breeding Event <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <select name="breeding_event_id" id="breeding_event_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                            <option value="">No Breeding Event (Direct entry)</option>
                            @foreach($breedings as $br)
                                <option value="{{ $br->id }}" {{ (old('breeding_event_id', request('breeding_id')) == $br->id) ? 'selected' : '' }}>
                                    {{ $br->breeding_code }} &bull; {{ $br->maleLivestock?->livestock_code ?? '?' }} &times; {{ $br->femaleLivestock?->livestock_code ?? '?' }} ({{ $br->species?->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="variety" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Variety / Strain Name <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <input type="text" name="variety" id="variety" value="{{ old('variety') }}" placeholder="e.g. Albino Blue Topaz F1, Kamfa Red Dragon" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>

                <!-- Counts & Dates -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="initial_count" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Initial Headcount <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" min="1" name="initial_count" id="initial_count" x-model="initialCount" required class="w-full bg-slate-950 border @error('initial_count') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white font-mono font-bold focus:outline-none focus:border-blue-500 transition">
                        @error('initial_count')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="birth_or_hatch_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Birth / Hatch Date <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" name="birth_or_hatch_date" id="birth_or_hatch_date" x-model="birthDate" required class="w-full bg-slate-950 border @error('birth_or_hatch_date') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label for="tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Assigned Growout Tank
                        </label>
                        <select name="tank_id" id="tank_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                            <option value="">No Tank Assigned</option>
                            @foreach($tanks as $tk)
                                <option value="{{ $tk->id }}" {{ (old('tank_id', request('tank_id')) == $tk->id) ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} - {{ $tk->name }} ({{ $tk->volume_liters }}L)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status & Grade -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Batch Lifecycle Status <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                            <option value="GROWING" {{ old('status', 'GROWING') === 'GROWING' ? 'selected' : '' }}>GROWING (Early fry / raising)</option>
                            <option value="READY_FOR_GRADING" {{ old('status') === 'READY_FOR_GRADING' ? 'selected' : '' }}>READY FOR GRADING (Sub-adults)</option>
                            <option value="READY_FOR_SALE" {{ old('status') === 'READY_FOR_SALE' ? 'selected' : '' }}>READY FOR SALE (Commercial)</option>
                            <option value="SOLD_OUT" {{ old('status') === 'SOLD_OUT' ? 'selected' : '' }}>SOLD OUT</option>
                            <option value="COMPLETED" {{ old('status') === 'COMPLETED' ? 'selected' : '' }}>COMPLETED (Archived)</option>
                        </select>
                    </div>

                    <div>
                        <label for="grade" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Batch Quality Grade <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <select name="grade" id="grade" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                            <option value="">Unrated / Mixed</option>
                            <option value="SHOW">SHOW GRADE POTENTIAL</option>
                            <option value="BREEDER">BREEDER QUALITY</option>
                            <option value="MATERIAL">COMMERCIAL / FLOCK</option>
                            <option value="CULL">FEEDER / CULL GRADE</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Batch Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Feeding regime (baby brine shrimp, microworms), growth milestones, observations..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('batches.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-950/40 transition">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
