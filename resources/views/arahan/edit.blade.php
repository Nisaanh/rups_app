<x-app-layout>
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('keputusan.show', $arahan->keputusan_id) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Detail
                </a>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight italic underline decoration-emerald-500 decoration-4 underline-offset-8">
                    Edit Arahan
                </h2>
            </div>
        </div>

        {{-- Konteks Keputusan --}}
        <div class="bg-slate-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400">Edit Butir Arahan</p>
                    <h3 class="text-2xl font-black mt-1 uppercase tracking-tight">
                        {{ $keputusanSelected->nomor_keputusan }}
                    </h3>
                    <div class="flex items-center mt-2 space-x-4 text-slate-400 font-bold text-xs uppercase tracking-tighter">
                        <span>ID: #{{ $keputusanSelected->id }}</span>
                        <span class="w-1 h-1 bg-slate-700 rounded-full"></span>
                        <span>Tahun: {{ $keputusanSelected->periode_year }}</span>
                    </div>
                </div>
                <div class="px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                    Status: {{ $keputusanSelected->status }}
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-600/20 rounded-full blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Edit (Kiri) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 bg-slate-50 border-b border-slate-100">
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Ubah Data Arahan
                        </h3>
                    </div>

                    <form action="{{ route('arahan.update', $arahan) }}" method="POST" class="p-10 space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Unit Kerja --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    Unit Kerja Pelaksana <span class="text-rose-500">*</span>
                                </label>
                                <select name="unit_kerja_id" id="unitKerjaSelect"
                                    class="w-full rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold text-slate-700 transition" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($unitKerja as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ $arahan->unit_kerja_id == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->level }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- PIC --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    PIC Penanggung Jawab
                                </label>
                                <select name="pic_unit_kerja_id" id="picSelect"
                                    class="w-full rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold text-slate-700 transition" required>
                                    {{-- Diisi oleh JS --}}
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Terbit --}}
                        <div class="max-w-xs">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                Tanggal Terbit Arahan
                            </label>
                            <input type="date" name="tanggal_terbit"
                                value="{{ old('tanggal_terbit', $arahan->tanggal_terbit->format('Y-m-d')) }}"
                                class="w-full rounded-2xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-bold text-slate-700 transition" required>
                        </div>

                        {{-- Strategi --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                Strategi Pelaksanaan (Butir Arahan)
                            </label>
                            <textarea name="strategi" rows="5"
                                class="w-full rounded-[2rem] border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-medium text-slate-700 transition p-6"
                                required>{{ old('strategi', $arahan->strategi) }}</textarea>
                        </div>

                        {{-- After Save Option --}}
                        <div class="bg-emerald-50 p-6 rounded-[2rem] border border-emerald-100 flex items-center justify-between">
                            <span class="text-[10px] font-black text-emerald-800 uppercase tracking-widest">Setelah Simpan:</span>
                            <div class="flex gap-6">
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="after_save" value="finish" checked
                                        class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                    <span class="ml-2 text-xs font-black text-emerald-700 uppercase tracking-tighter">Kembali ke Detail</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="after_save" value="continue"
                                        class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                    <span class="ml-2 text-xs font-bold text-slate-400 uppercase tracking-tighter">Tambah Arahan Lagi</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-between items-center">
                            <a href="{{ route('keputusan.show', $arahan->keputusan_id) }}"
                                class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-10 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-700 shadow-xl shadow-emerald-100 transition active:scale-95 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar: Arahan lain dalam keputusan ini --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-6 bg-slate-800 text-white">
                        <h3 class="text-[10px] font-black uppercase tracking-widest">
                            Arahan Lain ({{ $existingArahan->count() }})
                        </h3>
                    </div>
                    <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-50">
                        @forelse($existingArahan as $ea)
                        <div class="p-6 transition {{ $ea->id === $arahan->id ? 'bg-emerald-50 border-l-4 border-emerald-500' : 'hover:bg-slate-50' }}">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[8px] font-black uppercase tracking-tighter">
                                    {{ $ea->unitKerja->name }}
                                </span>
                                @if($ea->id === $arahan->id)
                                <span class="text-[8px] font-black text-emerald-600 uppercase">Sedang diedit</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-600 italic leading-relaxed">
                                "{{ Str::limit($ea->strategi, 80) }}"
                            </p>
                            <div class="mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                PIC: {{ $ea->pic->name ?? '-' }}
                            </div>
                        </div>
                        @empty
                        <div class="p-10 text-center">
                            <p class="text-xs text-slate-400 italic">Tidak ada arahan lain.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const picData = @json($picByUnit);
        const currentPicId = {
            {
                $arahan - > pic_unit_kerja_id ?? 'null'
            }
        };

        function updatePIC(selectedPicId = null) {
            const unitId = document.getElementById('unitKerjaSelect').value;
            const picSelect = document.getElementById('picSelect');

            picSelect.innerHTML = '<option value="">-- Pilih PIC --</option>';

            if (unitId && picData[unitId]) {
                const pic = picData[unitId];
                const opt = document.createElement('option');
                opt.value = pic.id;
                opt.textContent = (pic.badge ? pic.badge + ' - ' : '') + pic.name;
                opt.selected = true;
                picSelect.appendChild(opt);
            } else if (unitId) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '⚠️ Unit belum punya PIC';
                picSelect.appendChild(opt);
            }
        }

        document.getElementById('unitKerjaSelect').addEventListener('change', () => updatePIC());

        // Inisialisasi PIC saat page load
        updatePIC(currentPicId);
    </script>
    @endpush
</x-app-layout>