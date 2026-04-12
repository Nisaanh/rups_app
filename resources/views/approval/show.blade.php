<x-app-layout>
<div class="space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('approval.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Approval
        </a>
    </div>

    {{-- Tindak Lanjut Info --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Tindak Lanjut</h3>
                <span class="px-3 py-1 {{ $tindakLanjut->status == 'approved' ? 'bg-green-100 text-green-800' : ($tindakLanjut->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }} rounded-full text-sm">
                    {{ ucfirst($tindakLanjut->status) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Unit Kerja</p>
                    <p class="font-medium">{{ $tindakLanjut->unitKerja->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Periode</p>
                    <p class="font-medium">{{ $tindakLanjut->periode_bulan }}/{{ $tindakLanjut->periode_tahun }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Dibuat Oleh</p>
                    <p class="font-medium">{{ $tindakLanjut->creator->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                    <p class="font-medium">{{ $tindakLanjut->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500">Tindak Lanjut</p>
                <p class="text-gray-700 mt-1">{{ $tindakLanjut->tindak_lanjut }}</p>
            </div>

            @if($tindakLanjut->kendala)
            <div class="mb-4">
                <p class="text-sm text-gray-500">Kendala</p>
                <p class="text-gray-700 mt-1">{{ $tindakLanjut->kendala }}</p>
            </div>
            @endif

            @if($tindakLanjut->evidence_url)
            <div class="mb-4">
                <p class="text-sm text-gray-500">Bukti Pendukung</p>
                <a href="{{ Storage::url($tindakLanjut->evidence_url) }}" target="_blank"
                    class="inline-flex items-center mt-1 text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Lihat Bukti
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Approval Timeline --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Timeline Persetujuan</h3>
        </div>

        <div class="p-6">
            <div class="relative">
                @php
                $stages = [
                1 => ['name' => 'Atasan Auditi', 'role' => 'Atasan Auditi', 'icon' => '👤'],
                2 => ['name' => 'Tim Monitoring', 'role' => 'Tim Monitoring', 'icon' => '📊'],
                3 => ['name' => 'Pengendali Teknis', 'role' => 'Pengendali Teknis', 'icon' => '🔧'],
                4 => ['name' => 'Pengendali Mutu', 'role' => 'Pengendali Mutu', 'icon' => '✅'],
                5 => ['name' => 'Penanggung Jawab', 'role' => 'Penanggung Jawab', 'icon' => '👑']
                ];
                @endphp

                @foreach($stages as $stageNum => $stage)
                @php
                $approval = $tindakLanjut->approvals->where('stage', $stageNum)->first();
                $status = $approval ? $approval->status : 'pending';
                $statusColor = $status == 'approved' ? 'green' : ($status == 'rejected' ? 'red' : 'gray');
                $statusIcon = $status == 'approved' ? '✓' : ($status == 'rejected' ? '✗' : '○');
                @endphp

                <div class="relative flex items-start mb-8 last:mb-0">
                    {{-- Timeline Line --}}
                    @if(!$loop->last)
                    <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-gray-200"></div>
                    @endif

                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                            {{ $status == 'approved' ? 'bg-green-500' : ($status == 'rejected' ? 'bg-red-500' : 'bg-gray-300') }}">
                        <span class="text-white text-lg">{{ $stage['icon'] }}</span>
                    </div>

                    {{-- Content --}}
                    <div class="ml-4 flex-1">
                        <div class="flex flex-wrap justify-between items-start">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Stage {{ $stageNum }}: {{ $stage['name'] }}</h4>
                                <p class="text-sm text-gray-500">Role: {{ $stage['role'] }}</p>
                            </div>
                            <div class="mt-1">
                                @if($status == 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Disetujui
                                </span>
                                @elseif($status == 'rejected')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Ditolak
                                </span>
                                @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Menunggu
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($approval && $approval->approved_at)
                        <div class="mt-2 text-sm text-gray-600">
                            <p>Disetujui oleh: <span class="font-medium">{{ $approval->approver->name ?? '-' }}</span></p>
                            <p>Waktu: {{ $approval->approved_at->format('d/m/Y H:i:s') }}</p>
                            @if($approval->result)
                            <p>Hasil: <span class="font-medium">{{ $approval->result == 'selesai' ? 'Selesai' : 'TD (Tidak Dapat Ditindaklanjuti)' }}</span></p>
                            @endif
                        </div>
                        @endif

                        @if($approval && $approval->note)
                        <div class="mt-3 bg-gray-50 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-700">Catatan:</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $approval->note }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Action Buttons for Current Stage --}}
    @if($currentApproval && $currentApproval->status == 'pending')
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Tindakan Anda</h3>
        </div>
        <div class="p-6 flex gap-4">
            <button
                data-id="{{ $tindakLanjut->id }}"
                data-stage="{{ $currentStage }}"
                onclick="openApproveModal(this.dataset.id, this.dataset.stage)"
                class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Setujui
            </button>
            <button
                data-id="{{ $tindakLanjut->id }}"
                data-stage="{{ $currentStage }}"
                onclick="openRejectModal(this.dataset.id, this.dataset.stage)"
                class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Tolak
            </button>
        </div>
    </div>
    @endif
</div>

{{-- Approve Modal (sama seperti di index) --}}
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Setujui Tindak Lanjut</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="approveForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Persetujuan</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="result" value="selesai" checked
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Selesai - Tindak lanjut berhasil diselesaikan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="result" value="rejected"
                                class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                            <span class="ml-2 text-sm text-gray-700">TD (Tidak Dapat Ditindaklanjuti) - Proses dihentikan</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="note" rows="3"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Tindak Lanjut</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="note" rows="4" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Berikan alasan penolakan dengan jelas..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Alasan akan dikirimkan kepada pembuat tindak lanjut untuk revisi</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentTindakLanjutId = null;

    function openApproveModal(tindakLanjutId, stage) {
        currentTindakLanjutId = tindakLanjutId;
        const form = document.getElementById('approveForm');
        form.action = `/approval/${tindakLanjutId}/approve`;
        document.getElementById('approveModal').classList.remove('hidden');
        document.getElementById('approveModal').classList.add('flex');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        document.getElementById('approveModal').classList.remove('flex');
        document.getElementById('approveForm').reset();
    }

    function openRejectModal(tindakLanjutId, stage) {
        currentTindakLanjutId = tindakLanjutId;
        const form = document.getElementById('rejectForm');
        form.action = `/approval/${tindakLanjutId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
        document.getElementById('rejectForm').reset();
    }

    window.onclick = function(event) {
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        if (event.target === approveModal) closeApproveModal();
        if (event.target === rejectModal) closeRejectModal();
    }
</script>
@endpush
@endsection

<x-app-layout>