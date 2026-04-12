<x-app-layout>
    {{-- Header Bar: Action Buttons --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('unit-kerja.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Unit Kerja</h2>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Decorative Header --}}
            <div class="p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Sinkronisasi Struktur Organisasi</h3>
                        <p class="text-slate-400 text-sm mt-1 tracking-wide italic">Mengubah unit: {{ $unitKerja->name }}</p>
                    </div>
                    <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-md border border-white/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-600/20 rounded-full blur-3xl"></div>
            </div>

            <form action="{{ route('unit-kerja.update', $unitKerja) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-10 space-y-8">
                    {{-- Nama Unit --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Unit Kerja <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $unitKerja->name) }}"
                               class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('name') border-rose-500 @enderror"
                               placeholder="Contoh: Seksi Infrastruktur IT">
                        @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Level --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Level <span class="text-rose-500">*</span></label>
                            <select name="level" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('level') border-rose-500 @enderror">
                                <option value="">Pilih Level</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $unitKerja->level) == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                @endforeach
                            </select>
                            @error('level') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        {{-- Atasan --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Atasan (Parent Unit)</label>
                            <select name="report_to" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                <option value="">-- Pilih Atasan --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('report_to', $unitKerja->report_to) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ $parent->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Footer: Buttons --}}
                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('unit-kerja.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-sm font-bold hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition active:scale-95">
                        Update Unit Kerja
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>