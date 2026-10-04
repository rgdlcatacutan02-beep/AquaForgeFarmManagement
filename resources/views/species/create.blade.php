<x-app-layout title="Add Species">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('species.index') }}" class="hover:text-cyan-400 transition">Species</a>
            <span>/</span>
            <span class="text-white">Add New Species</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-6 shadow-xl backdrop-blur-sm">
            <h1 class="text-xl font-bold text-white mb-1 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-cyan-400"></i>
                Add New Species
            </h1>
            <p class="text-sm text-slate-400 mb-6">Register a new aquatic species kept or bred in your facility.</p>

            <form action="{{ route('species.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Common Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Guppy, Flowerhorn, Blue Crayfish" required class="w-full bg-slate-950 border @error('name') border-rose-500 @else border-slate-800 @enderror rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                    @error('name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scientific_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Scientific Name <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <input type="text" name="scientific_name" id="scientific_name" value="{{ old('scientific_name') }}" placeholder="e.g. Poecilia reticulata, Cherax quadricarinatus" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition font-mono">
                    @error('scientific_name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Husbandry & Breeding Notes <span class="text-slate-500 lowercase">(optional)</span>
                    </label>
                    <textarea name="description" id="description" rows="4" placeholder="Typical water parameters, ideal temperatures, dietary preferences, or breeding requirements..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500/20">
                    <label for="active" class="text-sm font-medium text-slate-300">
                        Mark as Active
                        <span class="block text-xs font-normal text-slate-500">Active species appear in dropdown menus for livestock and breeding creation.</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/80">
                    <a href="{{ route('species.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium rounded-lg transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-sm font-semibold rounded-lg shadow-lg shadow-cyan-950/40 transition">Save Species</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
