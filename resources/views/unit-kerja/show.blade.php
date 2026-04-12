<x-app-layout>
    @php
    // Helper untuk membedakan warna level unit secara dinamis
    function getLevelTheme($level) { 
        switch($level) {
            case 'Direktorat': 
                return ['bg' => 'bg-slate-900', 'badge' => 'bg-blue-500', 'text' => 'text-blue-500', 'border' => 'border-blue-500'];
            case 'Divisi': 
                return ['bg' => 'bg-indigo-900', 'badge' => 'bg-emerald-500', 'text' => 'text-emerald-500', 'border' => 'border-emerald-500'];
            case 'Departemen': 
                return ['bg' => 'bg-slate-800', 'badge' => 'bg-amber-500', 'text' => 'text-amber-500', 'border' => 'border-amber-500'];
            case 'Seksi': 
                return ['bg' => 'bg-slate-700', 'badge' => 'bg-purple-500', 'text' => 'text-purple-500', 'border' => 'border-purple-500'];
            case 'Sub Seksi': 
                return ['bg' => 'bg-slate-600', 'badge' => 'bg-rose-500', 'text' => 'text-rose-500', 'border' => 'border-rose-500'];
            default: 
                return ['bg' => 'bg-slate-500', 'badge' => 'bg-slate-400', 'text' => 'text-slate-400', 'border' => 'border-slate-400'];
        }
    }
    $theme = getLevelTheme($unitKerja->level);
    @endphp

    <div class="space-y-6">
        
        {{-- Header Bar: Tombol Kembali & Judul --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                {{ $unitKerja->name }}
            </h2>
            <a href="{{ route('unit-kerja.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Stats Cards Overview --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 border-t-4 {{ $theme['border'] }}">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total User</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_users'] }}</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 border-t-4 {{ $theme['border'] }}">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bawahan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_children'] }}</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 border-t-4 {{ $theme['border'] }}">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Arahan</p>
                <p class="text-2xl font-black mt-1">{{ $stats['total_arahan'] }}</p>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 border-t-4 {{ $theme['border'] }}">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending</p>
                <p class="text-2xl font-black mt-1">{{ $stats['pending_approvals'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Kolom Kiri: Profil & User --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Profil Unit --}}
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 {{ $theme['bg'] }} text-white text-center relative overflow-hidden transition-colors duration-500">
                        <div class="relative z-10">
                            <div class="w-20 h-20 bg-white/10 rounded-2xl backdrop-blur-md flex items-center justify-center mx-auto mb-4 border border-white/20 shadow-inner text-3xl font-black">
                                {{ substr($unitKerja->name, 0, 1) }}
                            </div>
                            <h3 class="text-xl font-bold tracking-tight">{{ $unitKerja->name }}</h3>
                            <span class="inline-block mt-2 px-4 py-1.5 {{ $theme['badge'] }} rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg">
                                {{ $unitKerja->level }}
                            </span>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-[10px] text-slate-400 block uppercase font-black tracking-widest">Atasan Langsung</label>
                                <p class="text-slate-800 font-bold mt-1">
                                    {{ $unitKerja->parent ? $unitKerja->parent->name : 'Root (Pimpinan)' }}
                                </p>
                            </div>
                            <a href="{{ route('unit-kerja.edit', $unitKerja) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-blue-600 rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Daftar User --}}
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                    <h3 class="font-bold text-slate-800 mb-6 flex items-center">
                        <span class="w-1.5 h-1.5 {{ $theme['badge'] }} rounded-full mr-2"></span>
                        User Terdaftar
                    </h3>
                    <div class="space-y-4">
                        @forelse($unitKerja->users as $user)
                            <div class="flex items-center space-x-3 p-3 bg-slate-50 rounded-2xl">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 leading-none">{{ $user->name }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-tighter">{{ $user->badge ?? 'No Badge' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 italic text-center py-4">Tidak ada user terdaftar</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Struktur Bawahan --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">Struktur Bawahan</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest">
                            {{ count($descendants) }} Unit Terdeteksi
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50">
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Nama Unit</th>
                                    <th class="px-6 py-4">Level</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($descendants as $desc)
                                    @php $descTheme = getLevelTheme($desc->level); @endphp
                                    <tr class="hover:bg-slate-50/80 transition group">
                                        <td class="px-6 py-4 font-bold text-slate-700">{{ $desc->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 bg-white border {{ $descTheme['border'] }} {{ $descTheme['text'] }} rounded-lg text-[10px] font-black uppercase tracking-tight">
                                                {{ $desc->level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('unit-kerja.show', $desc->id) }}" class="inline-flex items-center text-xs font-black text-blue-600 hover:text-blue-800">
                                                Detail
                                                <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-20 text-center">
                                            <svg class="mx-auto h-12 w-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                                            </svg>
                                            <p class="text-slate-400 italic text-sm mt-2">Unit ini tidak memiliki turunan unit kerja.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>