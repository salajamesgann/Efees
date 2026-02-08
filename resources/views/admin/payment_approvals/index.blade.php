<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Payment Approvals - Efees Admin</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
      body { font-family: 'Inter', 'Noto Sans', sans-serif; }
      [x-cloak] { display: none !important; }
  </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
  <!-- Mobile Sidebar Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

  <!-- Sidebar -->
  @include('layouts.admin_sidebar')

  <!-- Mobile Header & Content -->
  <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Mobile Header -->
      <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2">
              <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
                  <i class="fas fa-user-shield text-lg"></i>
              </div>
              <span class="font-bold text-lg text-blue-900">Efees</span>
          </div>
          <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
              <i class="fas fa-bars text-xl"></i>
          </button>
      </div>

      <!-- Main content -->
      <main class="flex-1 p-6 lg:p-8 overflow-y-auto bg-gray-50">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Payment Approvals</h1>
                    <p class="text-sm text-slate-500 mt-1">Review and approve pending payments</p>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <form action="{{ route('admin.payment_approvals.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ref number or student ID..." class="w-full pl-10 pr-4 py-2 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">
                        Filter
                    </button>
                </form>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Payments Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Reference</th>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Processed By</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pendingPayments as $payment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $payment->reference_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900">{{ $payment->student ? ($payment->student->first_name . ' ' . $payment->student->last_name) : 'Unknown Student' }}</span>
                                            <span class="text-xs text-slate-500">{{ $payment->student_id ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $auditUser = optional(optional($payment->submitAuditLog)->user);
                                            $staff = null;
                                            if ($auditUser && $auditUser->roleable_type === 'App\\Models\\Staff' && $auditUser->roleable) {
                                                $staff = $auditUser->roleable;
                                            }
                                            $displayName = $staff ? $staff->full_name : ($auditUser ? ($auditUser->full_name ?? $auditUser->email) : null);
                                        @endphp
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900">{{ $displayName ?? 'Unknown Staff' }}</span>
                                            @if($staff)
                                                <span class="text-xs text-slate-500">{{ $staff->staff_id }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ number_format($payment->amount_paid, 2) }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($payment->method) }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.payment_approvals.approve', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this payment?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs font-bold transition-colors border border-green-200">
                                                    Approve
                                                </button>
                                            </form>
                                            <button @click="$dispatch('open-reject-modal', { id: '{{ $payment->id }}' })" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition-colors border border-red-200">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400">
                                                <i class="fas fa-inbox text-xl"></i>
                                            </div>
                                            <p class="font-medium">No pending payments found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($pendingPayments->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        {{ $pendingPayments->links() }}
                    </div>
                @endif
            </div>
        </div>
      </main>
  </div>

  <!-- Reject Modal -->
  <div x-data="{ open: false, paymentId: null }" @open-reject-modal.window="open = true; paymentId = $event.detail.id" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="open" x-cloak>
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
          <form :action="`{{ url('admin/payment-approvals') }}/${paymentId}/reject`" method="POST">
              @csrf
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Reject Payment</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">Are you sure you want to reject this payment? This action cannot be undone.</p>
                      <div class="mt-4">
                          <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Rejection</label>
                          <textarea name="reason" id="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Enter reason..." required></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Reject Payment</button>
                <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
