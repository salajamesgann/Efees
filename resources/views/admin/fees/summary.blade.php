<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fee Summary - E-Fees Portal</title>
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

    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">
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

        <main class="flex-1 overflow-y-auto bg-slate-50 custom-scrollbar p-4 md:p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                <section class="rounded-3xl bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white shadow-xl overflow-hidden relative">
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35) 0, transparent 22%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.18) 0, transparent 18%), radial-gradient(circle at 100% 100%, rgba(59,130,246,0.35) 0, transparent 24%);"></div>
                    <div class="relative p-6 md:p-8 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div class="space-y-3 max-w-3xl">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">
                                <i class="fas fa-layer-group"></i>
                                Fee Summary
                            </div>
                            <div>
                                <h1 class="text-3xl md:text-4xl font-black tracking-tight">Fee Summary</h1>
                                <p class="mt-2 text-sm md:text-base text-blue-100/90 max-w-2xl">Review tuition, additional charges, and discounts for the selected grade in one clean view.</p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15">School Year: {{ $schoolYear ?? 'N/A' }}</span>
                                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15">Semester: {{ $semester ?? 'N/A' }}</span>
                                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15">Grade: {{ $gradeLevel ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white border border-white/15 hover:bg-white/15 transition-colors">
                                <i class="fas fa-arrow-left"></i>
                                Back to Fees
                            </a>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-3xl shadow-sm border border-slate-200 p-5 md:p-6">
                    <form method="GET" action="{{ route('admin.fees.summary') }}" class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-4 items-end">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Grade Level</label>
                            <select name="grade_level" class="w-full md:w-80 rounded-xl border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15">
                                @foreach($gradeLevels as $grade)
                                    <option value="{{ $grade }}" {{ ($gradeLevel ?? '') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                            <i class="fas fa-sync-alt"></i>
                            Update Summary
                        </button>
                    </form>
                </section>

                <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Base Tuition</p>
                                <p class="mt-2 text-2xl font-black text-slate-900">₱{{ number_format($baseTuition, 2) }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Additional Charges</p>
                                <p class="mt-2 text-2xl font-black text-slate-900">₱{{ number_format($additionalChargesTotal, 2) }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3xl bg-white border border-slate-200 p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Discounts</p>
                                <p class="mt-2 text-2xl font-black text-slate-900">-₱{{ number_format($discountsTotal, 2) }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-percent"></i>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-600 border border-blue-500/20 p-5 shadow-sm text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-100 font-medium">Net Payable</p>
                                <p class="mt-2 text-2xl font-black">₱{{ number_format($totalAmount, 2) }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-2xl bg-white/15 text-white flex items-center justify-center">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 xl:grid-cols-5">
                    <div class="xl:col-span-3 rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-200 flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Fee Context</h2>
                                <p class="text-sm text-slate-500 mt-1">Current grade selection and tuition record overview.</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $gradeLevel ?? 'N/A' }}</span>
                        </div>
                        <div class="p-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">School Year</p>
                                <p class="mt-2 text-base font-bold text-slate-900">{{ $schoolYear ?? 'N/A' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Semester</p>
                                <p class="mt-2 text-base font-bold text-slate-900">{{ $semester ?? 'N/A' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 sm:col-span-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tuition Record</p>
                                @if($tuitionFee)
                                    <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-base font-bold text-slate-900">{{ $tuitionFee->grade_level }}</p>
                                            <p class="text-sm text-slate-500">{{ $tuitionFee->fee_name ?? 'Tuition fee' }}</p>
                                        </div>
                                        <p class="text-lg font-black text-blue-600">{{ $tuitionFee->formatted_amount }}</p>
                                    </div>
                                @else
                                    <p class="mt-2 text-sm text-slate-600">No active tuition fee found for the selected grade.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="xl:col-span-2 rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-200">
                            <h2 class="text-lg font-bold text-slate-900">Tuition Breakdown</h2>
                            <p class="text-sm text-slate-500 mt-1">Base tuition plus any subject fee components.</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Base tuition</span>
                                <span class="font-semibold text-slate-900">₱{{ number_format($baseTuition, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Additional charges</span>
                                <span class="font-semibold text-slate-900">₱{{ number_format($additionalChargesTotal, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Discounts</span>
                                <span class="font-semibold text-emerald-600">-₱{{ number_format($discountsTotal, 2) }}</span>
                            </div>
                            <div class="border-t border-dashed border-slate-200 pt-4 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-700">Net payable</span>
                                <span class="text-xl font-black text-blue-600">₱{{ number_format($totalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-200 flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Additional Charges</h2>
                                <p class="text-sm text-slate-500 mt-1">Charges applied to the selected grade.</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">{{ $additionalCharges->count() }} item(s)</span>
                        </div>
                        <div class="p-6">
                            @if($additionalCharges->count() > 0)
                                <div class="space-y-3">
                                    @foreach($additionalCharges as $charge)
                                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $charge->charge_name }}</p>
                                            </div>
                                            <p class="font-bold text-slate-900">{{ $charge->formatted_amount }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center">
                                    <p class="text-sm text-slate-600">No additional charges for this grade.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-200 flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Tuition Fee</h2>
                                <p class="text-sm text-slate-500 mt-1">Active fee record for the chosen grade.</p>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($tuitionFee)
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Grade Level</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900">{{ $tuitionFee->grade_level }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</p>
                                            <p class="mt-1 text-lg font-black text-blue-600">{{ $tuitionFee->formatted_amount }}</p>
                                        </div>
                                    </div>
                                    @if(!empty($tuitionFee->subject_fees) && is_array($tuitionFee->subject_fees))
                                        <div class="mt-4 border-t border-slate-200 pt-4">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Subject Fees</p>
                                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                                <table class="min-w-full text-sm">
                                                    <thead class="bg-slate-50 text-slate-500">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left font-semibold">Item</th>
                                                            <th class="px-4 py-3 text-right font-semibold">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-200">
                                                        @foreach($tuitionFee->subject_fees as $subjectFee)
                                                            <tr>
                                                                <td class="px-4 py-3 text-slate-700">{{ $subjectFee['name'] ?? $subjectFee['subject_name'] ?? 'Subject Fee' }}</td>
                                                                <td class="px-4 py-3 text-right font-semibold text-slate-900">₱{{ number_format((float)($subjectFee['amount'] ?? 0), 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center">
                                    <p class="text-sm text-slate-600">No active tuition fee found for the selected grade.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-bold text-slate-900">Applied Discounts</h2>
                        <p class="text-sm text-slate-500 mt-1">Automatic discounts applied to the current summary.</p>
                    </div>
                    <div class="p-6">
                        @if(!empty($discountBreakdown) && count($discountBreakdown) > 0)
                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-slate-50 text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold">Discount</th>
                                            <th class="px-4 py-3 text-left font-semibold">Scope</th>
                                            <th class="px-4 py-3 text-left font-semibold">Stackable</th>
                                            <th class="px-4 py-3 text-left font-semibold">Rule Value</th>
                                            <th class="px-4 py-3 text-right font-semibold">Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @foreach($discountBreakdown as $row)
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                                                <td class="px-4 py-3 text-slate-600 capitalize">{{ str_replace('_', ' ', $row['scope']) }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $row['stackable'] ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                        {{ $row['stackable'] ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-slate-700">
                                                    @if($row['type'] === 'percentage')
                                                        {{ number_format($row['value'], 2) }}%
                                                    @else
                                                        ₱{{ number_format($row['value'], 2) }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-blue-600">-₱{{ number_format($row['applied_amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                                <p class="text-sm text-slate-600">No automatic discounts applicable.</p>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
