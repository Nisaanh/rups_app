<x-app-layout>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 ">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total User</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 ">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">User Aktif</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['active'] }}</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nonaktif</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $stats['inactive'] }}</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-5 ">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit Terlibat</p>
                <p class="text-2xl font-black text-purple-600 mt-1">{{ count($stats['by_unit']) }}</p>
            </div>
        </div>

        {{-- Actions Bar --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center flex-wrap gap-4">
                <a href="{{ route('users.create') }}" class="inline-flex items-center px-5 py-2.5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Tambah User
                </a>

                <form method="GET" class="flex gap-2">
                    <select name="role" class="px-4 py-2 border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:ring-slate-900">
                        <option value="">Semua Role</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/badge/email..."
                            class="pl-4 pr-10 py-2 border-slate-200 rounded-xl w-64 text-xs font-bold focus:ring-slate-900">
                    </div>
                    <button type="submit" class="bg-slate-100 text-slate-900 px-4 py-2 rounded-xl font-black text-xs uppercase hover:bg-slate-200 transition">Cari</button>
                    @if($search || request('role'))
                    <a href="{{ route('users.index') }}" class="bg-rose-50 text-rose-600 px-4 py-2 rounded-xl font-black text-xs uppercase hover:bg-rose-100 transition">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Users Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Badge</th>
                            <th class="px-6 py-4">Unit Kerja</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center mr-3 shadow-sm text-white font-black text-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm leading-none">{{ $user->name }}</p>
                                        <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-black text-slate-600 bg-slate-100 px-2 py-1 rounded-lg italic">{{ $user->badge }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                {{ $user->unitKerja->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-blue-100">
                                    {{ $user->getRoleNames()->first() ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700">
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-100 text-rose-700">
                                    Nonaktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('users.show', $user) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-blue-600 rounded-xl transition hover:bg-blue-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-emerald-600 rounded-xl transition hover:bg-emerald-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- PERBAIKAN: Fungsi showDeleteModal dipanggil di sini --}}
                                    <button type="button"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        onclick="showDeleteModal(this)"
                                        class="p-2 bg-slate-50 text-slate-400 hover:text-rose-600 rounded-xl transition hover:bg-rose-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <p class="text-slate-400 italic text-sm">Belum ada data user terdaftar.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-50">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 transition-all">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full mx-4 overflow-hidden border border-slate-100">
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-3xl bg-rose-50 mb-6">
                    <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Konfirmasi Hapus</h3>
                <p class="text-sm text-slate-500 mb-8 leading-relaxed px-4">Apakah Anda yakin ingin menghapus user <span id="deleteUserName" class="font-black text-slate-900"></span>? Data yang dihapus tidak dapat dipulihkan.</p>
                <div class="flex justify-center space-x-3">
                    <button onclick="closeDeleteModal()" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase hover:bg-slate-200 transition">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-rose-600 text-white rounded-2xl font-black text-xs uppercase hover:bg-rose-700 shadow-lg shadow-rose-200 transition">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
       function showDeleteModal(button) {
            // 1. Ambil data dari attribute data-id dan data-name
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            // 2. Isi nama user ke dalam modal (Gunakan ID yang benar: deleteUserName)
            document.getElementById('deleteUserName').textContent = name;
            
            // 3. Atur action form ke route users.destroy
            let url = "{{ route('users.destroy', ':id') }}";
            document.getElementById('deleteForm').action = url.replace(':id', id);
            
            // 4. Tampilkan modal
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        window.onclick = function(event) {
            let modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
</x-app-layout>