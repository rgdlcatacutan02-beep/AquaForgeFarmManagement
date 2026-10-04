<x-app-layout title="Record Water Test">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('water-logs.index') }}" class="hover:text-cyan-400 transition">Water Quality</a>
            <span>/</span>
            <span class="text-white">New Parameter Test</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                temp: '{{ old('temperature', '26.5') }}',
                ph: '{{ old('ph', '7.2') }}',
                ammonia: '{{ old('ammonia', '0.00') }}',
                nitrite: '{{ old('nitrite', '0.00') }}',
                nitrate: '{{ old('nitrate', '20') }}',
                tds: '{{ old('tds') }}',
                computeStatus() {
                    let nh3 = parseFloat(this.ammonia);
                    let no2 = parseFloat(this.nitrite);
                    let no3 = parseFloat(this.nitrate);
                    let phVal = parseFloat(this.ph);
                    if (nh3 > 0.5 || no2 > 0.5 || no3 > 80) return 'CHECK';
                    if (nh3 > 0.25 || no2 > 0.25 || no3 > 40 || phVal < 6.0 || phVal > 8.5) return 'WARNING';
                    return 'GOOD';
                }
             }">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                        <i data-lucide="droplet" class="w-5 h-5 text-cyan-400"></i>
                        Record Aquarium Water Test
                    </h1>
                    <p class="text-sm text-slate-400">Log temperature, chemical parameters, and receive automatic condition grading.</p>
                </div>
                <!-- Live Computed Status Badge -->
                <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 text-center">
                    <span class="text-[9px] uppercase tracking-wider text-slate-500 font-semibold block">Calculated Status</span>
                    <span class="text-xs font-bold font-mono"
                          :class="{
                            'text-emerald-400': computeStatus() === 'GOOD',
                            'text-amber-400': computeStatus() === 'WARNING',
                            'text-rose-400': computeStatus() === 'CHECK'
                          }"
                          x-text="computeStatus()">
                    </span>
                </div>
            </div>

            <form action="{{ route('water-logs.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Tank Selection & Recorded Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Target Aquarium / Setup <span class="text-rose-400">*</span>
                        </label>
                        <select name="tank_id" id="tank_id" required class="w-full bg-slate-950 border @error('tank_id') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">Select Tank</option>
                            @foreach($tanks as $tk)
                                <option value="{{ $tk->id }}" {{ (old('tank_id', $selectedTankId) == $tk->id) ? 'selected' : '' }}>
                                    {{ $tk->tank_code }} &bull; {{ $tk->name }} ({{ $tk->volume_liters }}L - {{ $tk->purpose }})
                                </option>
                            @endforeach
                        </select>
                        @error('tank_id')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="recorded_at" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Test Timestamp <span class="text-rose-400">*</span>
                        </label>
                        <input type="datetime-local" name="recorded_at" id="recorded_at" value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-cyan-500 font-mono">
                    </div>
                </div>

                <!-- Parameters Matrix -->
                <div class="p-4 bg-slate-950/60 border border-slate-800/80 rounded-xl space-y-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        Water Chemistry & Parameters
                    </span>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <!-- Temperature -->
                        <div>
                            <label for="temperature" class="block text-xs text-slate-300 mb-1">Temperature (°C)</label>
                            <input type="number" step="0.1" name="temperature" id="temperature" x-model="temp" placeholder="26.0" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-cyan-300 font-mono font-bold focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- pH -->
                        <div>
                            <label for="ph" class="block text-xs text-slate-300 mb-1">pH Level</label>
                            <input type="number" step="0.05" name="ph" id="ph" x-model="ph" placeholder="7.2" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white font-mono font-bold focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- Ammonia -->
                        <div>
                            <label for="ammonia" class="block text-xs text-slate-300 mb-1">Ammonia NH? (ppm)</label>
                            <input type="number" step="0.01" name="ammonia" id="ammonia" x-model="ammonia" placeholder="0.00" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:border-cyan-500" :class="parseFloat(ammonia) > 0 ? 'text-rose-400' : 'text-emerald-400'">
                        </div>

                        <!-- Nitrite -->
                        <div>
                            <label for="nitrite" class="block text-xs text-slate-300 mb-1">Nitrite NO₂⁻ (ppm)</label>
                            <input type="number" step="0.01" name="nitrite" id="nitrite" x-model="nitrite" placeholder="0.00" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:border-cyan-500" :class="parseFloat(nitrite) > 0 ? 'text-rose-400' : 'text-emerald-400'">
                        </div>

                        <!-- Nitrate -->
                        <div>
                            <label for="nitrate" class="block text-xs text-slate-300 mb-1">Nitrate NO₃⁻ (ppm)</label>
                            <input type="number" step="1" name="nitrate" id="nitrate" x-model="nitrate" placeholder="20" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white font-mono font-bold focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- TDS -->
                        <div>
                            <label for="tds" class="block text-xs text-slate-300 mb-1">TDS (ppm, optional)</label>
                            <input type="number" step="1" name="tds" id="tds" x-model="tds" placeholder="e.g. 180" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 font-mono focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- Observations & Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Water Clarity & Behavioral Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Water crystal clear, fish active, slight biofilm on surface..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('water-logs.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-semibold rounded-lg shadow-lg shadow-cyan-950/40 transition">Save Water Test</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
