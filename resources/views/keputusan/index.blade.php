<x-app-layout>
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Daftar Keputusan RUPS</h2>

            @can('create_keputusan')
            <a href="{{ route('keputusan.create') }}" class="inline-flex items-center px-5 py-2.5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Keputusan
            </a>
            @endcan
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">ID & Nomor</th>
                            <th class="px-8 py-4">Periode</th>
                            <th class="px-8 py-4">Status Progres</th>
                            <th class="px-8 py-4">Dibuat Oleh</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($keputusan as $item)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-3 font-black text-xs">
                                        {{ $item->periode_year % 100 }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm mt-1 uppercase tracking-tighter">{{ $item->nomor_keputusan ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-black text-slate-700">{{ $item->periode_year }}</span>
                            </td>
                            <td class="px-8 py-5">
                                @php
                                $statusStyle = [
                                'BD' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Belum Dikirim'],
                                'BS' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Belum Selesai'],
                                'S' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => 'Selesai'],
                                ][$item->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'label' => $item->status];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-lg {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} text-[9px] font-black uppercase tracking-widest border border-current/10">
                                    <span class="w-1 h-1 rounded-full bg-current mr-2"></span>
                                    {{ $statusStyle['label'] }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-xs font-bold text-slate-600">{{ $item->creator->name ?? 'System' }}</p>
                                <p class="text-[9px] text-slate-400 mt-0.5 uppercase tracking-tighter">{{ $item->created_at->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('keputusan.show', $item) }}" class="p-2.5 bg-slate-50 text-slate-400 hover:text-blue-600 rounded-xl transition hover:bg-blue-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @can('edit_keputusan')
                                    @if($item->status === 'BD')
                                    {{-- Jika Draft: Icon plus/pensil dengan warna biru untuk "Lanjutkan" --}}
                                    <a href="{{ route('keputusan.edit', $item) }}"
                                        class="p-2.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition shadow-sm border border-blue-100"
                                        title="Lanjutkan Isi Arahan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </a>
                                    @else
                                    {{-- Jika Sudah Dikirim: Icon edit biasa (emerald) --}}
                                    <a href="{{ route('keputusan.edit', $item) }}"
                                        class="p-2.5 bg-slate-50 text-slate-400 hover:text-emerald-600 rounded-xl transition hover:bg-emerald-50"
                                        title="Edit Data Keputusan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endif
                                    @endcan
                                    @can('delete_keputusan')
                                    <form action="{{ route('keputusan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-slate-50 text-slate-400 hover:text-rose-600 rounded-xl transition hover:bg-rose-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic font-bold">Belum ada data keputusan yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($keputusan->hasPages())
            <div class="p-8 border-t border-slate-50">
                {{ $keputusan->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>