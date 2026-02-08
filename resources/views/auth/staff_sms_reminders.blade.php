<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>SMS Reminders - Staff Panel</title>
    <!-- Fonts and CSS -->
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    
    <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
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
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 md:hidden" x-cloak></div>
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 overflow-y-auto shadow-2xl md:shadow-none">
        <div class="flex items-center justify-between gap-3 px-8 py-6 border-b border-gray-200 bg-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <h1 class="text-blue-900 font-extrabold text-xl tracking-tight select-none">Efees Staff</h1>
                    <p class="text-xs text-slate-500 font-medium">Staff Panel</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex flex-col mt-6 px-4 space-y-1.5 flex-grow pb-6">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff_dashboard') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff_dashboard') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('staff_dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Student Records</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.payment_processing') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.payment_processing') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-credit-card text-lg {{ request()->routeIs('staff.payment_processing') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Payment Processing</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.sms_reminders') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.sms_reminders') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-sms text-lg {{ request()->routeIs('staff.sms_reminders') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">SMS Reminders</span>
            </a>
            <a class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('staff.reports') ? 'bg-blue-50 text-blue-700 font-bold shadow-sm ring-1 ring-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:shadow-sm' }}" href="{{ route('staff.reports') }}">
                <div class="w-8 flex justify-center">
                    <i class="fas fa-chart-line text-lg {{ request()->routeIs('staff.reports') ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500 transition-colors' }}"></i>
                </div>
                <span class="text-sm font-medium">Reports</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 bg-blue-600 text-white font-bold text-sm rounded-lg h-10 justify-center cursor-pointer transition-colors duration-300 hover:bg-blue-700" type="submit">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-8 overflow-y-auto" x-data="{ 
        activeTab: 'send', 
        selectedStudents: [], 
        selectAll: false,
        toggleAll() {
            this.selectAll = !this.selectAll;
            this.selectedStudents = this.selectAll ? {{ json_encode($students->pluck('student_id')) }} : [];
        }
    }">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">SMS Reminders</h1>
                <p class="text-gray-500 mt-1">Send payment reminders to students and guardians.</p>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'send'" :class="activeTab === 'send' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Send Reminders
                    </button>
                    <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Sent History
                    </button>
                </nav>
            </div>

            <!-- Send Reminders Tab -->
            <div x-show="activeTab === 'send'" class="space-y-6">
                <!-- Filters -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-4 flex gap-4">
                            <div class="flex-1 relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search students..." class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg hover:bg-gray-800 font-medium">Search</button>
                        </div>
                        
                        <!-- Filter Dropdowns -->
                        <div>
                            <select name="level" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                                <option value="">All Levels</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl }}" {{ (isset($level) && $level == $lvl) ? 'selected' : '' }}>{{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="section" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}" {{ (isset($section) && $section == $sec) ? 'selected' : '' }}>{{ $sec }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="strand" class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                                <option value="">All Strands</option>
                                @foreach($strands as $str)
                                    <option value="{{ $str }}" {{ (isset($strand) && $strand == $str) ? 'selected' : '' }}>{{ $str }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center">
                            <a href="{{ route('staff.sms_reminders') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Clear Filters</a>
                        </div>
                    </form>
                </div>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Wrap -->
                <form method="POST" action="{{ route('staff.sms_reminders.send') }}" onsubmit="return confirm('Send SMS to selected recipients? This may incur costs.')">
                    @csrf
                    
                    <!-- Student Table -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 w-4">
                                        <input type="checkbox" @click="toggleAll()" :checked="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-3">Student Name</th>
                                    <th class="px-6 py-3">Student ID</th>
                                    <th class="px-6 py-3">Grade/Section</th>
                                    <th class="px-6 py-3">Strand</th>
                                    <th class="px-6 py-3">Total Balance</th>
                                    <th class="px-6 py-3">Mobile Number</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="selected_students[]" value="{{ $student->student_id }}" x-model="selectedStudents" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $student->student_id }}</td>
                                    <td class="px-6 py-4 text-gray-500">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900">{{ $student->level ?? 'N/A' }}</span>
                                            <span class="text-xs">{{ $student->section ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $student->strand ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 font-bold text-red-600">₱{{ number_format($student->feeRecords->sum('balance'), 2) }}</td>
                                    @php
                                        $mobileNumber = 'N/A';
                                        if ($student->parents && $student->parents->isNotEmpty()) {
                                            $primaryParent = $student->parents->firstWhere('pivot.is_primary', true) ?? $student->parents->first();
                                            if ($primaryParent) {
                                                $mobileNumber = $primaryParent->phone;
                                            }
                                        }
                                    @endphp
                                    <td class="px-6 py-4 text-gray-500">{{ $mobileNumber }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No students found with pending balances.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @if($students->hasPages())
                        <div class="p-4 border-t border-gray-200">
                            {{ $students->appends(['history_page' => request('history_page')])->links() }}
                        </div>
                        @endif
                    </div>

                    <!-- Action Bar -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm sticky bottom-6 z-10" x-show="selectedStudents.length > 0" x-transition.opacity>
                        <div class="flex flex-col md:flex-row gap-6 items-end">
                            <div class="flex-1 w-full" x-data="{ content: '', edited: '', count: 0 }" x-init="$watch('edited', v => count = v.length)">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Template</label>
                                <select name="template_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                                    @change="
                                        const opt = $event.target.selectedOptions[0];
                                        content = opt?.dataset?.content || '';
                                        edited = content;
                                    ">
                                    <option value="">-- Choose a Message Template --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" data-content="{{ $template->content }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (Editable)</label>
                                        <textarea x-model="edited" name="custom_message" rows="4" maxlength="200" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                        <div class="mt-1 text-xs text-gray-500">Characters: <span x-text="count"></span>/200</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                                        <div class="p-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700">
                                            <p x-text="edited || 'Select a template to preview.'"></p>
                                            <p class="mt-2 text-xs text-gray-500">Placeholders will auto-fill per student at send time.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-64">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule (Optional)</label>
                                <input type="datetime-local" name="scheduled_at" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 font-medium shadow-sm transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>Send to <span x-text="selectedStudents.length"></span> Student(s)</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- History Tab -->
            <div x-show="activeTab === 'history'" class="space-y-6" style="display: none;">
                <div class="flex justify-end">
                    <form action="{{ route('staff.sms_reminders.refresh') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i> Refresh Delivery Status
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3">Recipient</th>
                                <th class="px-6 py-3">Message Preview</th>
                                <th class="px-6 py-3">Date/Time</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($history as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $log->student->first_name }} {{ $log->student->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->mobile_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="truncate max-w-xs text-gray-600" title="{{ $log->message }}">{{ Str::limit($log->message, 60) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->scheduled_at && $log->status === 'queued')
                                        <span class="text-yellow-600 text-xs font-medium"><i class="fas fa-clock mr-1"></i> Scheduled: {{ $log->scheduled_at->format('M d, H:i') }}</span>
                                    @else
                                        <div class="text-gray-900">{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i') : '-' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                     @php
                                        $statusConfig = [
                                            'sent' => ['color' => 'text-green-600', 'bg' => 'bg-green-50'],
                                            'delivered' => ['color' => 'text-green-600', 'bg' => 'bg-green-50'],
                                            'failed' => ['color' => 'text-red-600', 'bg' => 'bg-red-50'],
                                            'queued' => ['color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                                            'cancelled' => ['color' => 'text-gray-600', 'bg' => 'bg-gray-50'],
                                        ];
                                        $config = $statusConfig[$log->status] ?? ['color' => 'text-gray-600', 'bg' => 'bg-gray-50'];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['color'] }} capitalize">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($log->status === 'queued' && $log->scheduled_at > now())
                                        <form action="{{ route('staff.sms_reminders.schedule.cancel', $log->id) }}" method="POST" onsubmit="return confirm('Cancel this scheduled SMS?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No history found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    @if($history->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $history->appends(['students_page' => request('students_page')])->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
