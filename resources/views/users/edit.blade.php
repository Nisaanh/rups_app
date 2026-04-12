<x-app-layout>
    {{-- Header Bar: Action Buttons --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Pengguna</h2>
        </div>
    </div>

    <div class="max-w-5xl">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            {{-- Decorative Header --}}
            <div class="p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Perbarui Data Akun</h3>
                        <p class="text-slate-400 text-sm mt-1 tracking-wide">{{ $user->name }} ({{ $user->badge }})</p>
                    </div>
                    <div class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $user->status == 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                        {{ $user->status }}
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
            </div>

            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-8 space-y-8">
                    
                    {{-- Section 1: Identitas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Badge / NIP</label>
                            <input type="text" name="badge" value="{{ old('badge', $user->badge) }}" 
                                   class="w-full rounded-2xl border-slate-200 bg-slate-50 text-slate-400 font-bold cursor-not-allowed"
                                   readonly>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('name') border-rose-500 @enderror"
                                   required>
                            @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700 @error('email') border-rose-500 @enderror"
                                   required>
                            @error('email') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Section 2: Password (Yellow Box) --}}
                    <div class="p-6 bg-amber-50 rounded-[2rem] border border-amber-100">
                        <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-4">Ganti Password? (Opsional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-amber-700 uppercase mb-2">Password Baru</label>
                                <input type="password" name="password" 
                                       class="w-full rounded-2xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 placeholder-amber-300"
                                       placeholder="Kosongkan jika tidak diubah">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-amber-700 uppercase mb-2">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full rounded-2xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 placeholder-amber-300"
                                       placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Akses --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Unit Kerja</label>
                            <select name="unit_kerja_id" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach($unitKerja as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_kerja_id', $user->unit_kerja_id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Role / Hak Akses <span class="text-rose-500">*</span></label>
                            <select name="role" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $userRole->name ?? '') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Atasan Langsung / PIC</label>
                            <select name="pic_unit_kerja_id" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-700">
                                <option value="">-- Pilih Atasan --</option>
                                @foreach($picUsers->groupBy(fn($item) => $item->unitKerja->name ?? 'Admin / Pusat') as $unitName => $users)
                                    <optgroup label="UNIT: {{ strtoupper($unitName) }}">
                                        @foreach($users as $pic)
                                            <option value="{{ $pic->id }}" {{ old('pic_unit_kerja_id', $user->pic_unit_kerja_id) == $pic->id ? 'selected' : '' }}>
                                                {{ $pic->name }} ({{ $pic->badge }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Section 4: Status --}}
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Status Akun</label>
                        <div class="flex gap-6">
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="active" {{ old('status', $user->status) == 'active' ? 'checked' : '' }} 
                                       class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-bold text-slate-600 group-hover:text-slate-900">Aktif</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="inactive" {{ old('status', $user->status) == 'inactive' ? 'checked' : '' }} 
                                       class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                                <span class="ml-2 text-sm font-bold text-slate-600 group-hover:text-slate-900">Nonaktif</span>
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
                        Update Data User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>