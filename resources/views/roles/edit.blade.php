<x-app-layout>
    {{-- Header Section --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Role & Permission</h2>
        </div>
    </div>

    <div class="max-w-6xl">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Decorative Header --}}
            <div class="p-10 bg-slate-900 text-white relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-tight">{{ $role->name }}</h3>
                        <p class="text-slate-400 text-sm mt-1 font-medium tracking-wide italic">Konfigurasi hak akses sistem secara mendalam</p>
                    </div>
                    <div class="p-4 bg-white/10 rounded-2xl backdrop-blur-md border border-white/20">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl"></div>
            </div>

            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-10 space-y-10">
                    {{-- Input Name --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Nama Identitas Role</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" 
                               class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-800 transition {{ $isProtected ? 'bg-slate-50 cursor-not-allowed italic text-slate-400' : '' }}"
                               required {{ $isProtected ? 'readonly' : '' }}>
                        @if($isProtected)
                            <p class="text-[10px] text-amber-600 font-bold mt-2 flex items-center uppercase italic">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                Role Sistem: Nama tidak dapat dimodifikasi
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-slate-50 pt-10">
                        <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-6">Konfigurasi Hak Akses (Permissions)</label>
                        
                        @php
                            $permissionGroups = [
                                'keputusan' => 'Manajemen Keputusan RUPS',
                                'arahan'    => 'Sistem Arahan & Notulensi',
                                'tindak'    => 'Progress Tindak Lanjut',
                                'approve'   => 'Verifikasi & Approval',
                                'view'      => 'Monitoring & Insight',
                                'export'    => 'Pelaporan & Dokumen',
                                'manage'    => 'Sistem Management',
                                'user'      => 'Kontrol Pengguna',
                                'role'      => 'Kontrol Role & Akses',
                                'unit'      => 'Struktur Unit Kerja'
                            ];
                            
                            $grouped = $permissions->groupBy(function($permission) {
                                if(str_contains($permission->name, 'keputusan')) return 'keputusan';
                                if(str_contains($permission->name, 'arahan')) return 'arahan';
                                if(str_contains($permission->name, 'tindak')) return 'tindak';
                                if(str_contains($permission->name, 'approve')) return 'approve';
                                if(str_contains($permission->name, 'view')) return 'view';
                                if(str_contains($permission->name, 'export')) return 'export';
                                if(str_contains($permission->name, 'manage')) return 'manage';
                                if(str_contains($permission->name, 'user')) return 'user';
                                if(str_contains($permission->name, 'role')) return 'role';
                                if(str_contains($permission->name, 'unit')) return 'unit';
                                return 'other';
                            });
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($grouped as $group => $perms)
                            <div class="bg-slate-50 rounded-[2rem] p-6 border border-slate-100 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 group">
                                <h4 class="font-black text-slate-800 mb-4 flex items-center text-xs border-b border-slate-100 pb-3">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 group-hover:scale-150 transition-transform"></span>
                                    {{ strtoupper($permissionGroups[$group] ?? $group) }}
                                </h4>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($perms as $permission)
                                    <label class="flex items-center cursor-pointer p-2 rounded-xl hover:bg-slate-50 transition">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               class="w-5 h-5 rounded-lg border-slate-300 text-slate-900 focus:ring-slate-900 transition"
                                               {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                        <span class="ml-3 text-xs font-bold text-slate-600 uppercase tracking-tight group-hover:text-slate-900">
                                            {{ str_replace('_', ' ', $permission->name) }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('roles.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-sm font-bold hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-slate-800 shadow-lg shadow-slate-200 transition active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Info Box --}}
        <div class="mt-8 bg-indigo-50 border-l-8 border-indigo-500 rounded-[2rem] p-8 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-indigo-500 p-2 rounded-xl">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-5">
                    <h4 class="text-sm font-black text-indigo-900 uppercase tracking-widest">Peringatan Sinkronisasi</h4>
                    <p class="mt-2 text-xs text-indigo-700 leading-relaxed font-bold">
                        Setiap perubahan yang Anda simpan akan berdampak instan. Seluruh user yang terikat pada role <span class="text-indigo-900 underline">{{ $role->name }}</span> akan segera mendapatkan pembaruan hak akses pada request (klik) berikutnya. Pastikan konfigurasi sudah sesuai dengan SOP yang berlaku.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>