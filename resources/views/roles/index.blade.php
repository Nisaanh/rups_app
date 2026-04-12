<x-app-layout>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 border-t-4 border-blue-500 transition hover:shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Role</p>
                        <p class="text-3xl font-black text-slate-900 mt-1">{{ $totalRoles }}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-2xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 border-t-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Permission</p>
                        <p class="text-3xl font-black text-emerald-600 mt-1">{{ $totalPermissions }}</p>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-2xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 border-t-4 border-purple-500 transition hover:shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">User Ter-assign</p>
                        <p class="text-3xl font-black text-purple-600 mt-1">{{ $totalUsersWithRoles }}</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-2xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Section --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center flex-wrap gap-4">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('roles.create') }}" class="inline-flex items-center px-5 py-2.5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Role
                    </a>
                    <a href="{{ route('roles.user-assignments') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition">
                        Assign ke User
                    </a>
                </div>
                
                <form action="{{ route('roles.refresh-cache') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Refresh cache permission?')" class="p-2.5 text-amber-600 bg-amber-50 rounded-2xl hover:bg-amber-100 transition" title="Refresh Cache">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </form>
            </div>

            <div class="p-8 bg-slate-50/50">
                <form method="GET" class="flex gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama role..." 
                               class="w-full pl-5 pr-4 py-3 border-slate-200 rounded-2xl focus:ring-slate-900 focus:border-slate-900 font-bold text-xs transition">
                    </div>
                    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Cari</button>
                    @if($search)
                        <a href="{{ route('roles.index') }}" class="bg-white border border-slate-200 text-slate-400 px-6 py-3 rounded-2xl font-black text-xs uppercase hover:bg-slate-50 transition flex items-center">Reset</a>
                    @endif
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Role Name</th>
                            <th class="px-8 py-4">Permissions Preview</th>
                            <th class="px-8 py-4">Users Count</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($roles as $role)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center mr-3 shadow-sm text-white font-black text-xs">
                                        {{ strtoupper(substr($role->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm leading-none capitalize">{{ $role->name }}</p>
                                        
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[9px] font-black rounded-lg border border-blue-100 uppercase tracking-tighter">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="px-2 py-1 bg-slate-100 text-slate-400 text-[9px] font-black rounded-lg border border-slate-200">+{{ $role->permissions->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center text-xs font-bold text-slate-600">
                                    <span class="bg-slate-100 px-3 py-1 rounded-full">{{ $role->users->count() }} Users</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('roles.edit', $role) }}" class="p-2.5 bg-slate-50 text-slate-400 hover:text-emerald-600 rounded-xl transition hover:bg-emerald-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    
                                    @if(!in_array($role->name, ['admin', 'Super Admin']))
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Hapus role ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-slate-50 text-slate-400 hover:text-rose-600 rounded-xl transition hover:bg-rose-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <p class="text-slate-400 italic text-sm font-bold">Data role tidak ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($roles->hasPages())
            <div class="p-8 border-t border-slate-50">
                {{ $roles->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>