<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6">
                
                {{-- Welcome Banner --}}
                <div class="relative overflow-hidden bg-slate-900 rounded-2xl shadow-xl p-8 text-white">
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                        <div class="text-center md:text-left">
                            <h2 class="text-3xl font-extrabold mb-2 tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h2>
                            <p class="text-slate-400 text-lg max-w-xl">Sistem Monitoring dan Tindak Lanjut Keputusan RUPS - Kelola data dengan presisi.</p>
                        </div>
                        <div class="mt-6 md:mt-0 bg-white/10 backdrop-blur-md px-6 py-3 rounded-xl border border-white/20">
                            <p class="text-sm font-semibold uppercase tracking-wider text-slate-300">Tanggal Hari Ini</p>
                            <p class="text-xl font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    {{-- Aksen Dekorasi --}}
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
                </div>

                {{-- Quick Stats Area --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-amber-50 rounded-xl">
                                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Keputusan</p>
                                <h3 class="text-3xl font-black text-slate-900">{{ array_sum($keputusanStats ?? []) }}</h3>
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-2">
                            <div class="bg-slate-50 p-2 rounded-lg text-center">
                                <span class="block text-xs font-bold text-amber-600">BD: {{ $keputusanStats['BD'] ?? 0 }}</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-lg text-center">
                                <span class="block text-xs font-bold text-blue-600">BS: {{ $keputusanStats['BS'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-indigo-50 rounded-xl">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Arahan</p>
                                <h3 class="text-3xl font-black text-slate-900">{{ $totalArahan ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="mt-6 flex space-x-2">
                            <div class="flex-1 bg-green-50 p-2 rounded-lg text-center border border-green-100">
                                <span class="text-xs font-bold text-green-700">Dikirim: {{ $totalArahanTerkirim ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-emerald-50 rounded-xl">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Tindak Lanjut</p>
                                <h3 class="text-3xl font-black text-slate-900">{{ array_sum($tindakLanjutStats ?? []) }}</h3>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex -space-x-1">
                                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-400 uppercase">Status Approval Berjalan</span>
                        </div>
                    </div>
                </div>

                {{-- Charts Area --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-xl font-bold text-slate-800">Distribusi Status</h3>
                            <div class="flex bg-slate-100 p-1 rounded-lg">
                                <button onclick="toggleChartType('keputusan', 'doughnut')" class="px-3 py-1 text-xs font-bold rounded-md hover:bg-white transition">Doughnut</button>
                                <button onclick="toggleChartType('keputusan', 'bar')" class="px-3 py-1 text-xs font-bold rounded-md hover:bg-white transition ml-1">Bar</button>
                            </div>
                        </div>
                        <div class="h-72">
                            <canvas id="keputusanChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-xl font-bold text-slate-800">Progress Approval</h3>
                            <select id="approvalFilter" class="text-xs font-bold border-slate-200 rounded-lg focus:ring-slate-900 focus:border-slate-900">
                                <option value="all">Semua Unit</option>
                                @foreach($unitKerjaList ?? [] as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="h-72">
                            <canvas id="approvalChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Table Section --}}
                @if(auth()->user()->can('approve_stage_1') || auth()->user()->can('approve_stage_2') || auth()->user()->can('approve_stage_3'))
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">Menunggu Persetujuan</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-slate-400 text-xs font-black uppercase tracking-widest bg-white">
                                    <th class="px-6 py-4">Unit Kerja</th>
                                    <th class="px-6 py-4">Tindak Lanjut</th>
                                    <th class="px-6 py-4 text-center">Stage</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($pendingApprovals ?? [] as $approval)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-700">{{ $approval->tindakLanjut->unitKerja->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-500 text-sm">{{ Str::limit($approval->tindakLanjut->tindak_lanjut, 60) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            Stage {{ $approval->stage }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('approval.index') }}" class="inline-flex items-center text-sm font-black text-slate-900 hover:text-blue-600 group">
                                            Proses <svg class="ml-1 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <p class="text-slate-400 font-medium italic text-sm">Semua tugas selesai. Tidak ada antrian persetujuan.</p>
                                    </td>
                                </tr>
                                @endforelse
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

    // Data dari Laravel
    const keputusanData = {
        labels: @json(array_keys($keputusanStats ?? [])),
        values: @json(array_values($keputusanStats ?? [])),
    };

    const tindakLanjutData = {
        labels: @json(array_keys($tindakLanjutStats ?? [])),
        values: @json(array_values($tindakLanjutStats ?? [])),
    };

    const unitKerjaData = {
        labels: @json($unitKerjaStats->pluck('name')),
        values: @json($unitKerjaStats->pluck('total')),
    };

    const colors = [
        '#6366f1','#f59e0b','#10b981','#ef4444','#3b82f6','#8b5cf6','#ec4899'
    ];

    // Chart Keputusan (Doughnut default)
    function buildKeputusanChart(type = 'doughnut') {
        if (charts['keputusan']) charts['keputusan'].destroy();

        charts['keputusan'] = new Chart(
            document.getElementById('keputusanChart'),
            {
                type: type,
                data: {
                    labels: keputusanData.labels,
                    datasets: [{
                        data: keputusanData.values,
                        backgroundColor: colors.slice(0, keputusanData.labels.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
                    }
                }
            }
        );
    }

    // Chart Approval (Bar per Unit Kerja)
    function buildApprovalChart(filterId = 'all') {
        if (charts['approval']) charts['approval'].destroy();

        let labels = unitKerjaData.labels;
        let values = unitKerjaData.values;

        if (filterId !== 'all') {
            const idx = labels.findIndex((_, i) => i == filterId);
            if (idx !== -1) {
                labels = [labels[idx]];
                values = [values[idx]];
            }
        }

        charts['approval'] = new Chart(
            document.getElementById('approvalChart'),
            {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tindak Lanjut',
                        data: values,
                        backgroundColor: colors,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            }
        );
    }

    function toggleChartType(name, type) {
        if (name === 'keputusan') buildKeputusanChart(type);
    }

    // Init
    buildKeputusanChart('doughnut');
    buildApprovalChart('all');

    // Filter Unit Kerja
    document.getElementById('approvalFilter')?.addEventListener('change', function () {
        buildApprovalChart(this.value);
    });
</script>
@endpush
</x-app-layout>