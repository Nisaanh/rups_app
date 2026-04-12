@extends('layouts.app')

@section('title', 'Statistik Activity Logs')
@section('header', 'Statistik Activity Logs')

@section('content')
<div class="space-y-6">
    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Aktivitas</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">24 Jam Terakhir</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['last_24h']) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">7 Hari Terakhir</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['last_7d']) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">30 Hari Terakhir</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['last_30d']) }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Activity Chart --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas 7 Hari Terakhir</h3>
            <canvas id="dailyChart" height="250"></canvas>
        </div>
        
        {{-- Hourly Activity Chart --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas per Jam (Hari Ini)</h3>
            <canvas id="hourlyChart" height="250"></canvas>
        </div>
    </div>
    
    {{-- Top Users & Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Active Users --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Top 10 User Paling Aktif</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($stats['by_user'] as $item)
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                <span class="font-medium">{{ $item->user ? substr($item->user->name, 0, 1) : '?' }}</span>
                            </div>
                            <div>
                                <p class="font-medium">{{ $item->user ? $item->user->name : 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $item->user ? $item->user->badge : '-' }}</p>
                            </div>
                        </div>
                        <div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ number_format($item->total) }} aktivitas
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        {{-- Actions Distribution --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Distribusi Actions</h3>
            </div>
            <div class="p-6">
                <canvas id="actionChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyData = {!! json_encode($stats['daily_last_week']) !!};
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => item.date),
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: dailyData.map(item => item.total),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = {!! json_encode($stats['hourly_today']) !!};
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyData.map(item => item.hour + ':00'),
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: hourlyData.map(item => item.total),
                backgroundColor: '#10B981',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Action Distribution Chart
    const actionCtx = document.getElementById('actionChart').getContext('2d');
    const actionData = {!! json_encode($stats['by_action']) !!};
    
    const actionColors = {
        create: '#10B981',
        update: '#3B82F6',
        delete: '#EF4444',
        login: '#8B5CF6',
        logout: '#6B7280',
        approve: '#14B8A6',
        reject: '#F97316'
    };
    
    new Chart(actionCtx, {
        type: 'doughnut',
        data: {
            labels: actionData.map(item => item.action.charAt(0).toUpperCase() + item.action.slice(1)),
            datasets: [{
                data: actionData.map(item => item.total),
                backgroundColor: actionData.map(item => actionColors[item.action] || '#9CA3AF'),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection