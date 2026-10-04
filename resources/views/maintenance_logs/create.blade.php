<x-app-layout title="Record Maintenance">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('maintenance.index') }}" class="hover:text-teal-400 transition">Maintenance</a>
            <span>/</span>
            <span class="text-white">Record Husbandry Task</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                type: '{{ old('maintenance_type', 'WATER_CHANGE') }}',
                wcPct: '{{ old('water_change_percentage', '30') }}'
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="wrench" class="w-5 h-5 text-teal-400"></i>
                Record Tank Maintenance
            </h1>
            <p class="text-sm text-slate-400 mb-6">Log water changes, mechanical filtration cleans, and equipment inspections.</p>

            <form action="{{ route('maintenance.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Tank Selection -->
                <div>
                    <label for="tank_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Target Tank <span class="text-rose-400">*</span>
                    </label>
                    <select name="tank_id" id="tank_id" required class="w-full bg-slate-950 border @error('tank_id') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition">
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

                <!-- Maintenance Type & Water Change % -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="maintenance_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Task Performed <span class="text-rose-400">*</span>
                        </label>
                        <select name="maintenance_type" id="maintenance_type" x-model="type" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition">
                            <option value="WATER_CHANGE">Water Change</option>
                            <option value="FILTER_CLEANING">Filter Cleaning / Sponge Rinse</option>
                            <option value="TANK_CLEANING">Tank Scrub & Gravel Vac</option>
                            <option value="EQUIPMENT_CHECK">Heater / Pump Inspection</option>
                            <option value="OTHER">Other Husbandry Task</option>
                        </select>
                    </div>

                    <div x-show="type === 'WATER_CHANGE'">
                        <label for="water_change_percentage" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Water Volume Replaced (%)
                        </label>
                        <input type="number" min="0" max="100" name="water_change_percentage" id="water_change_percentage" x-model="wcPct" placeholder="30" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-teal-300 font-mono font-bold focus:outline-none focus:border-teal-500">
                    </div>
                </div>

                <!-- Timestamp -->
                <div>
                    <label for="performed_at" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Performed At <span class="text-rose-400">*</span>
                    </label>
                    <input type="datetime-local" name="performed_at" id="performed_at" value="{{ old('performed_at', now()->format('Y-m-d\TH:i')) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2 text-sm text-white font-mono focus:outline-none focus:border-teal-500">
                </div>

                <!-- Observations & Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Details & Conditioner Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Conditioner added (Prime 2 drops/gal), glass wiped, filter media rinsed in siphoned water..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-teal-950/40 transition">Record Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
