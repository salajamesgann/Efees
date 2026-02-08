<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Student Profile - Efees Admin</title>
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
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-20 md:hidden" x-cloak></div>

    <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
    <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-slate-100 bg-white sticky top-0 z-10">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
            <i class="fas fa-user-shield text-lg"></i>
        </div>
        <div>
            <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
            <p class="text-xs text-slate-500 font-medium">Administration</p>
        </div>
      </div>
      <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6 overflow-y-auto custom-scrollbar">
        <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
        
        <!-- Dashboard -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin_dashboard') }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('admin_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        

        <!-- Student Management -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.students.index') }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-users text-lg {{ request()->routeIs('admin.students.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Student Management</span>
        </a>
        
          <!-- Parent Management removed -->

          <!-- User Management -->
          <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
              <div class="w-8 flex justify-center">
                  <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
              </div>
              <span class="text-sm font-medium">User Management</span>
          </a>

        <!-- Fee Management -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-file-invoice-dollar text-lg {{ request()->routeIs('admin.fees.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Fee Management</span>
        </a>

        <!-- Payment Approvals -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.payment_approvals.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.payment_approvals.index') }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-check-double text-lg {{ request()->routeIs('admin.payment_approvals.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Payment Approvals</span>
        </a>

        <!-- Reports & Analytics -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.reports.index') }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-chart-bar text-lg {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Reports & Analytics</span>
        </a>

        <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">System</p>

        <!-- Audit Logs -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.audit-logs.index') }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-shield-alt text-lg {{ request()->routeIs('admin.audit-logs.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Audit Logs</span>
        </a>

        <!-- SMS Control -->
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sms.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.sms.logs') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-comment-alt text-lg {{ request()->routeIs('admin.sms.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Control</span>
            </a>

        <!-- Settings -->
        <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.settings.index') }}">
            <div class="w-8 flex justify-center">
                <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
            </div>
            <span class="text-sm font-medium">Settings</span>
        </a>
    </nav>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
            <div class="w-8 flex justify-center">
                <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
            </div>
            <span class="text-sm font-bold">Logout</span>
        </button>
    </form>
  </aside>

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

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <div class="p-6 md:p-8 max-w-6xl mx-auto space-y-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.enrollment.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> <span class="text-sm font-medium">Back to List</span>
                    </a>
                    <div class="flex-grow"></div>
                    <a href="{{ route('admin.enrollment.edit', $student) }}" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors shadow-sm">
                        <i class="fas fa-edit"></i> Edit Details
                    </a>
                </div>

                <!-- Profile Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-bold text-3xl flex-shrink-0 border border-blue-200">
                            {{ substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1) }}
                        </div>
                        <div class="flex-grow w-full">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $student->full_name }}</h1>
                                    <p class="text-gray-500 font-mono text-sm mt-1">{{ $student->student_id }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($student->is_shs_voucher)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-ticket-alt mr-1.5"></i> SHS Voucher
                                    </span>
                                    @endif
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $student->enrollment_status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $student->enrollment_status === 'Inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $student->enrollment_status === 'Archived' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $student->enrollment_status === 'Graduated' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $student->enrollment_status === 'Dropped' ? 'bg-orange-100 text-orange-800' : '' }}
                                    ">
                                        {{ $student->enrollment_status ?? 'Active' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-100">
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Grade & Section</p>
                                    <p class="font-medium text-gray-900">{{ $student->level }} - {{ $student->section }}</p>
                                    @if($student->strand)
                                        <p class="text-xs text-gray-500">{{ $student->strand }}</p>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">School Year</p>
                                    <p class="font-medium text-gray-900">{{ $student->school_year ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Contact Email</p>
                                    <p class="font-medium text-gray-900 break-all">{{ $student->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Fee Summary -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-900">Fee Summary</h2>
                                <a href="{{ route('admin.fees.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">Manage Fees</a>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Total Fees</p>
                                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalFees, 2) }}</p>
                                </div>
                                <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                                    <p class="text-xs text-green-600 uppercase font-bold mb-1">Paid</p>
                                    <p class="text-2xl font-bold text-green-700">₱{{ number_format($totalPaid, 2) }}</p>
                                </div>
                                <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                                    <p class="text-xs text-red-600 uppercase font-bold mb-1">Balance</p>
                                    <p class="text-2xl font-bold text-red-700">₱{{ number_format($balance, 2) }}</p>
                                </div>
                            </div>

                            <h3 class="font-bold text-gray-900 mb-4 text-sm">Recent Transactions</h3>
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                                        <tr>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3 text-right">Amount</th>
                                            <th class="px-4 py-3 text-right">Balance</th>
                                            <th class="px-4 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @forelse($student->feeRecords->sortByDesc('created_at')->take(5) as $record)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $record->type }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($record->amount, 2) }}</td>
                                                <td class="px-4 py-3 text-right text-gray-600">₱{{ number_format($record->balance, 2) }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold 
                                                        {{ $record->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-6 text-center text-gray-500 text-sm">No fee records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Fee Adjustment Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-900">Fee Adjustment</h2>
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100">Admin Only</span>
                            </div>

                            <!-- Manage Discounts -->
                            <div class="mb-8 border-b border-gray-100 pb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wide">Applied Discounts</h3>
                                    <div x-data="{ showModal: false }">
                                        <button @click="showModal = true" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded transition-colors flex items-center gap-1">
                                            <i class="fas fa-plus"></i> Add Discount
                                        </button>

                                        <!-- Modal -->
                                        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="showModal" @click="showModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                                                </div>
                                                <div x-show="showModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                    <form action="{{ route('admin.enrollment.discounts.store', $student) }}" method="POST">
                                                        @csrf
                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add Discount</h3>
                                                            <div class="space-y-4">
                                                                <div>
                                                                    <label for="discount_id" class="block text-sm font-medium text-gray-700">Select Discount</label>
                                                                    <select id="discount_id" name="discount_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                                        @forelse($availableDiscounts as $d)
                                                                            <option value="{{ $d->id }}">
                                                                                {{ $d->discount_name }} ({{ $d->type === 'percentage' ? $d->value . '%' : '₱' . number_format($d->value, 2) }})
                                                                            </option>
                                                                        @empty
                                                                            <option disabled>No available discounts</option>
                                                                        @endforelse
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" {{ $availableDiscounts->isEmpty() ? 'disabled' : '' }}>
                                                                Add Discount
                                                            </button>
                                                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($feeAssignment && $feeAssignment->discounts->count() > 0)
                                    <div class="space-y-2 mb-4">
                                        @foreach($feeAssignment->discounts as $discount)
                                            <div class="flex items-center justify-between bg-white p-3 border border-gray-200 rounded-lg hover:shadow-sm transition-shadow">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-sm font-medium text-gray-900">{{ $discount->discount_name }}</p>
                                                        @if($discount->is_automatic)
                                                            <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">AUTO</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-gray-500">{{ $discount->type === 'percentage' ? $discount->value . '%' : '₱' . number_format($discount->value, 2) }} off</p>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                     <span class="text-sm font-bold text-green-600">-₱{{ number_format($discount->pivot->applied_amount ?? 0, 2) }}</span>
                                                     <form action="{{ route('admin.enrollment.discounts.destroy', ['student' => $student, 'discount' => $discount]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this discount?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Remove Discount">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                     </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic mb-4">No discounts applied.</p>
                                @endif
                            </div>

                            <!-- Current Breakdown -->
                            @if(isset($feeAssignment))
                                <div class="bg-gray-50 rounded-xl p-5 mb-8 border border-gray-200">
                                    <h3 class="font-bold text-gray-700 mb-4 text-xs uppercase tracking-wide">Current Assessment Breakdown</h3>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Base Tuition</span>
                                            <span class="font-medium text-gray-900">₱{{ number_format($feeAssignment->base_tuition, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Additional Charges</span>
                                            <span class="font-medium text-red-600">+₱{{ number_format($feeAssignment->additional_charges_total, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                            <span class="text-gray-600">Discounts</span>
                                            <span class="font-medium text-green-600">-₱{{ number_format($feeAssignment->discounts_total, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center font-bold text-gray-900 text-lg pt-1">
                                            <span>Total Payable</span>
                                            <span>₱{{ number_format($feeAssignment->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Adjustment Form -->
                            <form action="{{ route('admin.enrollment.adjustments.store', $student) }}" method="POST" class="mb-8 bg-gray-50/50 p-5 rounded-xl border border-gray-200/60">
                                @csrf
                                <h3 class="font-bold text-gray-900 mb-4 text-sm flex items-center gap-2">
                                    <i class="fas fa-plus-circle text-blue-600"></i> Apply New Adjustment
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Adjustment Type</label>
                                        <select name="type" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                            <option value="discount">Discount (Deduct)</option>
                                            <option value="charge">Additional Charge (Add)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Amount (₱)</label>
                                        <input type="number" step="0.01" min="0" name="amount" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="0.00">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Name / Reason</label>
                                        <input type="text" name="name" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="e.g. Academic Scholarship, Late Registration Fee">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Remarks (Optional)</label>
                                        <textarea name="remarks" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Add any specific details or notes..."></textarea>
                                    </div>
                                    <div class="md:col-span-2 flex items-center bg-white p-2 rounded-lg border border-gray-200">
                                        <input type="checkbox" name="notify_sms" id="notify_sms" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="notify_sms" class="ml-2 block text-xs font-medium text-gray-700">
                                            Notify Parent/Guardian via SMS
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition-colors text-sm shadow-md hover:shadow-lg">
                                    Apply Adjustment
                                </button>
                            </form>

                            <!-- Adjustment History -->
                            <h3 class="font-bold text-gray-900 mb-4 text-sm flex items-center gap-2">
                                <i class="fas fa-history text-gray-400"></i> Adjustment History
                            </h3>
                            @if(isset($feeAssignment) && $feeAssignment->adjustments->isNotEmpty())
                                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                                    @foreach($feeAssignment->adjustments->sortByDesc('created_at') as $adj)
                                        <div class="flex items-start justify-between p-3 rounded-lg border {{ $adj->type == 'discount' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="font-semibold text-gray-900 text-sm">{{ $adj->name }}</p>
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded uppercase font-bold {{ $adj->type == 'discount' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                                        {{ $adj->type }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">{{ $adj->created_at->format('M d, Y h:i A') }}</p>
                                                @if($adj->remarks)
                                                    <p class="text-xs text-gray-600 mt-1 italic">"{{ $adj->remarks }}"</p>
                                                @endif
                                            </div>
                                            <span class="font-bold text-sm {{ $adj->type == 'discount' ? 'text-green-700' : 'text-red-700' }}">
                                                {{ $adj->type == 'discount' ? '-' : '+' }}₱{{ number_format($adj->amount, 2) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    <p class="text-sm text-gray-500">No manual adjustments applied yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="lg:col-span-1 space-y-6">
                         <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="font-bold text-gray-900 mb-4 text-sm">Parent/Guardian</h3>
                            @php
                                $primaryParent = $student->parents->where('pivot.is_primary', true)->first();
                                if (!$primaryParent) {
                                    $primaryParent = $student->parents->first();
                                }
                            @endphp
                            @if($primaryParent)
                                <div class="mb-4 pb-4 border-b border-gray-100">
                                    <p class="font-semibold text-gray-900 text-sm flex items-center gap-2">
                                        {{ $primaryParent->full_name }}
                                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Primary</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mb-1">{{ $primaryParent->pivot->relationship }}</p>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-phone w-4 text-gray-400"></i>
                                        <span>{{ $primaryParent->phone }}</span>
                                    </div>
                                </div>
                            @endif
                         </div>

                         <!-- Quick Actions -->
                         <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="font-bold text-gray-900 mb-4 text-sm">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.sms.logs') }}" class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium text-gray-700 transition-colors border border-gray-100">
                                    <i class="fas fa-comment-alt text-blue-500"></i> Send SMS
                                </a>
                                <a href="{{ route('admin.enrollment.print', $student) }}" target="_blank" class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm font-medium text-gray-700 transition-colors border border-gray-100">
                                    <i class="fas fa-print text-blue-500"></i> Print Statement
                                </a>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
