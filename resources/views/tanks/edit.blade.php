<x-app-layout title="Edit {{ $tank->tank_code }}">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('tanks.index') }}" class="hover:text-cyan-400 transition">Tanks</a>
            <span>/</span>
            <a href="{{ route('tanks.show', $tank) }}" class="hover:text-cyan-400 transition">{{ $tank->tank_code }}</a>
            <span>/</span>
            <span class="text-white">Edit Setup</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm"
             x-data="{
                tankCode: '{{ old('tank_code', $tank->tank_code) }}',
                tankType: '{{ old('tank_type', $tank->tank_type) }}',
                length: '{{ old('length', $tank->length) }}',
                width: '{{ old('width', $tank->width) }}',
                height: '{{ old('height', $tank->height) }}',
                volume: '{{ old('volume_liters', $tank->volume_liters) }}',
                calcVolume() {
                    let l = parseFloat(this.length);
                    let w = parseFloat(this.width);
                    let h = parseFloat(this.height);
                    if (l > 0 && w > 0 && h > 0) {
                        this.volume = ((l * w * h) / 1000).toFixed(1);
                    }
                }
             }">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="pencil" class="w-5 h-5 text-cyan-400"></i>
                Edit Tank: {{ $tank->tank_code }} ({{ $tank->name }})
            </h1>
            <p class="text-sm text-slate-400 mb-6">Update dimensions, purpose, status, or equipment notes.</p>

            <form action="{{ route('tanks.update', $tank) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Identification -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tank_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Tank Code <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="tank_code" id="tank_code" x-model="tankCode" required class="w-full bg-slate-950 border @error('tank_code') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-cyan-400 font-mono font-bold focus:outline-none focus:border-cyan-500 transition">
                        @error('tank_code')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Display Name / Label <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $tank->name) }}" required class="w-full bg-slate-950 border @error('name') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
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
                        <input type="text" name="location" id="location" value="{{ old('location', $tank->location) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
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
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Length (cm)</label>
                            <input type="number" step="0.1" name="length" x-model="length" @input="calcVolume()" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Width (cm)</label>
                            <input type="number" step="0.1" name="width" x-model="width" @input="calcVolume()" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Height (cm)</label>
                            <input type="number" step="0.1" name="height" x-model="height" @input="calcVolume()" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-semibold text-cyan-400">Total Liters *</label>
                            <input type="number" step="0.1" name="volume_liters" id="volume_liters" x-model="volume" required class="w-full bg-slate-950 border @error('volume_liters') border-rose-500 @else border-cyan-800/60 @enderror rounded-lg px-3 py-2 text-sm text-cyan-400 font-bold focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- Purpose & Operational Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="purpose" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Tank Purpose <span class="text-rose-400">*</span>
                        </label>
                        <select name="purpose" id="purpose" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="BREEDING" {{ old('purpose', $tank->purpose) === 'BREEDING' ? 'selected' : '' }}>BREEDING (Spawning / Pairing)</option>
                            <option value="GROWOUT" {{ old('purpose', $tank->purpose) === 'GROWOUT' ? 'selected' : '' }}>GROWOUT (Fry & Juvenile Raising)</option>
                            <option value="QUARANTINE" {{ old('purpose', $tank->purpose) === 'QUARANTINE' ? 'selected' : '' }}>QUARANTINE (New or Sick Isolations)</option>
                            <option value="DISPLAY" {{ old('purpose', $tank->purpose) === 'DISPLAY' ? 'selected' : '' }}>DISPLAY (Showcase)</option>
                            <option value="HOLDING" {{ old('purpose', $tank->purpose) === 'HOLDING' ? 'selected' : '' }}>HOLDING (Conditioning)</option>
                            <option value="SICK" {{ old('purpose', $tank->purpose) === 'SICK' ? 'selected' : '' }}>SICK / HOSPITAL TANK</option>
                            <option value="SALES" {{ old('purpose', $tank->purpose) === 'SALES' ? 'selected' : '' }}>SALES (Ready for Customers)</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Operational Status <span class="text-rose-400">*</span>
                        </label>
                        <select name="status" id="status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="ACTIVE" {{ old('status', $tank->status) === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Occupied or Running)</option>
                            <option value="EMPTY" {{ old('status', $tank->status) === 'EMPTY' ? 'selected' : '' }}>EMPTY (Cleaned & Ready)</option>
                            <option value="MAINTENANCE" {{ old('status', $tank->status) === 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE (Cycling / Cleaning)</option>
                            <option value="INACTIVE" {{ old('status', $tank->status) === 'INACTIVE' ? 'selected' : '' }}>INACTIVE (Stored / Decommissioned)</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Setup & Equipment Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">{{ old('notes', $tank->notes) }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('tanks.show', $tank) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-semibold rounded-lg shadow-lg shadow-cyan-950/40 transition">Update Tank</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
