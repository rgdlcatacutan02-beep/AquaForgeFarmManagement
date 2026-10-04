<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-white tracking-tight">
                {{ __('Profile & Farm Settings') }}
            </h1>
            <a href="{{ route('catalog.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-300 border border-cyan-800/60 text-xs font-semibold transition">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>View Public Catalog</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- Philippine Payments, Messenger & Farm Settings -->
        <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl shadow-sm">
            @include('profile.partials.update-payment-form')
        </div>

        <!-- Account Profile Info -->
        <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="p-6 bg-slate-900 border border-slate-800 rounded-xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
