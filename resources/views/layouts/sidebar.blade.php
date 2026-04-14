<div class="w-64 bg-slate-900 text-slate-100 h-screen flex-shrink-0 flex flex-col shadow-xl transition-all duration-300">
    {{-- Logo / Brand --}}
    <div class="p-6 text-center border-b border-slate-800">
        <span class="text-xl font-extrabold tracking-wider text-blue-500">RUPS</span>
        <span class="text-xl font-light text-slate-300">MONITORING</span>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-2">

        

        {{-- 1. SECTION VIEW & MONITORING --}}
        @can('view_dashboard')
        <div class="pb-2">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">Main Menu</p>
            <a href="{{ route('dashboard') }}"
                class="flex items-center mt-2 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
        </div>
        @endcan

        @if(auth()->user()->can('manage_users') || auth()->user()->can('manage_roles'))
        <div class="pt-4 pb-2 border-t border-slate-800">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest text-yellow-500">Administrator</p>

            @can('manage_users')
            <a href="{{ route('users.index') }}" class="flex items-center mt-2 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                <span class="text-sm font-medium">User Management</span>
            </a>
            @endcan

            @can('manage_roles')
            <a href="{{ route('roles.index') }}" class="flex items-center mt-1 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                <span class="text-sm font-medium">Roles & Permissions</span>
            </a>
            @endcan

            @can('manage_unit_kerja')
            <a href="{{ route('unit-kerja.index') }}" class="flex items-center mt-1 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                <span class="text-sm font-medium">Unit Kerja</span>
            </a>
            @endcan
        </div>
        @endif

        {{-- 2. SECTION OPERATIONAL (Berdasarkan Screenshot) --}}
        <div class="pt-4 pb-2 border-t border-slate-800">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest text-blue-400">Monitoring & Input</p>

            {{-- Menu Keputusan RUPS --}}
            @can('view_keputusan')
            <a href="{{ route('keputusan.index') }}" class="flex items-center mt-2 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('keputusan.*') ? 'bg-blue-600 text-white' : '' }}">
                <span class="text-sm font-medium">Keputusan RUPS</span>
            </a>
            @endcan

            {{-- Menu Arahan --}}
            {{-- @can('view_arahan')
        <a href="{{ route('arahan.index') }}" class="flex items-center mt-1 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('arahan.*') ? 'bg-blue-600 text-white' : '' }}">
            <span class="text-sm font-medium">Data Arahan</span>
            </a>
            @endcan --}}

            {{-- Menu Tindak Lanjut --}}
            @can('view_tindak_lanjut')
            <a href="{{ route('tindaklanjut.index') }}" class="flex items-center mt-1 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('tindaklanjut.*') ? 'bg-blue-600 text-white' : '' }}">
                <span class="text-sm font-medium">Tindak Lanjut</span>
            </a>
            @endcan
        </div>

        @if(
        auth()->user()->can('approve_stage_1') ||
        auth()->user()->can('approve_stage_2') ||
        auth()->user()->can('approve_stage_3') ||
        auth()->user()->can('approve_stage_4') ||
        auth()->user()->can('approve_stage_5')
        )
        <div class="pt-4 pb-2 border-t border-slate-800">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest text-orange-400">Verification</p>
            <a href="{{ route('approval.index') }}"
                class="flex items-center mt-2 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('approval.*') ? 'bg-blue-600 text-white' : '' }}">
                <span class="text-sm font-medium">Persetujuan (Approval)</span>
            </a>
        </div>
        @endif

       @can('export_report')
<a href="{{ route('export.index') }}"
    class="flex items-center mt-1 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('export.*') ? 'bg-blue-600 text-white' : '' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
    </svg>
    <span class="text-sm font-medium">Export Data</span>
</a>
@endcan
    </nav>
</div>