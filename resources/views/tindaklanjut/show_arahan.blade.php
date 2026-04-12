<x-app-layout>
    <div class="space-y-6">
        {{-- Top Bar --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('tindaklanjut.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        {{-- Header Card --}}
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-2">
                        @php
                            $latestTl    = $arahan->tindakLanjut->sortByDesc('created_at')->first();
                            $statusTL    = $latestTl->status ?? 'pending';
                            $isGlobalTD  = ($arahan->keputusan->status ?? '') === 'TD';
                        @endphp

                        <div class="flex items-center gap-2">
                            @if($isGlobalTD)
                                <span class="px-2.5 py-1 bg-slate-700 text-slate-100 rounded text-[9px] font-black uppercase border border-slate-500">
                                    Status: Tidak Ditindaklanjuti
                                </span>
                            @elseif($statusTL === 'approved')
                                <span class="px-2.5 py-1 bg-emerald-500 text-white rounded text-[9px] font-black uppercase shadow-lg shadow-emerald-500/20">
                                    Status: Selesai
                                </span>
                            @elseif($statusTL === 'rejected')
                                <span class="px-2.5 py-1 bg-rose-500 text-white rounded text-[9px] font-black uppercase shadow-lg shadow-rose-500/20">
                                    Status: Perlu Revisi
                                </span>
                            @elseif($statusTL === 'in_approval')
                                <span class="px-2.5 py-1 bg-blue-500 text-white rounded text-[9px] font-black uppercase shadow-lg shadow-blue-500/20">
                                    Status: Sedang Diverifikasi
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-500 text-white rounded text-[9px] font-black uppercase shadow-lg shadow-amber-500/20">
                                    Status: Menunggu Verifikasi
                                </span>
                            @endif
                        </div>

                        <h1 class="text-xl font-black leading-tight tracking-tight italic">"{{ $arahan->strategi }}"</h1>
                    </div>

                    @can('create_tindak_lanjut')
                        @if(!$isGlobalTD && $statusTL !== 'approved')
                        <a href="{{ route('tindaklanjut.create', ['arahan_id' => $arahan->id]) }}"
                            class="px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 transition shadow-lg shadow-blue-500/20">
                            + Input Progres
                        </a>
                        @endif
                    @endcan
                </div>
            </div>

            {{-- Stat Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-white border-t border-slate-50">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Unit Pelaksana</p>
                    <p class="text-xs font-bold text-slate-700">{{ $arahan->unitKerja->name }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Target</p>
                    <p class="text-xs font-bold text-emerald-600">100% Tuntas</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Realisasi</p>
                    <p class="text-sm font-black text-blue-600">{{ $currentProgress }}%</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Status Akhir</p>
                    <p class="text-xs font-bold {{ $isGlobalTD ? 'text-slate-500' : 'text-slate-700' }}">
                        {{ $isGlobalTD ? 'Tidak Ditindaklanjuti' : 'Dalam Pemantauan' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- TIMELINE --}}
        <div class="space-y-4">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2">
                Histori Laporan & Verifikasi
            </h3>

            <div class="relative pl-6 border-l-2 border-slate-100 ml-3 space-y-6">
                @forelse($arahan->tindakLanjut->sortByDesc('created_at') as $tl)
                @php
                    // Ambil catatan revisi/TD — hanya dari approval yang rejected
                    $appWithNote = $tl->approvals()
                        ->where('status', 'rejected')
                        ->whereNotNull('note')
                        ->latest()
                        ->first();

                    $isTD = $tl->status === 'rejected'
                        && ($tl->arahan->keputusan->status ?? '') === 'TD';

                    $theme = match(true) {
                        $tl->status === 'approved'    => ['color' => 'emerald', 'label' => 'Selesai / Approved'],
                        $isTD                         => ['color' => 'slate',   'label' => 'Tidak Ditindaklanjuti'],
                        $tl->status === 'rejected'    => ['color' => 'rose',    'label' => 'Perlu Revisi'],
                        $tl->status === 'in_approval' => ['color' => 'blue',    'label' => 'Sedang Diverifikasi'],
                        default                       => ['color' => 'amber',   'label' => 'Menunggu Verifikasi'],
                    };

                    $dotClass = [
                        'emerald' => 'border-emerald-500',
                        'rose'    => 'border-rose-500',
                        'amber'   => 'border-amber-500',
                        'slate'   => 'border-slate-500',
                        'blue'    => 'border-blue-500',
                    ][$theme['color']] ?? 'border-slate-500';

                    $badgeClass = match(true) {
                        $tl->status === 'approved'    => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                        $isTD                         => 'bg-slate-900 text-white border-slate-900',
                        $tl->status === 'rejected'    => 'bg-rose-50 text-rose-600 border-rose-100',
                        $tl->status === 'in_approval' => 'bg-blue-50 text-blue-600 border-blue-100',
                        default                       => 'bg-amber-50 text-amber-600 border-amber-100',
                    };
                @endphp

                <div class="relative">
                    {{-- Dot Pin --}}
                    <div class="absolute -left-[31px] top-1 w-4 h-4 rounded-full bg-white border-4 {{ $dotClass }} z-10"></div>

                    <div class="bg-white rounded-[1.2rem] p-5 shadow-sm border border-slate-100">
                        {{-- Header Row --}}
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-[9px] font-bold text-slate-400 uppercase">
                                {{ $tl->created_at->format('d M Y • H:i') }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border {{ $badgeClass }}">
                                {{ $theme['label'] }}
                            </span>
                        </div>

                        {{-- Isi Laporan --}}
                        <div class="p-4 bg-slate-50 rounded-xl italic text-xs text-slate-600 leading-relaxed mb-4">
                            "{{ $tl->tindak_lanjut }}"
                        </div>

                        {{-- Stage Progress (hanya untuk in_approval) --}}
                        @if($tl->status === 'in_approval')
                        @php
                            $approvedStages = $tl->approvals()->where('status', 'approved')->count();
                        @endphp
                        <div class="mb-4 flex items-center gap-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Progress Stage:</span>
                            <div class="flex gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[8px] font-black
                                        {{ $s <= $approvedStages ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $s }}
                                    </span>
                                @endfor
                            </div>
                            <span class="text-[9px] font-bold text-slate-400">{{ $approvedStages }}/5</span>
                        </div>
                        @endif

                        {{-- Box Catatan Revisi / TD — hanya muncul saat rejected --}}
                        @if($appWithNote && ($tl->status === 'rejected' || $isTD))
                        <div class="p-4 rounded-xl border-l-4 shadow-sm mb-4
                            {{ $isTD ? 'bg-slate-100 border-slate-800' : 'bg-rose-50 border-rose-500' }}">
                            <p class="text-[8px] font-black {{ $isTD ? 'text-slate-500' : 'text-rose-700' }} uppercase tracking-widest mb-1">
                                {{ $isTD ? 'Keputusan Akhir (TD):' : 'Catatan Revisi:' }}
                            </p>
                            <p class="text-[11px] font-bold text-slate-800 italic">"{{ $appWithNote->note }}"</p>
                            <p class="text-[8px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">
                                Oleh: {{ $appWithNote->approver->name ?? '-' }}
                            </p>
                        </div>
                        @endif

                        {{-- Footer --}}
                        <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                            <span class="text-[9px] font-bold text-slate-400 uppercase italic">
                                Input: {{ $tl->creator->name }}
                            </span>
                            @if($tl->evidence_url)
                                <a href="{{ Storage::url($tl->evidence_url) }}" target="_blank"
                                    class="text-[9px] font-black text-blue-600 uppercase flex items-center gap-1 hover:underline">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Evidence
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <p class="text-slate-400 italic text-sm font-bold">Belum ada laporan yang masuk untuk arahan ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>