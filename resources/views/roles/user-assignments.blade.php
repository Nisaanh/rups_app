<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Assign Role ke User') }}
            </h2>
            <a href="{{ route('roles.index') }}" class="text-sm text-blue-600 hover:underline flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Manajemen Role
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('roles.bulk-assign') }}" method="POST">
                @csrf
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    {{-- Header & Selector Role --}}
                    <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Role yang Akan Diberikan:</label>
                            <select name="role" class="w-full md:w-64 rounded-lg border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-purple-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-purple-700 shadow-md transition" onclick="return confirm('Assign role terpilih ke user yang dicentang?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                Apply Bulk Assign
                            </button>
                        </div>
                    </div>

                    {{-- Table User --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Current Roles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs mr-3 border">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 leading-none">{{ $user->name }}</p>
                                                <p class="text-[11px] text-gray-500 mt-1">{{ $user->badge }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ $user->unitKerja->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->roles as $userRole)
                                                <span class="px-2 py-0.5 bg-purple-50 text-purple-700 text-[10px] font-bold rounded border border-purple-100 uppercase">
                                                    {{ $userRole->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Data user tidak ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100 bg-gray-50">
                        {{ $users->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fitur Check All
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    </script>
</x-app-layout>