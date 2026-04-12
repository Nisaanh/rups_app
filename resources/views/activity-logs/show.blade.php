@extends('layouts.app')

@section('title', 'Detail Activity Log')
@section('header', 'Detail Activity Log')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Activity Logs
        </a>
    </div>
    
    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Basic Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">ID Log</p>
                        <p class="font-medium">#{{ $activityLog->id }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">User</p>
                        <div class="flex items-center mt-1">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                <span class="text-lg font-medium">{{ $activityLog->user ? substr($activityLog->user->name, 0, 1) : 'S' }}</span>
                            </div>
                            <div>
                                @if($activityLog->user)
                                    <p class="font-medium">{{ $activityLog->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $activityLog->user->badge }} | {{ $activityLog->user->email }}</p>
                                @else
                                    <p class="font-medium">System</p>
                                    <p class="text-sm text-gray-500">Aksi otomatis sistem</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Action</p>
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-800',
                                'update' => 'bg-blue-100 text-blue-800',
                                'delete' => 'bg-red-100 text-red-800',
                                'login' => 'bg-purple-100 text-purple-800',
                                'logout' => 'bg-gray-100 text-gray-800',
                                'approve' => 'bg-teal-100 text-teal-800',
                                'reject' => 'bg-orange-100 text-orange-800',
                            ];
                            $colorClass = $actionColors[$activityLog->action] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <p class="mt-1">
                            <span class="px-3 py-1 {{ $colorClass }} rounded-full text-sm capitalize">
                                {{ $activityLog->action }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Module</p>
                        <p class="mt-1">
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm capitalize">
                                {{ $activityLog->module }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">IP Address</p>
                        <p class="font-mono">{{ $activityLog->ip_address ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">User Agent</p>
                        <p class="text-sm text-gray-600 break-all">{{ $activityLog->user_agent ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Waktu</p>
                        <p>{{ $activityLog->created_at->format('d F Y H:i:s') }}</p>
                        <p class="text-sm text-gray-500">{{ $activityLog->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Right Column - Description & Data Changes --}}
        <div class="lg:col-span-2">
            {{-- Description --}}
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Deskripsi Aktivitas</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700">{{ $activityLog->description }}</p>
                </div>
            </div>
            
            {{-- Data Changes --}}
            @if($oldData || $newData)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Perubahan Data</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($oldData)
                        <div>
                            <h4 class="font-medium text-red-600 mb-3">Data Sebelumnya (Old)</h4>
                            <div class="bg-red-50 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-sm">{{ json_encode($oldData, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                        @endif
                        
                        @if($newData)
                        <div>
                            <h4 class="font-medium text-green-600 mb-3">Data Baru (New)</h4>
                            <div class="bg-green-50 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-sm">{{ json_encode($newData, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($oldData && $newData)
                    <div class="mt-6">
                        <h4 class="font-medium text-blue-600 mb-3">Perubahan Detail</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @php
                                $changes = [];
                                foreach($newData as $key => $value) {
                                    if(isset($oldData[$key]) && $oldData[$key] != $value) {
                                        $changes[$key] = [
                                            'old' => $oldData[$key],
                                            'new' => $value
                                        ];
                                    }
                                }
                            @endphp
                            
                            @if(count($changes) > 0)
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-2">Field</th>
                                            <th class="text-left py-2">Old Value</th>
                                            <th class="text-left py-2">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($changes as $field => $change)
                                        <tr class="border-b">
                                            <td class="py-2 font-medium">{{ $field }}</td>
                                            <td class="py-2 text-red-600">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</td>
                                            <td class="py-2 text-green-600">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500">Tidak ada perubahan field yang signifikan</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection