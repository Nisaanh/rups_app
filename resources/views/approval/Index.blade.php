<x-app-layout>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menunggu Anda</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Disetujui</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $approvedCount ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Direvisi</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $rejectedCount ?? 0 }}</p>
            </div>
        </div>

        {{-- Pending Approvals Area --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight italic">Antrean Persetujuan</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1 italic">Role: {{ $roleName }} — Stage {{ $currentStage }}</p>
                </div>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse($pendingApprovals as $approval)
                <div class="p-8 hover:bg-slate-50/50 transition group">
                    <div class="flex flex-col lg:flex-row justify-between gap-8">
                        <div class="flex-1 space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-blue-100">
                                    Stage {{ $approval->stage }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-300">#TL-{{ $approval->tindakLanjut->id }}</span>
                                @if($approval->stage == 5)
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[9px] font-black uppercase border border-emerald-100">
                                    Stage Terakhir
                                </span>
                                @endif
                            </div>

                            <h4 class="text-2xl font-black text-slate-800 group-hover:text-blue-600 transition tracking-tight">
                                {{ $approval->tindakLanjut->unitKerja->name ?? 'N/A' }}
                            </h4>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Periode</p>
                                    <p class="text-xs font-bold text-slate-700 italic">{{ $approval->tindakLanjut->periode_bulan }}/{{ $approval->tindakLanjut->periode_tahun }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Inisiator</p>
                                    <p class="text-xs font-bold text-slate-700">{{ $approval->tindakLanjut->creator->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Masuk Pada</p>
                                    <p class="text-xs font-bold text-slate-700">{{ $approval->tindakLanjut->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 italic relative">
                                <p class="text-sm text-slate-600 leading-relaxed font-medium">"{{ $approval->tindakLanjut->tindak_lanjut }}"</p>
                                @if($approval->tindakLanjut->kendala)
                                <div class="mt-3 pt-3 border-t border-slate-200/50">
                                    <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mb-1">Kendala Terlapor:</p>
                                    <p class="text-xs text-rose-600 font-bold leading-relaxed">{{ $approval->tindakLanjut->kendala }}</p>
                                </div>
                                @endif
                            </div>

                            {{-- Progress Stage --}}
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Progress:</span>
                                <div class="flex gap-1">
                                    @for($s = 1; $s <= 5; $s++)
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[8px] font-black
                                            {{ $s < $approval->stage ? 'bg-emerald-500 text-white' : ($s == $approval->stage ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400') }}">
                                            {{ $s }}
                                        </span>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-row lg:flex-col gap-2 shrink-0 lg:w-48 justify-end">
                            <button
                                data-id="{{ $approval->tindakLanjut->id }}"
                                data-stage="{{ $approval->stage }}"
                                onclick="openApproveModal(this.dataset.id, this.dataset.stage)"
                                class="flex-1 lg:flex-none px-6 py-3 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition shadow-lg shadow-slate-200">
                                {{ $approval->stage == 5 ? 'Selesaikan' : 'Setujui' }}
                            </button>
                            <button
                                data-id="{{ $approval->tindakLanjut->id }}"
                                onclick="openRejectModal(this.dataset.id)"
                                class="flex-1 lg:flex-none px-6 py-3 bg-white border border-slate-200 text-rose-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-50 transition">
                                Revisi
                            </button>
                            @if($approval->tindakLanjut->evidence_url)
                            <a href="{{ Storage::url($approval->tindakLanjut->evidence_url) }}" target="_blank"
                                class="flex-1 lg:flex-none px-6 py-3 bg-blue-50 text-blue-600 rounded-xl font-black text-[10px] uppercase tracking-widest text-center hover:bg-blue-100 transition">
                                Evidence
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center">
                    <p class="text-slate-400 italic text-sm font-bold">Semua antrean sudah diproses.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- History Table --}}
        @if($approvalHistory->total() > 0)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest italic">Riwayat Keputusan Anda</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Unit & Periode</th>
                            <th class="px-8 py-4">Keputusan</th>
                            <th class="px-8 py-4">Catatan</th>
                            <th class="px-8 py-4">Waktu</th>
                            <th class="px-8 py-4 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @foreach($approvalHistory as $history)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-4">
                                <p class="font-black text-slate-800 tracking-tight">{{ $history->tindakLanjut->unitKerja->name ?? '-' }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase italic">
                                    {{ $history->tindakLanjut->periode_bulan }}/{{ $history->tindakLanjut->periode_tahun }}
                                </p>
                            </td>
                            <td class="px-8 py-4">
                                @if($history->status === 'approved')
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase border border-emerald-100">DISETUJUI</span>
                                @else
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg text-[9px] font-black uppercase border border-rose-100">DIREVISI</span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-slate-500 italic font-medium">
                                {{ Str::limit($history->note ?? '-', 40) }}
                            </td>
                            <td class="px-8 py-4 text-slate-400 text-[10px] font-bold uppercase">
                                {{ $history->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('tindaklanjut.show_arahan', $history->tindakLanjut->arahan_id) }}"
                                    class="p-2 text-slate-400 hover:text-blue-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 border-t border-slate-50">
                {{ $approvalHistory->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- Approve Modal --}}
    <div id="approveModal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-[100] backdrop-blur-sm transition-all">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full mx-4 overflow-hidden border border-slate-100">
            <div class="p-10">
                <h3 id="approveModalTitle" class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight italic">Validasi Setuju</h3>
                <p id="approveModalDesc" class="text-sm text-slate-400 font-bold mb-8">Pastikan bukti dukung dan laporan sudah sesuai standar.</p>

                <form id="approveForm" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 gap-3">
                            {{-- Opsi utama: Lanjut atau Selesai (dinamis via JS) --}}
                            <label class="flex items-center p-4 border-2 border-slate-50 rounded-2xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 group">
                                <input type="radio" id="optionLanjut" name="result" value="lanjut" checked class="w-4 h-4 text-emerald-600 focus:ring-0">
                                <div class="ml-4">
                                    <span id="labelLanjut" class="block font-black text-xs uppercase tracking-widest text-slate-400 group-has-[:checked]:text-emerald-700">
                                        Lanjutkan ke Stage Berikutnya
                                    </span>
                                </div>
                            </label>
                            {{-- Opsi TD --}}
                            <label class="flex items-center p-4 border-2 border-slate-50 rounded-2xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-slate-800 has-[:checked]:bg-slate-900 group">
                                <input type="radio" id="optionTD" name="result" value="rejected" class="w-4 h-4 text-slate-800 focus:ring-0">
                                <div class="ml-4">
                                    <span class="block font-black text-xs uppercase tracking-widest text-slate-400 group-has-[:checked]:text-white">
                                        TD — Tidak Dapat Ditindaklanjuti
                                    </span>
                                </div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Catatan Verifikasi</label>
                            <textarea name="note" id="approveNote" rows="3"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-900 text-sm font-medium"
                                placeholder="Opsional..."></textarea>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="closeApproveModal()"
                                class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit" id="approveSubmitBtn"
                                class="flex-[2] px-6 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 shadow-xl shadow-slate-200 transition">
                                Konfirmasi Setuju
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-[100] backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full mx-4 overflow-hidden border border-slate-100">
            <div class="p-10">
                <h3 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight italic">Minta Revisi</h3>
                <p class="text-sm text-slate-400 font-bold mb-8">Berikan catatan agar unit dapat memperbaiki laporan.</p>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Alasan Revisi <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="note" id="rejectNote" rows="4" required
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500 text-sm font-medium"
                                placeholder="Sebutkan kekurangan..."></textarea>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="closeRejectModal()"
                                class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-[2] px-6 py-4 bg-rose-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 shadow-xl transition">
                                Kirim Revisi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openApproveModal(id, stage) {
            const isLastStage = parseInt(stage) === 5;

            // Reset form
            document.getElementById('approveNote').value = '';
            document.getElementById('approveForm').action = `/approval/${id}/approve`;

            // Ubah label dan value radio sesuai stage
            const optionLanjut  = document.getElementById('optionLanjut');
            const labelLanjut   = document.getElementById('labelLanjut');
            const submitBtn     = document.getElementById('approveSubmitBtn');
            const modalTitle    = document.getElementById('approveModalTitle');
            const modalDesc     = document.getElementById('approveModalDesc');

            if (isLastStage) {
                optionLanjut.value      = 'selesai';
                optionLanjut.checked    = true;
                labelLanjut.textContent = 'Selesai — Tindak Lanjut Dinyatakan Tuntas';
                submitBtn.textContent   = 'Konfirmasi Selesai';
                modalTitle.textContent  = 'Finalisasi Laporan';
                modalDesc.textContent   = 'Tindak lanjut akan dinyatakan selesai dan tidak dapat diubah lagi.';
            } else {
                optionLanjut.value      = 'lanjut';
                optionLanjut.checked    = true;
                labelLanjut.textContent = 'Lanjutkan ke Stage Berikutnya';
                submitBtn.textContent   = 'Konfirmasi Setuju';
                modalTitle.textContent  = 'Validasi Setuju';
                modalDesc.textContent   = 'Pastikan bukti dukung dan laporan sudah sesuai standar.';
            }

            document.getElementById('approveModal').classList.replace('hidden', 'flex');
        }

        function openRejectModal(id) {
            document.getElementById('rejectNote').value = '';
            document.getElementById('rejectForm').action = `/approval/${id}/reject`;
            document.getElementById('rejectModal').classList.replace('hidden', 'flex');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.replace('flex', 'hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.replace('flex', 'hidden');
        }

        ['approveModal', 'rejectModal'].forEach(id => {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) this.classList.replace('flex', 'hidden');
            });
        });
    </script>
    @endpush
</x-app-layout>