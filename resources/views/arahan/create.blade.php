<x-app-layout>
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('keputusan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Daftar Keputusan
                </a>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight underline decoration-blue-500 decoration-4 underline-offset-8">Buat Arahan RUPS</h2>
            </div>
        </div>

        {{-- Konteks Keputusan Selected (Header Card) --}}
        @if(isset($keputusanSelected) && $keputusanSelected)
        <div class="bg-slate-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400">Konteks Keputusan Aktif</p>
                    <h3 class="text-2xl font-black mt-1 uppercase tracking-tight">{{ $keputusanSelected->nomor_keputusan ?? '-' }}</h3>
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
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Input (Kiri) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            {{ $existingArahan->count() > 0 ? 'Tambah Arahan Berikutnya' : 'Input Arahan Pertama' }}
                        </h3>
                    </div>

                    <form action="{{ route('arahan.store') }}" method="POST" class="p-10 space-y-8">
                        @csrf
                        <input type="hidden" name="keputusan_id" value="{{ $keputusanSelected->id ?? '' }}">
                        <!-- <input type="hidden" name="create_another" value="yes"> -->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Unit Kerja --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Unit Kerja Pelaksana <span class="text-rose-500">*</span></label>
                                <select name="unit_kerja_id" id="unitKerjaSelect" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 transition appearance-none" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($unitKerja as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->level }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- PIC --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">PIC Penanggung Jawab</label>
                                <select name="pic_unit_kerja_id" id="picSelect" class="w-full rounded-2xl border-slate-200 bg-slate-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 transition" required>
                                    <option value="">Pilih Unit Kerja Dahulu</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Terbit --}}
                        <div class="max-w-xs">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tanggal Terbit Arahan</label>
                            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', date('Y-m-d')) }}" 
                                   class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 transition" required>
                        </div>

                        {{-- Strategi --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Strategi Pelaksanaan (Butir Arahan)</label>
                            <textarea name="strategi" rows="5" class="w-full rounded-[2rem] border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-medium text-slate-700 transition p-6" 
                                      placeholder="Tuliskan butir arahan secara detail dan terperinci..." required>{{ old('strategi') }}</textarea>
                        </div>

                        {{-- After Save Option --}}
                        <div class="bg-blue-50 p-6 rounded-[2rem] border border-blue-100 flex items-center justify-between">
                            <span class="text-[10px] font-black text-blue-800 uppercase tracking-widest">Alur Selanjutnya:</span>
                            <div class="flex gap-6">
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="after_save" value="continue" checked class="text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <span class="ml-2 text-xs font-black text-blue-700 uppercase tracking-tighter group-hover:text-blue-900 transition">Input Arahan Lagi</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="after_save" value="finish" class="text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <span class="ml-2 text-xs font-bold text-slate-400 uppercase tracking-tighter group-hover:text-slate-600 transition">Kembali ke Detail</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 shadow-xl shadow-slate-200 transition active:scale-95 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Simpan Butir Arahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar Draft List (Kanan) --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden sticky top-6">
                    <div class="p-6 bg-slate-800 text-white flex justify-between items-center">
                        <h3 class="text-[10px] font-black uppercase tracking-widest">Draft Tersimpan ({{ $existingArahan->count() }})</h3>
                    </div>
                    
                    <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-50 custom-scrollbar">
                        @forelse($existingArahan as $ea)
                        <div class="p-6 hover:bg-slate-50 transition group relative">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[8px] font-black uppercase tracking-tighter">{{ $ea->unitKerja->name }}</span>
                                <button type="button" onclick="deleteArahan({{ $ea->id }})" class="text-rose-400 hover:text-rose-600 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-600 italic leading-relaxed">"{{ Str::limit($ea->strategi, 100) }}"</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">PIC: {{ $ea->pic->name ?? '-' }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="p-10 text-center">
                            <p class="text-xs text-slate-400 italic">Belum ada butir arahan.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Finalization Box --}}
                    @if($existingArahan->count() > 0 && isset($keputusanSelected))
                    <div class="p-6 bg-slate-50 border-t border-slate-100">
                        <form action="{{ route('keputusan.finalize', $keputusanSelected->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 transition uppercase tracking-widest flex items-center justify-center group" onclick="return confirm('Kirim semua arahan ini sekarang?')">
                                🚀 Finalisasi & Kirim Massal
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Script mapping PIC --}}
    @push('scripts')
    <script>
        const picData = @json($picByUnit);
        
        function updatePIC() {
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
                picSelect.classList.remove('bg-slate-50');
            } else if(unitId) {
                const opt = document.createElement('option');
                opt.textContent = '⚠️ Unit belum punya PIC';
                picSelect.appendChild(opt);
                picSelect.classList.add('bg-slate-50');
            }
        }

        document.getElementById('unitKerjaSelect').addEventListener('change', updatePIC);

        function deleteArahan(id) {
            if (confirm('Hapus arahan ini dari draft?')) {
                const form = document.getElementById('deleteArahanForm');
                form.action = `/arahan/${id}`;
                form.submit();
            }
        }
    </script>
    <form id="deleteArahanForm" method="POST" class="hidden">@csrf @method('DELETE')</form>
    @endpush
</x-app-layout>