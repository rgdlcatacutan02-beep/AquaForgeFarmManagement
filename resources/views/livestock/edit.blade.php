<x-app-layout title="Edit {{ $livestock->livestock_code }}">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('livestock.index') }}" class="hover:text-cyan-400 transition">Livestock</a>
            <span>/</span>
            <a href="{{ route('livestock.show', $livestock) }}" class="hover:text-cyan-400 transition">{{ $livestock->livestock_code }}</a>
            <span>/</span>
            <span class="text-white">Edit</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                photoPreview: '{{ $livestock->photo_url }}',
                previewPhoto(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => { this.photoPreview = e.target.result; };
                        reader.readAsDataURL(file);
                    }
                },
                overallGrade: '{{ old('grade', $livestock->grade) }}',
                scores: {
                    body: {{ old('grading_scores.body', $livestock->grading_scores['body'] ?? 3) }},
                    color: {{ old('grading_scores.color', $livestock->grading_scores['color'] ?? 3) }},
                    tail: {{ old('grading_scores.tail', $livestock->grading_scores['tail'] ?? 3) }},
                    dorsal: {{ old('grading_scores.dorsal', $livestock->grading_scores['dorsal'] ?? 3) }},
                    pattern: {{ old('grading_scores.pattern', $livestock->grading_scores['pattern'] ?? 3) }},
                    overall: {{ old('grading_scores.overall', $livestock->grading_scores['overall'] ?? 3) }}
                }
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="pencil" class="w-5 h-5 text-cyan-400"></i>
                Edit Specimen: {{ $livestock->livestock_code }} ({{ $livestock->variety ?: $livestock->species?->name }})
            </h1>
            <p class="text-sm text-slate-400 mb-6">Modify records, update grading scores, reassign tanks, or replace photograph.</p>

            <form action="{{ route('livestock.update', $livestock) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Top Row: Identification, Species & Code -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="livestock_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Livestock Code <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="livestock_code" id="livestock_code" value="{{ old('livestock_code', $livestock->livestock_code) }}" required class="w-full bg-slate-950 border @error('livestock_code') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-cyan-400 font-mono font-bold focus:outline-none focus:border-cyan-500 transition">
                        @error('livestock_code')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="species_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Species <span class="text-rose-400">*</span>
                        </label>
                        <select name="species_id" id="species_id" required class="w-full bg-slate-950 border @error('species_id') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            @foreach($species as $sp)
                                <option value="{{ $sp->id }}" {{ (old('species_id', $livestock->species_id) == $sp->id) ? 'selected' : '' }}>
                                    {{ $sp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('species_id')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="variety" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Strain / Variety <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <input type="text" name="variety" id="variety" value="{{ old('variety', $livestock->variety) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>

                <!-- Second Row: Sex, Tank Assignment, Status -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="sex" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Sex <span class="text-rose-400">*</span>
                        </label>
                        <select name="sex" id="sex" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="MALE" {{ old('sex', $livestock->sex) === 'MALE' ? 'selected' : '' }}>♂ Male</option>
                            <option value="FEMALE" {{ old('sex', $livestock->sex) === 'FEMALE' ? 'selected' : '' }}>♀ Female</option>
                            <option value="UNKNOWN" {{ old('sex', $livestock->sex) === 'UNKNOWN' ? 'selected' : '' }}>❓ Unknown / Juvenile</option>
                        </select>
                    </div>

                    <div>
                        <label for="tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Assigned Tank <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <select name="tank_id" id="tank_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">No Tank (Holding / Quarantine)</option>
                            @foreach($tanks as $tk)
                                <option value="{{ $tk->id }}" {{ (old('tank_id', $livestock->tank_id) == $tk->id) ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} - {{ $tk->name }} ({{ $tk->volume_liters }}L)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Status <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="BREEDER" {{ old('status', $livestock->status) === 'BREEDER' ? 'selected' : '' }}>BREEDER (Active Breeding Stock)</option>
                            <option value="GROWOUT" {{ old('status', $livestock->status) === 'GROWOUT' ? 'selected' : '' }}>GROWOUT (Raising)</option>
                            <option value="DISPLAY" {{ old('status', $livestock->status) === 'DISPLAY' ? 'selected' : '' }}>DISPLAY (Show Specimen)</option>
                            <option value="QUARANTINE" {{ old('status', $livestock->status) === 'QUARANTINE' ? 'selected' : '' }}>QUARANTINE (Isolation)</option>
                            <option value="SICK" {{ old('status', $livestock->status) === 'SICK' ? 'selected' : '' }}>SICK (Treatment)</option>
                            <option value="AVAILABLE" {{ old('status', $livestock->status) === 'AVAILABLE' ? 'selected' : '' }}>AVAILABLE (For Sale)</option>
                            <option value="SOLD" {{ old('status', $livestock->status) === 'SOLD' ? 'selected' : '' }}>SOLD</option>
                            <option value="DECEASED" {{ old('status', $livestock->status) === 'DECEASED' ? 'selected' : '' }}>DECEASED</option>
                            <option value="CULLED" {{ old('status', $livestock->status) === 'CULLED' ? 'selected' : '' }}>CULLED</option>
                        </select>
                    </div>
                </div>

                <!-- Third Row: Dates & Acquisition -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="date_acquired" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Date Acquired</label>
                        <input type="date" name="date_acquired" id="date_acquired" value="{{ old('date_acquired', $livestock->date_acquired?->format('Y-m-d')) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $livestock->date_of_birth?->format('Y-m-d')) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label for="source" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Lineage Source</label>
                        <input type="text" name="source" id="source" value="{{ old('source', $livestock->source) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    </div>
                    <div>
                        <label for="purchase_price" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Cost / Price (₱)</label>
                        <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $livestock->purchase_price) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white font-mono focus:outline-none focus:border-cyan-500">
                    </div>
                </div>

                <!-- Grading Section -->
                <div class="p-4 bg-slate-950/60 border border-slate-800/80 rounded-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                            <i data-lucide="award" class="w-4 h-4"></i>
                            Grading & Physical Trait Scores (1-5 Scale)
                        </span>
                        <div>
                            <select name="grade" id="grade" x-model="overallGrade" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1 text-xs text-cyan-300 font-semibold focus:outline-none focus:border-cyan-500">
                                <option value="">Select Overall Grade</option>
                                <option value="SHOW">SHOW GRADE (High Quality)</option>
                                <option value="BREEDER">BREEDER GRADE (Selected)</option>
                                <option value="MATERIAL">MATERIAL (Flock / Standard)</option>
                                <option value="CULL">CULL (Deformed / Reject)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Body Form</label>
                            <input type="number" min="1" max="5" name="grading_scores[body]" x-model="scores.body" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-sm text-center text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Color / Luster</label>
                            <input type="number" min="1" max="5" name="grading_scores[color]" x-model="scores.color" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-sm text-center text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Tail / Caudal</label>
                            <input type="number" min="1" max="5" name="grading_scores[tail]" x-model="scores.tail" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-sm text-center text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Dorsal Fin</label>
                            <input type="number" min="1" max="5" name="grading_scores[dorsal]" x-model="scores.dorsal" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-sm text-center text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Pattern</label>
                            <input type="number" min="1" max="5" name="grading_scores[pattern]" x-model="scores.pattern" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-sm text-center text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Overall Impression</label>
                            <input type="number" min="1" max="5" name="grading_scores[overall]" x-model="scores.overall" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-sm text-center text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload with Preview -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Specimen Photograph <span class="text-slate-500 lowercase">(Upload to replace)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="relative w-28 h-28 rounded-xl bg-slate-950 border-2 border-slate-800 flex items-center justify-center overflow-hidden">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!photoPreview">
                                <div class="text-center text-slate-500 p-2">
                                    <i data-lucide="image" class="w-6 h-6 mx-auto mb-1"></i>
                                    <span class="text-[10px]">No Photo</span>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="file" name="photo" id="photo" accept="image/*" @change="previewPhoto($event)" class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer">
                            <p class="text-[11px] text-slate-500">Selecting a new image will overwrite the previous photo.</p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Husbandry & Lineage Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">{{ old('notes', $livestock->notes) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('livestock.show', $livestock) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-semibold rounded-lg shadow-lg shadow-cyan-950/40 transition">Update Specimen</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
