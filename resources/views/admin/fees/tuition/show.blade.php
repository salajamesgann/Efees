<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tuition Details - E-Fees Portal</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;" x-cloak></div>

    @include('layouts.admin_sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <span class="font-bold text-lg text-slate-800 tracking-tight">Efees Admin</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        @php
            $notes = (string) ($tuitionFee->notes ?? '');
            $feeName = $notes;
            $pos = mb_strpos($notes, ' — ');
            if ($pos !== false) {
                $feeName = mb_substr($notes, 0, $pos);
            }
            if (! $feeName) {
                $feeName = $tuitionFee->grade_level.' Tuition - SY '.($tuitionFee->school_year ?? 'N/A');
            }
            $isShs = in_array($tuitionFee->grade_level, ['Grade 11', 'Grade 12']);
        @endphp

        <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50 custom-scrollbar">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Tuition Configuration</h1>
                        <p class="text-sm text-slate-500 mt-1">Review the tuition configuration, attached charges, and computed totals.</p>
                    </div>
                    <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start justify-between gap-4 mb-5">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Details</h2>
                                <p class="text-sm text-slate-500 mt-1">Core tuition configuration data.</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $tuitionFee->is_active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                {{ $tuitionFee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Fee Name</dt><dd class="font-semibold text-slate-900 text-right">{{ $feeName }}</dd></div>
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Academic Year</dt><dd class="font-semibold text-slate-900">{{ $tuitionFee->school_year ?? 'N/A' }}</dd></div>
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Grade Level</dt><dd class="font-semibold text-slate-900">{{ $tuitionFee->grade_level ?? 'N/A' }}</dd></div>
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Track</dt><dd class="font-semibold text-slate-900">{{ $isShs ? ($tuitionFee->track ?? '—') : '—' }}</dd></div>
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Strand</dt><dd class="font-semibold text-slate-900">{{ $isShs ? ($tuitionFee->strand ?? '—') : '—' }}</dd></div>
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Base Tuition</dt><dd class="font-semibold text-slate-900">₱{{ number_format((float) $tuitionFee->amount, 2) }}</dd></div>
                            <div class="border-t border-dashed border-gray-200 pt-3 flex items-center justify-between gap-4"><dt class="text-slate-500">Gross Total</dt><dd class="text-blue-600 font-bold">₱{{ number_format((float) $grossTotal, 2) }}</dd></div>
                        </dl>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.fees.edit-tuition', $tuitionFee) }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-sm hover:shadow-md hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/20">
                                    <i class="fas fa-edit"></i>
                                </span>
                                <span>Edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.fees.toggle-tuition', $tuitionFee) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="active" value="{{ $tuitionFee->is_active ? '0' : '1' }}">
                                <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 rounded-full bg-gray-800 text-white font-semibold shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/10">
                                        <i class="fas {{ $tuitionFee->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                    </span>
                                    <span>{{ $tuitionFee->is_active ? 'Deactivate' : 'Activate' }}</span>
                                </button>
                            </form>
                        </div>
                    </section>

                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Fee Summary</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Base tuition</dt><dd class="font-semibold text-slate-900">₱{{ number_format((float) $tuitionFee->amount, 2) }}</dd></div>
                            <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Additional charges</dt><dd class="font-semibold text-slate-900">₱{{ number_format((float) $chargesTotal, 2) }}</dd></div>
                            <div class="border-t border-dashed border-gray-200 pt-3 flex items-center justify-between gap-4"><dt class="text-slate-500">Gross total</dt><dd class="text-blue-600 font-bold">₱{{ number_format((float) $grossTotal, 2) }}</dd></div>
                        </dl>
                    </section>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Attached Additional Charges</h2>
                        @if($charges->count() > 0)
                            <ul class="divide-y divide-gray-200 text-sm">
                                @foreach($charges as $charge)
                                    <li class="py-3 flex items-center justify-between gap-4">
                                        <span class="text-slate-700">{{ $charge->name ?? '—' }}</span>
                                        <span class="font-semibold text-slate-900">₱{{ number_format((float) ($charge->amount ?? 0), 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-slate-500">No charges found for this configuration.</p>
                        @endif
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
