@extends('layouts.super_admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">System Configuration</h1>
    <p class="text-sm text-slate-500 mt-1">Manage global platform settings and system behavior</p>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 animate-pulse">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('super_admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    @csrf
    
    <!-- Left Column: Branding -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                <i class="fas fa-palette text-blue-500"></i> Platform Branding
            </h2>
            
            <!-- School Logo -->
            <div class="space-y-4" x-data="{ logoPreview: '{{ $settings['school_logo'] ? asset('storage/'.$settings['school_logo']) : asset('images/default-logo.png') }}' }">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Institution Logo</div>
                <div class="flex flex-col items-center gap-4">
                    <div class="w-32 h-32 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                        <img :src="logoPreview" class="max-w-full max-h-full object-contain p-2" alt="Logo Preview">
                    </div>
                    <label class="cursor-pointer">
                        <span class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg transition-all">
                            Change Logo
                        </span>
                        <input type="file" name="school_logo" class="hidden" accept="image/*" @change="logoPreview = URL.createObjectURL($event.target.files[0])">
                    </label>
                </div>
            </div>

            <!-- Institution Name -->
            <div class="mt-8">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Institution Name</label>
                <input type="text" name="institution_name" value="{{ $settings['institution_name'] }}" 
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-semibold text-slate-700">
                <p class="text-[10px] text-slate-400 mt-2">Used on official receipts and generated reports.</p>
            </div>
        </div>
    </div>

    <!-- Right Column: System Behavior -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                <i class="fas fa-cogs text-indigo-500"></i> Core System Settings
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- School Year -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Active School Year</label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="school_year" value="{{ $settings['school_year'] }}" placeholder="e.g., 2024-2025"
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-mono font-bold text-blue-600">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2">Setting this will update the default year for all modules.</p>
                </div>

                <!-- Maintenance Mode -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">System Status</label>
                    <select name="maintenance_mode" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-semibold">
                        <option value="off" {{ $settings['maintenance_mode'] == 'off' ? 'selected' : '' }}>Operational (Live)</option>
                        <option value="read-only" {{ $settings['maintenance_mode'] == 'read-only' ? 'selected' : '' }}>Read-Only (Year-End Processing)</option>
                        <option value="maintenance" {{ $settings['maintenance_mode'] == 'maintenance' ? 'selected' : '' }}>Maintenance (Full Lockdown)</option>
                    </select>
                    <div class="mt-2 flex items-center gap-1.5">
                        @if($settings['maintenance_mode'] == 'off')
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            <span class="text-[10px] text-green-600 font-bold uppercase">All services active</span>
                        @elseif($settings['maintenance_mode'] == 'read-only')
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span class="text-[10px] text-amber-600 font-bold uppercase">Writes disabled for non-admins</span>
                        @else
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-[10px] text-red-600 font-bold uppercase">Emergency mode active</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- System Notice -->
            <div class="mt-8">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Global System Notice</label>
                <textarea name="system_notice" rows="3" placeholder="Display a banner to all users..."
                          class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none">{{ $settings['system_notice'] }}</textarea>
                <p class="text-[10px] text-slate-400 mt-2 italic">This message will appear on all dashboards when enabled.</p>
            </div>
        </div>

        <!-- Danger Zone / Actions -->
        <div class="flex items-center justify-end gap-4">
            <button type="reset" class="px-6 py-2.5 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all">
                Reset Changes
            </button>
            <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Apply System Changes
            </button>
        </div>
    </div>
</form>
@endsection
