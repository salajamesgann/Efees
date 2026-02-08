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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Custom Scrollbar for Sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { bg-transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
  <!-- Mobile Sidebar Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" style="display: none;" x-cloak></div>

  <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white text-slate-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 border-r border-slate-200 flex flex-col shrink-0 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)]" id="sidebar">
      <!-- Header -->
      <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-slate-100 bg-white sticky top-0 z-10 shrink-0">
          <div class="flex items-center gap-3">
              <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                  <i class="fas fa-user-shield text-lg"></i>
              </div>
              <div>
                  <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Admin</h1>
                  <p class="text-xs text-slate-500 font-medium">Administration</p>
              </div>
          </div>
          <!-- Mobile Close Button -->
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
          
          <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.parents.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.parents.index') }}">
              <div class="w-8 flex justify-center">
                  <i class="fas fa-user-friends text-lg {{ request()->routeIs('admin.parents.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
              </div>
              <span class="text-sm font-medium">Parent Management</span>
          </a>

          <!-- Staff Management -->
          <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.staff.index') }}">
              <div class="w-8 flex justify-center">
                  <i class="fas fa-chalkboard-teacher text-lg {{ request()->routeIs('admin.staff.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
              </div>
              <span class="text-sm font-medium">Staff Management</span>
          </a>

          <!-- Fee Management -->
          <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('admin.fees.index') }}">
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
      <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 px-4 pb-6 border-t border-slate-100">
          @csrf
          <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-sm transition-all duration-200 group border border-red-100">
              <div class="w-8 flex justify-center">
                  <i class="fas fa-sign-out-alt text-lg group-hover:scale-110 transition-transform"></i>
              </div>
              <span class="text-sm font-bold">Logout</span>
          </button>
      </form>
  </aside>

  <div class="flex-1 flex flex-col h-screen overflow-hidden">
    <!-- Mobile Header -->
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

    <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50">
         <div class="max-w-4xl mx-auto">
            <header class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold">Fee Summary</h1>
                    </div>
                    <a href="{{ route('admin.fees.index', ['tab' => 'tuition']) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                </div>
                <form method="GET" action="{{ route('admin.fees.summary') }}" class="mt-4 flex items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                        <select name="grade_level" class="w-48 px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($gradeLevels as $grade)
                                <option value="{{ $grade }}" {{ ($gradeLevel ?? '') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync mr-1"></i>Update
                    </button>
                </form>
            </header>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Context</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex items-center justify-between"><dt>Grade Level</dt><dd class="font-semibold">{{ $gradeLevel ?? 'N/A' }}</dd></div>
                        <div class="flex items-center justify-between"><dt>School Year</dt><dd class="font-semibold">{{ $schoolYear ?? 'N/A' }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Semester</dt><dd class="font-semibold">{{ $semester ?? 'N/A' }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Totals</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between"><dt>Base tuition</dt><dd class="font-semibold">₱{{ number_format($baseTuition, 2) }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Additional charges</dt><dd class="font-semibold">₱{{ number_format($additionalChargesTotal, 2) }}</dd></div>
                        <div class="flex items-center justify-between"><dt>Discounts</dt><dd class="font-semibold">-₱{{ number_format($discountsTotal, 2) }}</dd></div>
                        <div class="border-t border-dashed border-gray-200 pt-3 flex items-center justify-between"><dt>Net payable</dt><dd class="text-blue-600 font-bold">₱{{ number_format($totalAmount, 2) }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Tuition Fee</h2>
                    @if($tuitionFee)
                        <p class="text-sm">{{ $tuitionFee->grade_level }}</p>
                        <p class="text-sm">Amount: <span class="font-semibold">{{ $tuitionFee->formatted_amount }}</span></p>
                    @else
                        <p class="text-sm text-gray-600">No active tuition fee found for the selected grade.</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Additional Charges</h2>
                    @if($additionalCharges->count() > 0)
                        <ul class="divide-y divide-gray-200 text-sm">
                            @foreach($additionalCharges as $charge)
                                <li class="py-2 flex items-center justify-between">
                                    <span>{{ $charge->charge_name }}</span>
                                    <span class="font-semibold">{{ $charge->formatted_amount }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-600">No additional charges for this grade.</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold mb-4">Applied Discounts (Automatic)</h2>
                @if(($discountBreakdown ?? []) && count($discountBreakdown) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">Discount</th>
                                    <th class="py-2 pr-4">Scope</th>
                                    <th class="py-2 pr-4">Stackable</th>
                                    <th class="py-2 pr-4">Rule Value</th>
                                    <th class="py-2 pr-4">Applied</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($discountBreakdown as $row)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $row['name'] }}</td>
                                        <td class="py-2 pr-4 capitalize">
                                            {{ str_replace('_', ' ', $row['scope']) }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $row['stackable'] ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                                {{ $row['stackable'] ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="py-2 pr-4">
                                            @if($row['type'] === 'percentage')
                                                {{ number_format($row['value'], 2) }}%
                                            @else
                                                ₱{{ number_format($row['value'], 2) }}
                                            @endif
                                        </td>
                                        <td class="py-2 pr-4 font-semibold text-blue-600">-₱{{ number_format($row['applied_amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-600">No automatic discounts applicable.</p>
                @endif
            </div>
        </div>
    </main>
  </div>
</body>
</html>
