<x-app-layout title="Edit {{ $species->name }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('species.index') }}" class="hover:text-cyan-400 transition">Species</a>
            <span>/</span>
            <a href="{{ route('species.show', $species) }}" class="hover:text-cyan-400 transition">{{ $species->name }}</a>
            <span>/</span>
            <span class="text-white">Edit</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="pencil" class="w-5 h-5 text-cyan-400"></i>
                Edit Species: {{ $species->name }}
            </h1>
            <p class="text-sm text-slate-400 mb-6">Modify species details and status.</p>

            <form action="{{ route('species.update', $species) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Common Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $species->name) }}" required class="w-full bg-slate-950 border @error('name') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                    @error('name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scientific_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Scientific Name <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <input type="text" name="scientific_name" id="scientific_name" value="{{ old('scientific_name', $species->scientific_name) }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition font-mono">
                    @error('scientific_name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Husbandry & Breeding Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="description" id="description" rows="4" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">{{ old('description', $species->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active', $species->active) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500/20">
                    <label for="active" class="text-sm font-medium text-slate-300">
                        Mark as Active
                        <span class="block text-xs font-normal text-slate-500">Active species appear in dropdown menus across the farm system.</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('species.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-semibold rounded-lg shadow-lg shadow-cyan-950/40 transition">Update Species</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
