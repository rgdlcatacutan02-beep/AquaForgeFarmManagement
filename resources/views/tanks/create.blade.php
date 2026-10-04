<x-app-layout title="Add Tank">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('tanks.index') }}" class="hover:text-cyan-400 transition">Tanks</a>
            <span>/</span>
            <span class="text-white">Add New Tank</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                tankCode: '{{ old('tank_code', 'T00' . (\App\Models\Tank::max('id') + 1)) }}',
                tankType: '{{ old('tank_type', 'GLASS') }}',
                length: '{{ old('length') }}',
                width: '{{ old('width') }}',
                height: '{{ old('height') }}',
                volume: '{{ old('volume_liters') }}',
                calcVolume() {
                    let l = parseFloat(this.length);
                    let w = parseFloat(this.width);
                    let h = parseFloat(this.height);
                    if (l > 0 && w > 0 && h > 0) {
                        this.volume = ((l * w * h) / 1000).toFixed(1);
                    }
                },
                applyPreset(l, w, h, v) {
                    this.length = l;
                    this.width = w;
                    this.height = h;
                    this.volume = v;
                }
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-cyan-400"></i>
                Register Aquarium or Vat Setup
            </h1>
            <p class="text-sm text-slate-400 mb-6">Create a new tank, breeding tub, growout vat, or quarantine unit.</p>

            <form action="{{ route('tanks.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Basic Identification -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tank_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Tank Code <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="tank_code" id="tank_code" x-model="tankCode" required placeholder="e.g. T006, Q01, RACK-A1" class="w-full bg-slate-950 border @error('tank_code') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-cyan-400 font-mono font-bold focus:outline-none focus:border-cyan-500 transition">
                        @error('tank_code')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Display Name / Label <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="e.g. Blue Topaz Breeder, Kamfa Solo Tank" class="w-full bg-slate-950 border @error('name') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                        @error('name')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type & Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tank_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Tank Type <span class="text-rose-400">*</span>
                        </label>
                        <select name="tank_type" id="tank_type" x-model="tankType" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="GLASS">Glass Aquarium</option>
                            <option value="ACRYLIC">Acrylic Tank</option>
                            <option value="TUB">Breeding / Poly Tub</option>
                            <option value="GROWOUT_VAT">Growout Vat</option>
                            <option value="POND">Indoor/Outdoor Pond</option>
                            <option value="SUMP">Filtration / Sump Tank</option>
                        </select>
                    </div>

                    <div>
                        <label for="location" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Physical Location / Rack <span class="text-slate-500 lowercase">(optional)</span>
                        </label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="e.g. Rack A - Tier 2, Room 1, Outdoor Deck" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                        @error('location')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dimensions & Volume Calculator -->
                <div class="p-4 bg-slate-950/60 border border-slate-800/80 rounded-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                            <i data-lucide="calculator" class="w-4 h-4"></i>
                            Dimensions & Water Volume
                        </span>
                        <div class="flex gap-2">
                            <button type="button" @click="applyPreset(45, 30, 30, 40.5)" class="text-[11px] px-2 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded transition">45cm (~40L)</button>
                            <button type="button" @click="applyPreset(60, 30, 36, 64.8)" class="text-[11px] px-2 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded transition">2-Foot (~65L)</button>
                            <button type="button" @click="applyPreset(90, 45, 45, 182.2)" class="text-[11px] px-2 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded transition">3-Foot (~180L)</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Length (cm)</label>
                            <input type="number" step="0.1" name="length" x-model="length" @input="calcVolume()" placeholder="e.g. 60" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Width (cm)</label>
                            <input type="number" step="0.1" name="width" x-model="width" @input="calcVolume()" placeholder="e.g. 30" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Height (cm)</label>
                            <input type="number" step="0.1" name="height" x-model="height" @input="calcVolume()" placeholder="e.g. 36" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-semibold text-cyan-400">Total Liters *</label>
                            <input type="number" step="0.1" name="volume_liters" id="volume_liters" x-model="volume" required placeholder="Calculated" class="w-full bg-slate-950 border @error('volume_liters') border-rose-500 @else border-cyan-800/60 @enderror rounded-lg px-3 py-2 text-sm text-cyan-400 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                    @error('volume_liters')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose & Operational Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="purpose" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Tank Purpose <span class="text-rose-400">*</span>
                        </label>
                        <select name="purpose" id="purpose" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="BREEDING" {{ old('purpose') === 'BREEDING' ? 'selected' : '' }}>BREEDING (Spawning / Pairing)</option>
                            <option value="GROWOUT" {{ old('purpose', 'GROWOUT') === 'GROWOUT' ? 'selected' : '' }}>GROWOUT (Fry & Juvenile Raising)</option>
                            <option value="QUARANTINE" {{ old('purpose') === 'QUARANTINE' ? 'selected' : '' }}>QUARANTINE (New or Sick Isolations)</option>
                            <option value="DISPLAY" {{ old('purpose') === 'DISPLAY' ? 'selected' : '' }}>DISPLAY (Showcase)</option>
                            <option value="HOLDING" {{ old('purpose') === 'HOLDING' ? 'selected' : '' }}>HOLDING (Conditioning)</option>
                            <option value="SICK" {{ old('purpose') === 'SICK' ? 'selected' : '' }}>SICK / HOSPITAL TANK</option>
                            <option value="SALES" {{ old('purpose') === 'SALES' ? 'selected' : '' }}>SALES (Ready for Customers)</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Operational Status <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" id="status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="ACTIVE" {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Occupied or Running)</option>
                            <option value="EMPTY" {{ old('status') === 'EMPTY' ? 'selected' : '' }}>EMPTY (Cleaned & Ready)</option>
                            <option value="MAINTENANCE" {{ old('status') === 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE (Cycling / Cleaning)</option>
                            <option value="INACTIVE" {{ old('status') === 'INACTIVE' ? 'selected' : '' }}>INACTIVE (Stored / Decommissioned)</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Setup & Equipment Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Filter type (e.g. sponge, canister), heater wattage, substrate, lighting schedule..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">{{ old('notes') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('tanks.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-semibold rounded-lg shadow-lg shadow-cyan-950/40 transition">Create Tank</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
