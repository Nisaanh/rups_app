<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('unit-kerja.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ isset($unitKerja) ? 'Edit' : 'Tambah' }} Unit Kerja</h2>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Form Konfigurasi Unit</h3>
                        <p class="text-slate-400 text-sm mt-1">Atur nama, level, dan hierarki organisasi.</p>
                    </div>
                    <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-md border border-white/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
            </div>

            <form action="{{ isset($unitKerja) ? route('unit-kerja.update', $unitKerja) : route('unit-kerja.store') }}" method="POST">
                @csrf
                @isset($unitKerja) @method('PUT') @endisset
                
                <div class="p-10 space-y-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Unit Kerja <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $unitKerja->name ?? '') }}" 
                               class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('name') border-rose-500 @enderror" placeholder="Contoh: Divisi Teknologi Informasi">
                        @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Level Unit <span class="text-rose-500">*</span></label>
                            <select name="level" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                <option value="">-- Pilih Level --</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $unitKerja->level ?? '') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                @endforeach
                            </select>
                            @error('level') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Unit Atasan (Parent)</label>
                            <select name="report_to" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                <option value="">-- Pilih Atasan --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('report_to', $unitKerja->report_to ?? '') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ $parent->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('unit-kerja.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-sm font-bold hover:bg-slate-100 transition">Batal</a>
                    <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-slate-800 shadow-lg shadow-slate-200 transition">
                        {{ isset($unitKerja) ? 'Update Unit' : 'Simpan Unit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>