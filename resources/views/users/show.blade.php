<x-app-layout>
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>
            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-5 py-2 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Profil
            </a>
        </div>

        {{-- Grid Utama: Sekarang Menjadi 2 Kolom Saja --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- KOLOM KIRI: Identitas & Info Detail --}}
            <div class="space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden h-full">
                    <div class="p-8 bg-slate-900 text-white flex flex-col items-center relative overflow-hidden">
                        <div class="relative z-10 text-center">
                            <div class="w-24 h-24 bg-white/10 rounded-3xl backdrop-blur-md flex items-center justify-center mx-auto mb-4 border border-white/20 shadow-inner text-4xl font-black">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight">{{ $user->name }}</h3>
                            <p class="text-slate-400 font-medium text-sm mt-1 uppercase tracking-widest">{{ $user->badge }}</p>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
                    </div>
                    
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Email Address</p>
                            <p class="text-slate-700 font-bold break-all">{{ $user->email }}</p>
                        </div>
                        <div class="group">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Unit Kerja</p>
                            <p class="text-slate-700 font-bold">{{ $user->unitKerja ? $user->unitKerja->name : 'Unassigned' }}</p>
                        </div>
                        <div class="group">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Role / Akses</p>
                            <span class="inline-block mt-1 px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-tight">
                                {{ $user->roles->first() ? $user->roles->first()->name : 'No Role' }}
                            </span>
                        </div>
                        <div class="group">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Status Akun</p>
                            <span class="text-xs font-bold {{ $user->status == 'active' ? 'text-emerald-500' : 'text-rose-500' }} uppercase">
                                ● {{ $user->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Statistik Kontribusi --}}
            <div class="space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 h-full">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center">
                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-2"></span>
                        Statistik Kontribusi
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 transition hover:bg-blue-50/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Keputusan</p>
                            <p class="text-4xl font-black text-slate-900">{{ $stats['total_keputusan'] ?? 0 }}</p>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 transition hover:bg-indigo-50/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Arahan</p>
                            <p class="text-4xl font-black text-slate-900">{{ $stats['total_arahan'] ?? 0 }}</p>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 transition hover:bg-emerald-50/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">T. Lanjut</p>
                            <p class="text-4xl font-black text-slate-900">{{ $stats['total_tindak_lanjut'] ?? 0 }}</p>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 transition hover:bg-amber-50/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Approval</p>
                            <p class="text-4xl font-black text-slate-900">{{ $stats['total_approvals'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Bawahan: Tetap Full Width di Bawah --}}
        @if($subordinates->count() > 0)
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Daftar Bawahan Langsung</h3>
                <span class="bg-slate-100 text-slate-500 text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest">
                    {{ $subordinates->count() }} Anggota
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-50">
                        @foreach($subordinates as $sub)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-xs">
                                        {{ substr($sub->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-slate-800 leading-none">{{ $sub->name }}</p>
                                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold">{{ $sub->badge }} • {{ $sub->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('users.show', $sub) }}" class="text-xs font-black text-blue-600 hover:underline uppercase tracking-tighter">Detail Profil →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>