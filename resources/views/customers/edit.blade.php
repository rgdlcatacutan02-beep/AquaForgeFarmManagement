<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.show', $customer) }}" class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Edit Customer</h1>
                <p class="text-xs text-slate-400 mt-0.5">Modify contact info, address, or customer preferences</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Customer / Business Name <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500 @error('name') border-rose-500 @enderror">
                    @error('name')<p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Phone / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Shipping / Delivery Address</label>
                    <textarea name="address" rows="2"
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Preferences & Notes</label>
                    <textarea name="notes" rows="2"
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">{{ old('notes', $customer->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white shadow-md shadow-cyan-950 transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
