<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Payment History - Fee Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
<script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#1173d4",
              "background-light": "#f6f7f8",
              "background-dark": "#101922",
            },
            fontFamily: {
              display: ["Inter", "sans-serif"],
            },
          },
        },
      };
    </script>
    <style>
        /* Custom Sidebar Scrollbar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        /* Custom Main Content Scrollbar */
        .main-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .main-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .main-scrollbar::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }
        .main-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200" x-data="{ sidebarOpen: false }">
<style>[x-cloak]{display:none!important}</style>
<div class="flex min-h-screen">
<div class="md:hidden w-full bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
            <i class="fas fa-user-shield text-lg"></i>
        </div>
        <span class="font-bold text-lg text-blue-900">Efees Staff</span>
    </div>
    <button @click="sidebarOpen = true" class="text-slate-600 hover:text-slate-800">
        <i class="fas fa-bars text-xl"></i>
    </button>
</div>
    @include('layouts.staff_sidebar')

<main class="flex-1 md:h-screen overflow-y-auto main-scrollbar p-8">
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    <header class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment History</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Review all transactions per student</p>
        </div>
        <a href="{{ route('staff.reports') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <i class="fas fa-file-alt"></i>
            Go to Reports
        </a>
    </header>

    <div class="bg-white dark:bg-background-dark rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Student (Name or ID)</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Enter Student Name or ID" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
            </div>
            <div>
                <label for="method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                <select name="method" id="method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
                    <option value="">All Methods</option>
                    @foreach($methods as $m)
                        @php
                            $methodLabels = [
                                'gcash' => 'GCash',
                                'grab_pay' => 'Grab',
                                'paymaya' => 'Maya',
                                'card' => 'Credit/Debit Card',
                            ];
                        @endphp
                        <option value="{{ $m }}" {{ $method == $m ? 'selected' : '' }}>{{ $methodLabels[$m] ?? ucwords(str_replace(['_', '-'], ' ', $m)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from" id="from" value="{{ $from }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to" id="to" value="{{ $to }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-primary hover:bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-200">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-background-dark rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <th scope="col" class="px-6 py-3">Student</th>
                        <th scope="col" class="px-6 py-3">Amount</th>
                        <th scope="col" class="px-6 py-3">Method</th>
                        <th scope="col" class="px-6 py-3">Reference</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Receipt</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">{{ $p->paid_at ? $p->paid_at->format('M d, Y H:i') : '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $p->student ? $p->student->full_name : 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $p->student_id }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">₱{{ number_format((float)($p->amount_paid ?? 0), 2) }}</td>
                            <td class="px-6 py-4">{{ $p->method }}</td>
                            <td class="px-6 py-4">{{ $p->reference_number ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php $pendingVoid = $p->voidRequests->where('status', 'pending')->first(); @endphp
                                @if($p->status === 'voided')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><i class="fas fa-ban"></i> Voided</span>
                                @elseif($pendingVoid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700"><i class="fas fa-clock"></i> Void Pending</span>
                                @elseif($p->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><i class="fas fa-check-circle"></i> Approved</span>
                                @elseif($p->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><i class="fas fa-times-circle"></i> Rejected</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700"><i class="fas fa-hourglass-half"></i> Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($p->status === 'approved' || $p->receipt)
                                    <a href="{{ route('staff.payments.receipt', $p) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fas fa-external-link-alt text-xs"></i> View
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($p->status === 'approved' && !$pendingVoid)
                                    <button @click="$dispatch('open-void-modal', { paymentId: {{ $p->id }}, refNumber: '{{ $p->reference_number ?? 'N/A' }}', amount: '₱{{ number_format((float)($p->amount_paid ?? 0), 2) }}', studentName: '{{ $p->student ? addslashes($p->student->full_name) : 'Unknown' }}' })" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 font-medium text-xs rounded-lg transition-colors">
                                        <i class="fas fa-undo-alt"></i> Request Void
                                    </button>
                                @elseif($pendingVoid)
                                    <span class="text-xs text-amber-600 italic">Awaiting approval</span>
                                @elseif($p->status === 'voided')
                                    @php $approvedVoid = $p->voidRequests->where('status', 'approved')->first(); @endphp
                                    <span class="text-xs text-red-500" title="{{ $approvedVoid ? $approvedVoid->reason : '' }}">Voided</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No payments found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $payments->links() }}
        </div>
    </div>
</main>
</div>

<!-- Void Request Modal -->
<div x-data="{ open: false, paymentId: null, refNumber: '', amount: '', studentName: '' }"
     @open-void-modal.window="open = true; paymentId = $event.detail.paymentId; refNumber = $event.detail.refNumber; amount = $event.detail.amount; studentName = $event.detail.studentName"
     x-cloak>
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition>
        <div x-show="open" @click="open = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div x-show="open" @click.stop class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-undo-alt text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Request Payment Void</h3>
                    <p class="text-sm text-gray-500">This requires admin approval</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-4 space-y-1.5">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Student:</span>
                    <span class="font-medium text-gray-900" x-text="studentName"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Reference:</span>
                    <span class="font-medium text-gray-900" x-text="refNumber"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Amount:</span>
                    <span class="font-bold text-red-600" x-text="amount"></span>
                </div>
            </div>

            <form :action="'/staff/payments/' + paymentId + '/void'" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="void-reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Void <span class="text-red-500">*</span></label>
                    <textarea name="reason" id="void-reason" rows="3" required maxlength="1000" placeholder="Explain why this payment needs to be voided..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 text-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium rounded-lg transition-colors text-sm">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white hover:bg-red-700 font-medium rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
