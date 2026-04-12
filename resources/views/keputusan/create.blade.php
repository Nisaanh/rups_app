<x-app-layout>
    <div class="mb-6">
        <a href="{{ route('keputusan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Header Gelap --}}
            <div class="p-10 bg-slate-900 text-white relative overflow-hidden text-center">
                <div class="relative z-10">
                    <h3 class="text-2xl font-black uppercase tracking-tight">Buat Keputusan RUPS</h3>
                    <p class="text-slate-400 text-sm mt-1">Inisiasi periode baru untuk monitoring arahan</p>
                </div>
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-blue-600/20 rounded-full blur-3xl"></div>
            </div>

            <form action="{{ route('keputusan.store') }}" method="POST">
                @csrf
                <div class="p-10 space-y-8">
                    {{-- Nomor Keputusan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Registrasi Keputusan</label>
                        <input type="text" name="nomor_keputusan" value="{{ old('nomor_keputusan', $autoNumber) }}" 
                               class="w-full px-5 py-4 rounded-2xl border-slate-200 bg-slate-50 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-800 transition" required>
                        <p class="mt-2 text-[10px] text-slate-400 italic">Nomor ini digenerate otomatis oleh sistem, Anda dapat menyesuaikannya jika perlu.</p>
                    </div>

                    {{-- Periode Tahun --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Periode Tahun RUPS <span class="text-rose-500">*</span></label>
                        <input type="number" name="periode_year" value="{{ old('periode_year', date('Y')) }}"
                               class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-800 transition @error('periode_year') border-rose-500 @enderror"
                               min="2000" max="{{ date('Y') + 5 }}" required>
                        @error('periode_year') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-center">
                    <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 shadow-xl shadow-slate-200 transition active:scale-95">
                        Buat & Lanjut Input Arahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>