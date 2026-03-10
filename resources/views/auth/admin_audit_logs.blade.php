<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Audit Logs - Efees Admin</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="flex flex-col md:flex-row h-screen overflow-hidden bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    @include('layouts.admin_sidebar')

    <main class="flex-1 overflow-y-auto h-screen custom-scrollbar" x-data="{ showDetailModal: false, selectedLog: null }">
        <div class="p-6 md:p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Logs / Audit Logs</h1>
                    <p class="text-gray-500 text-sm mt-1">Track all sensitive actions performed by Admin and Staff.</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.audit-logs.export') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="action_type" value="{{ request('action_type') }}">
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        <button type="submit" class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            <i class="fas fa-file-csv"></i>
                            Export CSV
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="User, Action, or Details..." class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                        <select name="action_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Actions</option>
                            @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action_type') == $act ? 'selected' : '' }}>{{ $act }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors h-10">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors h-10 flex items-center justify-center">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->user->email ?? 'System/Unknown' }}</div>
                                        <div class="text-xs text-gray-500 capitalize">{{ $log->user_role ?? 'System' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($log->model_type)
                                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded" title="{{ $log->model_type }}">
                                                {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="selectedLog = {{ json_encode($log) }}; showDetailModal = true" class="text-blue-600 hover:text-blue-900">View</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        No audit logs found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDetailModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-info text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Audit Log Details
                                </h3>
                                <div class="mt-4 space-y-3 text-sm text-gray-500">
                                    <div class="grid grid-cols-3 gap-2 border-b pb-2">
                                        <span class="font-medium text-gray-700">Action:</span>
                                        <span class="col-span-2" x-text="selectedLog?.action"></span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 border-b pb-2">
                                        <span class="font-medium text-gray-700">User:</span>
                                        <span class="col-span-2" x-text="(selectedLog?.user?.email || 'System') + ' (' + (selectedLog?.user_role || '') + ')'"></span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 border-b pb-2">
                                        <span class="font-medium text-gray-700">Date:</span>
                                        <span class="col-span-2" x-text="selectedLog ? new Date(selectedLog.created_at).toLocaleString() : ''"></span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 border-b pb-2">
                                        <span class="font-medium text-gray-700">Details:</span>
                                        <span class="col-span-2" x-text="selectedLog?.details || '-'"></span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 border-b pb-2">
                                        <span class="font-medium text-gray-700">IP Address:</span>
                                        <span class="col-span-2" x-text="selectedLog?.ip_address || '-'"></span>
                                    </div>
                                    
                                    <template x-if="selectedLog?.old_values || selectedLog?.new_values">
                                        <div class="mt-4">
                                            <h4 class="font-medium text-gray-900 mb-2">Changes</h4>
                                            <div class="bg-gray-50 p-3 rounded text-xs font-mono overflow-auto max-h-40">
                                                <div x-show="selectedLog?.old_values">
                                                    <strong>Old:</strong> <span x-text="JSON.stringify(selectedLog.old_values)"></span>
                                                </div>
                                                <div x-show="selectedLog?.new_values" class="mt-2">
                                                    <strong>New:</strong> <span x-text="JSON.stringify(selectedLog.new_values)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" @click="showDetailModal = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
