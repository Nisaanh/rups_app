<div class="w-64 bg-slate-900 text-slate-100 h-screen flex-shrink-0 flex flex-col shadow-xl">

    {{-- Logo --}}
    <div class="p-6 text-center border-b border-slate-800">
        <span class="text-xl font-extrabold tracking-wider text-blue-500">RUPS</span>
        <span class="text-xl font-light text-slate-300">MONITORING</span>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-2">

        {{-- DASHBOARD --}}
        @can('view_dashboard')
        <div class="pb-2">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">
                Main Menu
            </p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center mt-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M3 12l2-2 7-7 7 7 2 2M5 10v10h14V10" />
                </svg>

                <span class="text-sm font-medium">Dashboard</span>
            </a>
        </div>
        @endcan


        {{-- ADMIN --}}
        @php
        $canAdmin = auth()->user()->can('manage_users')
        || auth()->user()->can('manage_roles')
        || auth()->user()->can('manage_unit_kerja');
        @endphp

        @if($canAdmin)
        <div class="pt-4 pb-2 border-t border-slate-800">
            <p class="px-4 text-[10px] font-semibold text-yellow-500 uppercase tracking-widest">
                Administrator
            </p>

            @can('manage_users')
            <a href="{{ route('users.index') }}"
                class="flex items-center mt-2 px-4 py-2.5 rounded-lg 
    {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

                <span class="text-sm font-medium">User Management</span>
            </a>
            @endcan

            @can('manage_roles')
            <a href="{{ route('roles.index') }}"
                class="flex items-center mt-1 px-4 py-2.5 rounded-lg 
                 {{ request()->routeIs('roles.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 8c-1.657 0-3-1.343-3-3S10.343 2 12 2s3 1.343 3 3-1.343 3-3 3zm0 2c3.314 0 6 2.686 6 6v4H6v-4c0-3.314 2.686-6 6-6z" />
                </svg>

                <span class="text-sm font-medium">Roles & Permissions</span>
            </a>
            @endcan

            @can('manage_unit_kerja')
            <a href="{{ route('unit-kerja.index') }}"
                class="flex items-center mt-1 px-4 py-2.5 rounded-lg 
                  {{ request()->routeIs('unit-kerja.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M3 21h18M9 8h6M9 12h6M9 16h6M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16" />
                </svg>

                <span class="text-sm font-medium">Unit Kerja</span>
            </a>
            @endcan
        </div>
        @endif


        {{-- MONITORING --}}
        @if(auth()->user()->can('view_keputusan') || auth()->user()->can('view_tindak_lanjut'))
        <div class="pt-4 pb-2 border-t border-slate-800">
            <p class="px-4 text-[10px] font-semibold text-blue-400 uppercase tracking-widest">
                Monitoring & Input
            </p>

            @can('view_keputusan')
            <a href="{{ route('keputusan.index') }}"
                class="flex items-center mt-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('keputusan.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M9 12h6M9 16h6M5 4h14v16H5z" />
                </svg>

                <span class="text-sm font-medium">Keputusan RUPS</span>
            </a>
            @endcan

            @can('view_tindak_lanjut')
            <a href="{{ route('tindaklanjut.index') }}"
                class="flex items-center mt-1 px-4 py-2.5 rounded-lg {{ request()->routeIs('tindaklanjut.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M9 12l2 2 4-4M5 3h14v18H5z" />
                </svg>

                <span class="text-sm font-medium">Tindak Lanjut</span>
            </a>
            @endcan
        </div>
        @endif


        {{-- APPROVAL --}}
        @php
        $canApproval = auth()->user()->can('approve_stage_1') ||
        auth()->user()->can('approve_stage_2') ||
        auth()->user()->can('approve_stage_3') ||
        auth()->user()->can('approve_stage_4') ||
        auth()->user()->can('approve_stage_5');
        @endphp

        @if($canApproval)
        <div class="pt-4 pb-2 border-t border-slate-800">
            <p class="px-4 text-[10px] font-semibold text-orange-400 uppercase tracking-widest">
                Approval
            </p>

            <a href="{{ route('approval.index') }}"
                class="flex items-center mt-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('approval.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>

                <span class="text-sm font-medium">Persetujuan</span>
            </a>
        </div>
        @endif


        {{-- EXPORT --}}
        @can('export_report')
        <a href="{{ route('export.index') }}"
            class="flex items-center mt-4 px-4 py-2.5 rounded-lg {{ request()->routeIs('export.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">

            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12v-8m0 8l-3-3m3 3l3-3" />
            </svg>

            <span class="text-sm font-medium">Export Data</span>
        </a>
        @endcan

    </nav>
</div>