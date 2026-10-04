<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-base font-bold text-white tracking-tight">Admin Sign In</h2>
        <p class="text-xs text-slate-400 mt-1">Authorized farm management login for tank operations, breeding, and sales</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Owner / Admin Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="admin@aquaforge.test"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition @error('email') border-rose-500 @enderror">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] text-cyan-400 hover:text-cyan-300 transition" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="????????"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition @error('password') border-rose-500 @enderror">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500/20 focus:ring-offset-0">
                <span class="ms-2 text-xs text-slate-400">Remember credentials</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-950 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <i data-lucide="lock" class="w-4 h-4"></i>
                <span>Sign In to Dashboard</span>
            </button>
        </div>
    </form>
</x-guest-layout>
