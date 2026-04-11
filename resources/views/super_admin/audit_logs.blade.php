@extends('layouts.super_admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">System Audit Logs</h1>
        <p class="text-sm text-slate-500 mt-1">Track all system actions, data changes, and security events</p>
    </div>
</div>

<!-- Advanced Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
    <form action="{{ route('super_admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="lg:col-span-1">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Action, Email, IP..." 
                       class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
        </div>

        <!-- Action Type -->
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Action Type</label>
            <select name="action_type" class="w-full px-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="all">All Actions</option>
                @foreach($actionTypes as $type)
                    <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <!-- User -->
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">User</label>
            <select name="user_id" class="w-full px-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="all">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ ucfirst($user->role->role_name) }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date Start -->
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">From Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                   class="w-full px-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
        </div>

        <!-- Date End -->
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-4 py-2 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <button type="submit" class="p-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-100 h-[38px] w-[38px] flex items-center justify-center">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('super_admin.audit-logs.index') }}" class="p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-colors h-[38px] w-[38px] flex items-center justify-center" title="Clear Filters">
                <i class="fas fa-undo text-sm"></i>
            </a>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Timestamp</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Entity</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $log->created_at->format('h:i:s A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-700">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $log->user_role ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider
                                {{ str_contains($log->action, 'Deleted') || str_contains($log->action, 'Removed') ? 'bg-red-50 text-red-600 border border-red-100' : 
                                   (str_contains($log->action, 'Updated') || str_contains($log->action, 'Modified') ? 'bg-amber-50 text-amber-600 border border-amber-100' : 
                                   'bg-blue-50 text-blue-600 border border-blue-100') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-600">{{ class_basename($log->model_type) }}</div>
                            <div class="text-xs text-slate-400">ID: {{ $log->model_id ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('super_admin.audit-logs.show', $log) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                            No activity logs found for the selected criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/50">
        {{ $logs->links() }}
    </div>
</div>
@endsection
