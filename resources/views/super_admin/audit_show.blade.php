@extends('layouts.super_admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Audit Detail</h1>
        <p class="text-sm text-slate-500 mt-1">Detailed breakdown of the selected activity</p>
    </div>
    <a href="{{ route('super_admin.audit-logs.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold rounded-xl transition-all duration-200 shadow-sm flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to Logs
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Log Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Activity Context</h2>
            <div class="space-y-4">
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Action</div>
                    <div class="px-2.5 py-1 rounded-lg text-sm font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100 inline-block">
                        {{ $log->action }}
                    </div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Timestamp</div>
                    <div class="text-sm font-semibold text-slate-700">{{ $log->created_at->format('M d, Y - h:i:s A') }}</div>
                    <div class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">User Involved</div>
                    <div class="flex items-center gap-3 mt-1">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            {{ substr($log->user->name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">{{ $log->user->name ?? 'System' }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $log->user_role ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Network Info</div>
                    <div class="text-sm font-semibold text-slate-700 font-mono">{{ $log->ip_address }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 leading-relaxed truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Breakdown -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Data Snapshot</h2>
            
            @if($log->old_values || $log->new_values)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Previous State -->
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Previous Values</div>
                        @if($log->old_values)
                            <div class="bg-slate-50 rounded-xl p-4 overflow-x-auto custom-scrollbar">
                                <pre class="text-xs text-slate-600 font-mono leading-relaxed">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @else
                            <div class="bg-slate-50 rounded-xl p-4 text-xs text-slate-400 italic">No previous values recorded</div>
                        @endif
                    </div>

                    <!-- New State -->
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Updated Values</div>
                        @if($log->new_values)
                            <div class="bg-green-50 rounded-xl p-4 border border-green-100 overflow-x-auto custom-scrollbar">
                                <pre class="text-xs text-green-700 font-mono leading-relaxed">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @else
                            <div class="bg-slate-50 rounded-xl p-4 text-xs text-slate-400 italic">No updated values recorded</div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <h3 class="text-slate-800 font-bold mb-1">No Data Changes Logged</h3>
                    <p class="text-slate-500 text-sm">This action did not modify any existing database fields.</p>
                </div>
            @endif

            @if($log->details)
                <div class="mt-8 pt-8 border-t border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Technical Details</div>
                    <div class="bg-slate-900 rounded-xl p-4 text-xs text-blue-300 font-mono leading-relaxed">
                        {{ $log->details }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
