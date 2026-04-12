<x-app-layout>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Arahan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 ">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Review</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 ">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">In Approval</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $stats['in_approval'] }}</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 ">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Approved/Selesai</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['approved'] }}</p>
            </div>
        </div>

        {{-- Actions Bar --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center flex-wrap gap-4">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight  underline decoration-blue-500 decoration-4 underline-offset-8">Monitoring Tindak Lanjut</h3>

                <form action="{{ route('tindaklanjut.index') }}" method="GET" class="flex gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari strategi arahan..."
                            class="pl-4 pr-10 py-2 border-slate-200 rounded-xl w-64 text-xs font-bold focus:ring-slate-900">
                    </div>
                    <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl font-black text-xs uppercase hover:bg-slate-800 transition shadow-lg shadow-slate-200">Cari</button>
                    @if(request('search'))
                    <a href="{{ route('tindaklanjut.index') }}" class="bg-rose-50 text-rose-600 px-4 py-2 rounded-xl font-black text-xs uppercase hover:bg-rose-100 transition">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Butir Arahan RUPS</th>
                            <th class="px-8 py-4">Unit Pelaksana</th>
                            <th class="px-8 py-4 text-center">Status Laporan</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($arahan as $item)
                        @php
                        $latestTl = $item->tindakLanjut->sortByDesc('created_at')->first();
                        $count = $item->tindakLanjut->count();
                        $isFullyApproved = $latestTl ? $latestTl->approvals()->where('stage', 5)->where('status', 'approved')->exists() : false;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-5">
                                <div class="max-w-md">
                                    <p class="font-bold text-slate-800 text-sm leading-snug group-hover:text-blue-600 transition">{{ $item->strategi }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-medium tracking-tighter">
                                        Ref: {{ $item->keputusan->nomor_keputusan }} | {{ $item->keputusan->periode_year }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-black text-slate-600 bg-slate-100 px-3 py-1 rounded-lg uppercase tracking-tight">
                                    {{ $item->unitKerja->name }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
    <div class="flex flex-col items-center gap-1.5">
        @php
            $latestTl = $item->tindakLanjut->sortByDesc('created_at')->first();
            $count = $item->tindakLanjut->count();
            
            // Logika Cek Status
            $isFullyApproved = $latestTl ? $latestTl->approvals()->where('stage', 5)->where('status', 'approved')->exists() : false;
            $isRevisi = $latestTl && $latestTl->status == 'rejected';
            $isGlobalTD = ($item->keputusan->status ?? '') == 'TD'; 
        @endphp

        @if(!$latestTl)
            {{-- BD - BELUM DITINDAKLANJUTI (SOFT ROSE/MERAH MUDA) --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-black uppercase tracking-tighter">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                BD - Belum Ditindaklanjuti
            </span>
        @elseif($isFullyApproved)
            {{-- S - SELESAI (SOFT EMERALD/HIJAU MUDA) --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-black uppercase tracking-tighter shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                S - Selesai 100%
            </span>
        @elseif($isGlobalTD)
            {{-- TD - TIDAK DAPAT DITINDAKLANJUTI (SOFT SLATE/ABU-ABU) --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-full text-[10px] font-black uppercase tracking-tighter shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                TD - Tidak Dapat TL
            </span>
        @elseif($isRevisi)
            {{-- BS - PERLU REVISI (SOFT ORANGE) --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 border border-orange-200 rounded-full text-[10px] font-black uppercase tracking-tighter shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce"></span>
                BS - Perlu Revisi
            </span>
        @else
            {{-- BS - SEDANG BERJALAN (SOFT AMBER/KUNING MUDA) --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-black uppercase tracking-tighter shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                BS - Sedang Berjalan
            </span>
        @endif

        @if($latestTl)
            <span class="text-[9px] text-slate-400 font-bold italic">
                ({{ $count }} Laporan Masuk)
            </span>
        @endif
    </div>
</td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('tindaklanjut.show_arahan', $item->id) }}" class="p-2.5 bg-slate-50 text-slate-400 hover:text-blue-600 rounded-xl transition hover:bg-blue-50" title="Riwayat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </a>
                                    @can('create_tindak_lanjut')
                                    <a href="{{ route('tindaklanjut.create', ['arahan_id' => $item->id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                                        + Input
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <p class="text-slate-400 italic text-sm font-bold">Tidak ada arahan ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-50">
                {{ $arahan->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>