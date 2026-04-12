<x-app-layout>
    {{-- Header Bar: Action Buttons --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tambah Pengguna</h2>
        </div>
    </div>

    <div class="max-w-5xl">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Decorative Header --}}
            <div class="p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Registrasi Akun Baru</h3>
                        <p class="text-slate-400 text-sm mt-1 tracking-wide">Silakan lengkapi informasi identitas dan hak akses user.</p>
                    </div>
                    <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-md border border-white/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
            </div>
            @if ($errors->any())
            <div class="p-4 mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-2xl font-bold uppercase text-xs">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="p-8 space-y-8">


                    {{-- Section 1: Identitas --}}
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-2"></span>
                            Informasi Identitas
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Badge / NIP <span class="text-rose-500">*</span></label>
                                <input type="text" name="badge" value="{{ old('badge') }}"
                                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('badge') border-rose-500 @enderror"
                                    placeholder="Contoh: 123456" required>
                                @error('badge') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('name') border-rose-500 @enderror"
                                    placeholder="Nama lengkap user" required>
                                @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('email') border-rose-500 @enderror"
                                    placeholder="email@pusri.co.id" required>
                                @error('email') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Keamanan (Password Wajib untuk Store/Create) --}}
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Keamanan Akun</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password"
                                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 placeholder-slate-300 @error('password') border-rose-500 @enderror"
                                    placeholder="••••••••" required>
                                @error('password') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Konfirmasi Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 placeholder-slate-300"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Penempatan --}}
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                            <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full mr-2"></span>
                            Penempatan & Akses
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Unit Kerja</label>
                                <select name="unit_kerja_id" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($unitKerja as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Role / Hak Akses <span class="text-rose-500">*</span></label>
                                <select name="role" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Atasan Langsung / PIC</label>
                                <select name="pic_unit_kerja_id" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                    <option value="">-- Pilih Atasan --</option>
                                    @foreach($picUsers->groupBy(fn($item) => $item->unitKerja->name ?? 'Admin / Pusat') as $unitName => $users)
                                    <optgroup label="UNIT: {{ strtoupper($unitName) }}">
                                        @foreach($users as $pic)
                                        <option value="{{ $pic->id }}" {{ old('pic_unit_kerja_id') == $pic->id ? 'selected' : '' }}>
                                            {{ $pic->name }} ({{ $pic->badge }})
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Status --}}
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Status Akun</h4>
                            <p class="text-xs text-slate-500">Tentukan apakah akun langsung aktif setelah dibuat.</p>
                        </div>
                        <div class="flex bg-white p-1 rounded-xl border border-slate-200">
                            <label class="flex items-center cursor-pointer px-6 py-2 rounded-lg transition {{ old('status', 'active') == 'active' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-400' }}">
                                <input type="radio" name="status" value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }} class="hidden">
                                <span class="text-xs font-black uppercase">Aktif</span>
                            </label>
                            <label class="flex items-center cursor-pointer px-6 py-2 rounded-lg transition {{ old('status') == 'inactive' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-400' }}">
                                <input type="radio" name="status" value="inactive" {{ old('status') == 'inactive' ? 'checked' : '' }} class="hidden">
                                <span class="text-xs font-black uppercase">Nonaktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer: Buttons --}}
                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-sm font-bold hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-slate-800 shadow-lg shadow-slate-200 transition active:scale-95">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>