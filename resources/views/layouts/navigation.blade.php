<nav x-data="{ open: false }" class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-center">
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">
                    @if(request()->routeIs('dashboard'))
                    Dashboard
                    @elseif(request()->routeIs('keputusan.*'))
                    Keputusan RUPS
                    @elseif(request()->routeIs('tindaklanjut.*'))
                    Tindak Lanjut
                    @elseif(request()->routeIs('approval.*'))
                    Persetujuan (Approval)
                    @elseif(request()->routeIs('users.*'))
                    Managemen User
                    @elseif(request()->routeIs('roles.*'))
                    Manajemen Role & Permission
                    @elseif(request()->routeIs('unit-kerja.*'))
                    Manajemen Unit Kerja
                    @else
                    {{ __('Portal RUPS') }}
                    @endif
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        {{-- Dropdown Profil --}}
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-xl text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150 group">

                                        {{-- Lingkaran Inisial (Opsional) --}}
                                        <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center mr-3 font-black text-xs shadow-sm">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>

                                        {{-- Teks Nama dan Role --}}
                                        <div class="text-left">
                                            <div class="text-sm font-bold text-slate-800 leading-none">
                                                {{ Auth::user()->name }}
                                            </div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                                                {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}
                                            </div>
                                        </div>

                                        <div class="ms-2 text-slate-400 group-hover:text-slate-600 transition">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile Settings') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-slate-100 md:hidden">
                            <p class="text-xs text-slate-400">User:</p>
                            <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Akun') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                class="text-red-600 font-semibold"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>