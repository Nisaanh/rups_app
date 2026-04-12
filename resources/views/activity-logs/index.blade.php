@extends('layouts.app')

@section('title', 'Activity Logs')
@section('header', 'Activity Logs - Riwayat Aktivitas Sistem')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Aktivitas</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Hari Ini</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Minggu Ini</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['this_week']) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Bulan Ini</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['this_month']) }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-2">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Actions & Modules --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Top 5 Actions</h3>
            <div class="space-y-2">
                @foreach($stats['by_action'] as $action)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 capitalize">{{ $action->action }}</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 rounded-full h-2" style="width: {{ ($action->total / $stats['total']) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($action->total) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Top 5 Modules</h3>
            <div class="space-y-2">
                @foreach($stats['by_module'] as $module)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 capitalize">{{ $module->module }}</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 rounded-full h-2" style="width: {{ ($module->total / $stats['total']) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($module->total) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
                <div class="flex space-x-2">
                    <button onclick="showClearModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        🗑️ Bersihkan Log Lama
                    </button>
                    
                </div>
            </div>
            
            {{-- Filter Form --}}
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari deskripsi, IP..." 
                       class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <select name="action" class="px-3 py-2 border rounded-lg">
                    <option value="">Semua Action</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ $action == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
                
                <select name="module" class="px-3 py-2 border rounded-lg">
                    <option value="">Semua Module</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ $module == $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
                
                <select name="user_id" class="px-3 py-2 border rounded-lg">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->badge }})</option>
                    @endforeach
                </select>
                
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
                @if($search || $action || $module || $user_id || $date_from || $date_to)
                    <a href="{{ route('activity-logs.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Reset</a>
                @endif
            </form>
            
            {{-- Date Range Filter --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <input type="date" name="date_from" form="filter-form" value="{{ $date_from }}" 
                       class="px-3 py-2 border rounded-lg" placeholder="Dari Tanggal">
                <input type="date" name="date_to" form="filter-form" value="{{ $date_to }}" 
                       class="px-3 py-2 border rounded-lg" placeholder="Sampai Tanggal">
            </div>
        </div>
        
        {{-- Activity Logs Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $log->id }}</td>
                        <td class="px-6 py-4">
                            @if($log->user)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                    <span class="text-sm font-medium">{{ substr($log->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $log->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->user->badge }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $actionColors = [
                                    'create' => 'green',
                                    'update' => 'blue',
                                    'delete' => 'red',
                                    'login' => 'purple',
                                    'logout' => 'gray',
                                    'approve' => 'teal',
                                    'reject' => 'orange',
                                ];
                                $color = $actionColors[$log->action] ?? 'gray';
                            @endphp
                            <span class="px-2 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 rounded text-xs capitalize">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs capitalize">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-md truncate">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('activity-logs.show', $log) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <button onclick="showDeleteModal({{ $log->id }}, '{{ addslashes($log->description) }}')" 
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Tidak ada activity log yang ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-6">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Clear Logs Modal --}}
{{-- <div id="clearModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bersihkan Log Lama</h3>
            <form action="{{ route('activity-logs.clear') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hapus log lebih dari (hari)</label>
                    <input type="number" name="days" value="30" min="1" max="365" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Log yang lebih dari jumlah hari yang ditentukan akan dihapus permanen.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeClearModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Bersihkan</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

{{-- Delete Single Log Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus activity log ini?</p>
            <p id="deleteDescription" class="text-sm text-gray-500 bg-gray-50 p-2 rounded mb-4"></p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Batal</button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showClearModal() {
        document.getElementById('clearModal').classList.remove('hidden');
        document.getElementById('clearModal').classList.add('flex');
    }
    
    function closeClearModal() {
        document.getElementById('clearModal').classList.add('hidden');
        document.getElementById('clearModal').classList.remove('flex');
    }
    
    function showDeleteModal(id, description) {
        document.getElementById('deleteDescription').textContent = description;
        document.getElementById('deleteForm').action = `/activity-logs/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
    
    // Auto refresh filter when date changes
    document.querySelectorAll('input[name="date_from"], input[name="date_to"]').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush

@push('styles')
<style>
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 300px;
    }
    
    /* Animation for modals */
    .fixed {
        transition: opacity 0.2s ease-in-out;
    }
</style>
@endpush
@endsection