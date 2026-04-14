<x-app-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Monitoring & Export</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Role: <span class="font-bold text-slate-600">{{ auth()->user()->getRoleNames()->first() }}</span>
                    @if(!$isAdmin)
                    — <span class="font-bold text-slate-600">{{ auth()->user()->unitKerja->name ?? '' }}</span>
                    @endif
                </p>
            </div>

            {{-- Tombol Export --}}
            @can('export_report')
            <a href="{{ route('export.download', request()->query()) }}"
                class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV ({{ $total }} data)
            </a>
            @endcan
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
            <form method="GET" action="{{ route('export.index') }}" class="flex flex-wrap gap-3 items-end">

                {{-- Unit Kerja --}}
                @if($unitKerjaList->isNotEmpty())
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Unit Kerja</label>
                    <select name="unit_kerja_id" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Unit</option>
                        @foreach($unitKerjaList as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Keputusan --}}
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Keputusan</label>
                    <select name="keputusan_id" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Keputusan</option>
                        @foreach($keputusanList as $kep)
                        <option value="{{ $kep->id }}" {{ request('keputusan_id') == $kep->id ? 'selected' : '' }}>
                            {{ $kep->nomor_keputusan }} ({{ $kep->periode_year }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bulan --}}
                <div class="min-w-[130px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Bulan</label>
                    <select name="periode_bulan" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                        <option value="{{ $i + 1 }}" {{ request('periode_bulan') == $i + 1 ? 'selected' : '' }}>
                            {{ $bulan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="min-w-[110px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun</label>
                    <select name="periode_tahun" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('periode_tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Status --}}
                <div class="min-w-[160px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Status</option>
                        <option value="pending"     {{ request('status') == 'pending'     ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="in_approval" {{ request('status') == 'in_approval' ? 'selected' : '' }}>Sedang Diverifikasi</option>
                        <option value="approved"    {{ request('status') == 'approved'    ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected"    {{ request('status') == 'rejected'    ? 'selected' : '' }}>Revisi / TD</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                        Filter
                    </button>
                    @if(request()->hasAny(['unit_kerja_id','keputusan_id','periode_bulan','periode_tahun','status']))
                    <a href="{{ route('export.index') }}"
                        class="px-4 py-2.5 bg-rose-50 text-rose-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-rose-100 transition">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-700 uppercase text-xs tracking-widest">
                    Hasil: {{ $total }} data ditemukan
                </h3>
                @if($isAdmin)
                <span class="text-[10px] font-bold text-slate-400 uppercase">Semua Unit Kerja</span>
                @else
                <span class="text-[10px] font-bold text-slate-400 uppercase">
                    {{ auth()->user()->unitKerja->name ?? '' }}
                </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            @if($isAdmin)
                            <th class="px-6 py-4">Unit Kerja</th>
                            @endif
                            <th class="px-6 py-4">Keputusan</th>
                            <th class="px-6 py-4">Arahan</th>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4">Tindak Lanjut</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            @if($isAdmin)
                            <th class="px-6 py-4 text-center">Stage</th>
                            @endif
                            <th class="px-6 py-4">Dibuat Oleh</th>
                            <th class="px-6 py-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($data as $i => $tl)
                        @php
                            $approvedStages = $tl->approvals->where('status', 'approved')->count();
                            $statusConfig = match($tl->status) {
                                'approved'    => ['bg-emerald-50 text-emerald-700 border-emerald-100', 'Selesai'],
                                'in_approval' => ['bg-blue-50 text-blue-700 border-blue-100', 'Diverifikasi'],
                                'rejected'    => ['bg-rose-50 text-rose-700 border-rose-100', 'Revisi/TD'],
                                default       => ['bg-amber-50 text-amber-700 border-amber-100', 'Menunggu'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-400 text-xs font-bold">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $i + 1 }}
                            </td>
                            @if($isAdmin)
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-600 bg-slate-100 px-2 py-1 rounded-lg uppercase">
                                    {{ $tl->unitKerja->name ?? '-' }}
                                </span>
                            </td>
                            @endif
                            <td class="px-6 py-4">
                                <p class="text-xs font-black text-slate-700">{{ $tl->arahan->keputusan->nomor_keputusan ?? '-' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $tl->arahan->keputusan->periode_year ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <p class="text-xs text-slate-600 font-medium truncate" title="{{ $tl->arahan->strategi ?? '-' }}">
                                    {{ Str::limit($tl->arahan->strategi ?? '-', 60) }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                {{ $tl->periode_bulan }}/{{ $tl->periode_tahun }}
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <p class="text-xs text-slate-700 font-medium truncate" title="{{ $tl->tindak_lanjut }}">
                                    {{ Str::limit($tl->tindak_lanjut, 60) }}
                                </p>
                                @if($tl->kendala)
                                <p class="text-[9px] text-rose-500 font-bold mt-0.5 truncate">
                                    Kendala: {{ Str::limit($tl->kendala, 40) }}
                                </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase border {{ $statusConfig[0] }}">
                                    {{ $statusConfig[1] }}
                                </span>
                            </td>
                            @if($isAdmin)
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[7px] font-black
                                        {{ $s <= $approvedStages ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $s }}
                                    </span>
                                    @endfor
                                </div>
                            </td>
                            @endif
                            <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                {{ $tl->creator->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase">
                                {{ $tl->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 10 : 8 }}" class="px-6 py-16 text-center">
                                <p class="text-slate-400 italic text-sm font-bold">Tidak ada data ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-50">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</x-app-layout>