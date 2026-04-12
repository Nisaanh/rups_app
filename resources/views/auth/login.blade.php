<x-guest-layout>
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-900 p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-xl bg-slate-800 border border-slate-700 mb-4">
                <svg class="h-8 w-8 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-white tracking-tight">RUPS</h2>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold"> PT Pupuk Sriwidjaja Palembang.</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email </label>
                    <div class="relative group">
                        <input id="email" type="email" name="email" required autofocus 
                            class="block w-full pl-4 pr-4 py-3 bg-slate-50 border-slate-200 border-2 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-0 transition-all text-slate-800 placeholder-slate-400"
                            placeholder="nama@email.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-bold text-sky-600 hover:text-sky-700" href="{{ route('password.request') }}">Lupa?</a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required 
                        class="block w-full px-4 py-3 bg-slate-50 border-slate-200 border-2 rounded-xl focus:bg-white focus:border-sky-500 focus:ring-0 transition-all text-slate-800 placeholder-slate-400"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    <label for="remember_me" class="ml-2 text-sm text-slate-600 cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-slate-200 transition-all active:scale-[0.98] uppercase tracking-wider text-sm">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <p class="text-center text-[10px] text-slate-400 mt-6 leading-relaxed">
        Sistem ini dilindungi enkripsi end-to-end.<br>
        &copy; {{ date('Y') }} PT Pupuk Sriwidjaja Palembang.
    </p>
</x-guest-layout>