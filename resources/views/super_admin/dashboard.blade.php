@extends('layouts.super_admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Super Admin Dashboard</h1>
    <p class="text-sm text-slate-500 mt-1">Platform management and statistics overview</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Students Card -->
    <a href="{{ route('super_admin.students.index') }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition-all group">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Total Students</h3>
                <p class="text-[10px] text-slate-500">Platform-wide students</p>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-lg font-extrabold text-blue-600">{{ number_format($stats['total_students']) }}</span>
            <i class="fas fa-chevron-right text-xs text-slate-300 group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    <!-- Total Users Card -->
    <a href="{{ route('super_admin.users.index') }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition-all group">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Total Users</h3>
                <p class="text-[10px] text-slate-500">Platform-wide users</p>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-lg font-extrabold text-indigo-600">{{ number_format($stats['total_users']) }}</span>
            <i class="fas fa-chevron-right text-xs text-slate-300 group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    <!-- Total Collected Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Total Collected</h3>
                <p class="text-[10px] text-slate-500">Confirmed payments</p>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-lg font-extrabold text-green-600">₱{{ number_format($stats['total_collected'], 2) }}</span>
        </div>
    </div>

    <!-- Total Outstanding Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Outstanding Balance</h3>
                <p class="text-[10px] text-slate-500">Remaining to collect</p>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-lg font-extrabold text-red-600">₱{{ number_format($stats['total_outstanding'], 2) }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Financial Overview Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-chart-pie text-blue-600"></i>
            Financial Collection Overview
        </h2>
        <div class="h-[300px] flex items-center justify-center">
            <canvas id="financialOverviewChart"></canvas>
        </div>
    </div>

    <!-- Enrollment by Level Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-chart-bar text-indigo-600"></i>
            Enrollment by Grade Level
        </h2>
        <div class="h-[300px] flex items-center justify-center">
            <canvas id="enrollmentByLevelChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-history text-blue-600"></i>
            Recent System Activity
        </h2>
        <div class="space-y-4">
            @forelse($stats['recent_activity'] as $log)
                <div class="flex items-start gap-3 pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-900 truncate">
                            {{ $log->user->name ?? 'System' }} 
                            <span class="font-normal text-slate-500">{{ $log->action }}</span>
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400">
                    <p class="text-sm italic">No recent activity recorded</p>
                </div>
            @endforelse
        </div>
        @if($stats['recent_activity']->count() > 0)
            <div class="mt-6">
                <a href="{{ route('super_admin.audit-logs.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    View all logs <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Role Distribution -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-chart-pie text-indigo-600"></i>
            User Distribution
        </h2>
        <div class="space-y-4">
            @foreach($stats['role_distribution'] as $item)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700">{{ $item->role->role_name ?? 'Unknown' }}</span>
                        <span class="text-sm font-bold text-slate-900">{{ $item->total }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        @php
                            $percentage = ($stats['total_users'] > 0) ? ($item->total / $stats['total_users']) * 100 : 0;
                        @endphp
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8 p-4 bg-slate-50 rounded-xl border border-slate-100">
            <p class="text-xs text-slate-500 leading-relaxed">
                <i class="fas fa-info-circle mr-1"></i>
                Distribution based on platform-wide user roles. Managed by Super Admin.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Financial Overview Chart
        const financialCtx = document.getElementById('financialOverviewChart').getContext('2d');
        new Chart(financialCtx, {
            type: 'doughnut',
            data: {
                labels: ['Collected', 'Outstanding'],
                datasets: [{
                    data: [{{ $stats['total_collected'] }}, {{ $stats['total_outstanding'] }}],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Enrollment by Level Chart
        const enrollmentCtx = document.getElementById('enrollmentByLevelChart').getContext('2d');
        new Chart(enrollmentCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['enrollment_by_level']->pluck('level')) !!},
                datasets: [{
                    label: 'Students',
                    data: {!! json_encode($stats['enrollment_by_level']->pluck('total')) !!},
                    backgroundColor: '#4f46e5',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            precision: 0,
                            callback: (value) => value
                        },
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
