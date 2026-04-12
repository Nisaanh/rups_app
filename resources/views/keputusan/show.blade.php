<x-app-layout>
    <div class="space-y-6">
        {{-- Top Bar --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('keputusan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Daftar Keputusan
            </a>

            <div class="flex gap-2">
                @if(in_array($keputusan->status, ['BD', 'BS']))
                <a href="{{ route('arahan.create', ['keputusan_id' => $keputusan->id]) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-100 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Arahan
                </a>
                @endif
            </div>
        </div>

        {{-- Info Card Utama --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-10 bg-slate-900 text-white relative">
                <div class="relative z-10 flex justify-between items-end">
                    <div>
                        <span class="px-3 py-1 bg-blue-500 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/20">
                            Periode {{ $keputusan->periode_year }}
                        </span>
                        <h3 class="text-3xl font-black mt-4 tracking-tight uppercase">{{ $keputusan->nomor_keputusan }}</h3>
                        <p class="text-slate-400 text-sm mt-1 font-medium tracking-wide">ID Registrasi: #{{ $keputusan->id }} | Dibuat oleh: {{ $keputusan->creator->name }}</p>
                    </div>

                    <div class="text-right hidden md:block border-l border-white/10 pl-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Waktu Input</p>
                        <p class="text-xl font-bold">{{ $keputusan->created_at->format('d/m/Y') }}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ $keputusan->created_at->format('H:i') }} WIB</p>
                    </div>
                </div>
            </div>

            {{-- Summary Arahan --}}
            <div class="p-10">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                    <h4 class="font-black text-slate-800 uppercase text-xs tracking-widest flex items-center">
                        <span class="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
                        Butir-Butir Arahan Pelaksanaan ({{ $keputusan->arahan->count() }})
                    </h4>
                </div>

                <div class="space-y-6">
                    @forelse($keputusan->arahan as $index => $arahan)
                    <div class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100 hover:shadow-xl hover:shadow-slate-200/50 transition duration-300">
                        <div class="flex justify-between items-start gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs font-black">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="px-3 py-1 bg-white border border-slate-200 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-tight shadow-sm">
                                        {{ $arahan->unitKerja->name ?? 'N/A' }}
                                    </span>
                                    <div class="h-1 w-1 bg-slate-300 rounded-full"></div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PIC: {{ $arahan->pic->name ?? '-' }}</p>
                                </div>

                                <p class="text-slate-700 leading-relaxed font-medium text-lg mb-6">"{{ $arahan->strategi }}"</p>

                                <div class="flex items-center gap-6">
                                    <div class="flex items-center text-slate-400">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-[10px] font-black uppercase">{{ \Carbon\Carbon::parse($arahan->tanggal_terbit)->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center text-blue-500">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span class="text-[10px] font-black uppercase">{{ $arahan->tindakLanjut->count() }} Progres Terdaftar</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-row gap-2">
                                <a href="{{ route('tindaklanjut.show_arahan', $arahan) }}"
                                    class="p-3 bg-white text-slate-400 hover:text-blue-600 rounded-2xl shadow-sm border border-slate-100 transition active:scale-90">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                @can('edit_arahan')
                                @if($arahan->status === 'draft')
                                <a href="{{ route('arahan.edit', $arahan) }}"
                                    class="p-3 bg-white text-slate-400 hover:text-emerald-600 rounded-2xl shadow-sm border border-slate-100 transition active:scale-90"
                                    title="Edit Arahan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @else
                                {{-- Arahan sudah dikirim, tampilkan ikon terkunci --}}
                                <span class="p-3 bg-slate-50 text-slate-300 rounded-2xl border border-slate-100 cursor-not-allowed"
                                    title="Arahan sudah dikirim, tidak bisa diedit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                @endif
                                @endcan

                                @can('delete_arahan')
                                @if($arahan->status === 'draft')
                                <form action="{{ route('arahan.destroy', $arahan) }}" method="POST"
                                    onsubmit="return confirm('Hapus arahan ini? Tindakan tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-3 bg-white text-slate-400 hover:text-rose-600 rounded-2xl shadow-sm border border-slate-100 transition active:scale-90"
                                        title="Hapus Arahan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center">
                        <p class="text-slate-400 italic text-sm font-bold">Belum ada arahan yang ditambahkan untuk keputusan ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>