<x-app-layout>
    <div class="py-2 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Welcome Banner --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-xl p-8 text-white">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl font-extrabold mb-2 tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-400 text-lg max-w-xl">Sistem Monitoring dan Tindak Lanjut Keputusan RUPS - Kelola data dengan presisi.</p>
                    </div>
                    <div class="mt-6 md:mt-0 bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-300 mb-1">Tanggal Hari Ini</p>
                        <p class="text-xl font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
                {{-- Aksen Dekorasi --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-64 h-64 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Quick Stats Area --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Card 1 --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Keputusan</p>
                            <h3 class="text-3xl font-black text-slate-900">{{ array_sum($keputusanStats ?? []) }}</h3>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 p-2 rounded-xl text-center border border-slate-100">
                            <span class="block text-xs font-bold text-amber-600 uppercase">BD: {{ $keputusanStats['BD'] ?? 0 }}</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl text-center border border-slate-100">
                            <span class="block text-xs font-bold text-blue-600 uppercase">BS: {{ $keputusanStats['BS'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Arahan</p>
                            <h3 class="text-3xl font-black text-slate-900">{{ $totalArahan ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="mt-6">
                        <div class="bg-green-50 p-2 rounded-xl text-center border border-green-100">
                            <span class="text-xs font-bold text-green-700 uppercase tracking-tighter">Dikirim: {{ $totalArahanTerkirim ?? 0 }} Arahan</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300 text-center md:text-left">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tindak Lanjut</p>
                            <h3 class="text-3xl font-black text-slate-900">{{ array_sum($tindakLanjutStats ?? []) }}</h3>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between items-center bg-slate-50 p-2 rounded-xl">
                        <div class="flex -space-x-2 ml-2">
                            <div class="w-3 h-3 rounded-full bg-amber-400 border-2 border-white"></div>
                            <div class="w-3 h-3 rounded-full bg-blue-400 border-2 border-white"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400 border-2 border-white"></div>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase mr-2 tracking-tighter text-right">Approval Berjalan</span>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-8">Status Keputusan</h3>
                    <div class="h-72 relative">
                        <canvas id="keputusanChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-slate-800">Progress Approval</h3>
                        @if($is_global)
                        <select id="approvalFilter" class="text-xs font-bold border-slate-200 rounded-xl bg-slate-50">
                            <option value="all">Semua Unit Kerja</option>
                            @foreach($unitKerjaList as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="h-72 relative">
                        <canvas id="approvalChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Pending Approval Table --}}
            @if($pendingApprovals->count() > 0)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 bg-slate-50 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 italic">Antrian Persetujuan (Global)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px]">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest">
                                <th class="px-8 py-5">Unit Kerja</th>
                                <th class="px-8 py-5">Deskripsi</th>
                                <th class="px-8 py-5 text-center">Stage</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingApprovals as $approval)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5 font-bold text-slate-700">{{ $approval->tindakLanjut->unitKerja->name ?? '-' }}</td>
                                <td class="px-8 py-5 text-slate-500 text-sm italic">{{ Str::limit($approval->tindakLanjut->tindak_lanjut, 60) }}</td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-4 py-1 bg-sky-50 text-sky-700 rounded-full text-[10px] font-black uppercase">Stage {{ $approval->stage }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('approval.index') }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-sky-600 transition-all shadow-sm active:scale-95">Proses</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const charts = {};
        const colors = ['#0ea5e9','#6366f1','#f59e0b','#10b981','#ef4444'];

        // Chart 1
        new Chart(document.getElementById('keputusanChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($keputusanStats)),
                datasets: [{ data: @json(array_values($keputusanStats)), backgroundColor: colors }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Chart 2
        const unitKerjaFull = @json($unitKerjaStats);
        function buildApprovalChart(filterId = 'all') {
            const ctx = document.getElementById('approvalChart');
            if (charts['approval']) charts['approval'].destroy();

            let filtered = unitKerjaFull;
            if (filterId !== 'all') filtered = unitKerjaFull.filter(item => item.id == filterId);

            charts['approval'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: filtered.map(d => d.name),
                    datasets: [{ label: 'Total', data: filtered.map(d => d.total), backgroundColor: '#6366f1', borderRadius: 8 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            buildApprovalChart('all');
            document.getElementById('approvalFilter')?.addEventListener('change', (e) => buildApprovalChart(e.target.value));
        });
    </script>
    @endpush
</x-app-layout>