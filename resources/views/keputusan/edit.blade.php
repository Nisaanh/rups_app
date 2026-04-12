<x-app-layout>
    <div class="space-y-6">
        {{-- Top Bar / Breadcrumb --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('keputusan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Batal & Kembali
                </a>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight underline decoration-blue-500 decoration-4 underline-offset-8">Edit Keputusan</h2>
            </div>
        </div>

        {{-- Form Container --}}
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                {{-- Header Card --}}
                <div class="p-10 bg-slate-900 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 mb-2">Pembaruan Data</p>
                        <h3 class="text-2xl font-black tracking-tight uppercase">{{ $keputusan->nomor_keputusan ?? 'Draft Keputusan' }}</h3>
                        <p class="text-slate-400 text-xs mt-2 font-medium">Terdaftar pada: {{ $keputusan->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    {{-- Decorative Element --}}
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
                </div>

                <form action="{{ route('keputusan.update', $keputusan->id) }}" method="POST" class="p-10 space-y-8">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                    <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-2xl mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-black text-rose-700 uppercase tracking-widest">Ada kesalahan input:</p>
                                <ul class="text-[11px] text-rose-600 font-bold list-disc list-inside mt-1 uppercase">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Periode Tahun --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Periode Tahun <span class="text-rose-500">*</span></label>
                            <input type="number" name="periode_year" value="{{ old('periode_year', $keputusan->periode_year) }}" 
                                class="w-full px-6 py-4 bg-slate-50 border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 transition appearance-none" 
                                placeholder="Contoh: 2026" required>
                        </div>

                        {{-- Status (Readonly/Info) --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Status Saat Ini</label>
                            <div class="w-full px-6 py-4 bg-slate-100 border-slate-200 rounded-2xl font-black text-slate-400 text-xs uppercase tracking-widest flex items-center">
                                <span class="w-2 h-2 bg-slate-400 rounded-full mr-3"></span>
                                {{ $keputusan->status }}
                            </div>
                        </div>
                    </div>

                    {{-- Nomor Keputusan --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nomor Keputusan RUPS <span class="text-rose-500">*</span></label>
                        <input type="text" name="nomor_keputusan" value="{{ old('nomor_keputusan', $keputusan->nomor_keputusan) }}" 
                            class="w-full px-6 py-4 bg-slate-50 border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 transition" 
                            placeholder="Contoh: KEP-01/RUPS/2026" required>
                    </div>

                    {{-- Note Info --}}
                    <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[11px] font-bold text-blue-700 leading-relaxed uppercase tracking-tighter">
                                Perubahan data keputusan akan mempengaruhi seluruh butir arahan yang terhubung. Pastikan nomor keputusan sudah sesuai dengan dokumen fisik.
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-6 flex justify-end space-x-3">
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 shadow-xl shadow-slate-200 transition active:scale-95 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>