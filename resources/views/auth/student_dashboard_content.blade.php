<!-- Top Student Switcher -->
<div x-data="{ showLinkModal: false, switching: false }" class="mb-8">
    <!-- Loading Overlay -->
    <div x-show="switching" 
         class="fixed inset-0 bg-white/90 backdrop-blur-sm z-50 flex items-center justify-center"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="flex flex-col items-center">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="mt-4 text-gray-600 font-medium animate-pulse">Loading Student Profile...</p>
        </div>
    </div>

    <!-- Student Tabs -->
    <div class="flex items-center gap-4 overflow-x-auto pb-4 scrollbar-hide p-2">
        @foreach($myChildren as $child)
            <div class="relative group shrink-0">
                <a href="{{ route('parent.dashboard', ['student_id' => $child->student_id]) }}" 
                   @click="switching = true"
                   class="relative flex items-center gap-3 px-5 py-3 rounded-xl transition-all duration-300 min-w-[200px]
                   {{ isset($selectedChild) && $selectedChild->student_id == $child->student_id 
                      ? 'bg-gray-900 text-white shadow-lg ring-1 ring-black/5' 
                      : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 hover:border-gray-300 shadow-sm' }}">
                    
                    <div class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-sm transition-transform group-hover:scale-105
                        {{ isset($selectedChild) && $selectedChild->student_id == $child->student_id 
                            ? 'bg-blue-500 text-white' 
                            : 'bg-blue-50 text-blue-600' }}">
                        {{ substr($child->first_name, 0, 1) }}
                    </div>
                    
                    <div class="flex-1 min-w-0 text-left">
                        <p class="font-bold text-sm truncate">{{ $child->first_name }}</p>
                        <p class="text-xs truncate opacity-80">
                            {{ $child->level ?? 'Student' }}
                        </p>
                    </div>

                    @if(isset($selectedChild) && $selectedChild->student_id == $child->student_id)
                        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-12 h-1 bg-blue-500 rounded-full"></div>
                    @endif
                </a>

                <!-- Unlink Button -->
                <form action="{{ route('parent.unlink_student') }}" method="POST" class="absolute -top-2 -right-2 z-20" onsubmit="return confirm('Are you sure you want to remove {{ $child->first_name }} from your list?');">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $child->student_id }}">
                    <button type="submit" class="bg-white text-red-500 border border-red-100 rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-md hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100" title="Remove student">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
        @endforeach

        <!-- Add Student Button -->
        <button @click="showLinkModal = true" 
                class="flex items-center justify-center w-12 h-12 rounded-xl bg-white border border-dashed border-gray-300 text-gray-400 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm flex-shrink-0 group"
                title="Link another student">
            <i class="fas fa-plus text-lg group-hover:rotate-90 transition-transform duration-300"></i>
        </button>
    </div>

    <!-- Link Student Modal (Preserved) -->
    <div x-show="showLinkModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showLinkModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <div class="bg-white p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Link Student</h3>
                        <p class="text-sm text-gray-500 mt-2">Enter the unique Student ID provided by the school.</p>
                    </div>
                    
                    <form action="{{ route('parent.link_student') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="student_id" class="sr-only">Student ID</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input type="text" name="student_id" id="student_id" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors sm:text-sm"
                                       placeholder="Student ID (e.g. 2023-0001)">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="showLinkModal = false" class="w-full justify-center rounded-xl bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="w-full justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
                                Link Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
@if(isset($selectedChild) && $selectedChild)
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- Left Column (Overview & History) -->
    <div class="xl:col-span-2 space-y-8">
        
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl shadow-blue-200">
            <!-- Decorative Patterns -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-black/10 blur-3xl"></div>
            
            <div class="relative z-10 grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="flex items-center gap-2 text-blue-100 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">Current Balance</span>
                    </div>
                    <h1 class="text-5xl font-extrabold tracking-tight mb-2">₱{{ number_format($balanceDue, 2) }}</h1>
                    
                    <div class="flex items-center gap-4 text-sm text-blue-100/90 mt-4">
                        <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Due: {{ $upcomingFees->first() ? (optional($upcomingFees->first()->payment_date)?->format('M d, Y') ?? 'N/A') : 'None' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('parent.pay', ['student_id' => $selectedChild->student_id, 'pay_full' => 1]) }}" 
                       class="w-full bg-white text-blue-600 font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-50 transition-all text-center flex items-center justify-center gap-2 group">
                        <span>Pay Now</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <div class="flex gap-3">
                        <a href="{{ route('parent.history', ['student_id' => $selectedChild->student_id]) }}" class="flex-1 bg-blue-800/40 hover:bg-blue-800/60 text-white py-3 px-4 rounded-xl text-sm font-medium backdrop-blur-sm transition-colors border border-white/10 text-center">
                            View History
                        </a>
                        <a href="{{ route('parent.fees.show', $selectedChild->student_id) }}" class="flex-1 bg-blue-800/40 hover:bg-blue-800/60 text-white py-3 px-4 rounded-xl text-sm font-medium backdrop-blur-sm transition-colors border border-white/10 text-center">
                            Fee Structure
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Download Invoice -->
            <button class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col items-center justify-center gap-2 text-center group hover:-translate-y-1">
                <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-600 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                    <i class="fas fa-file-invoice text-lg"></i>
                </div>
                <span class="text-xs font-bold text-gray-600">Invoice</span>
            </button>
            
            <!-- Payment Methods -->
            <button class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col items-center justify-center gap-2 text-center group hover:-translate-y-1">
                <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-600 group-hover:bg-purple-50 group-hover:text-purple-600 transition-colors">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <span class="text-xs font-bold text-gray-600">Methods</span>
            </button>

            <!-- Support -->
            <button class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col items-center justify-center gap-2 text-center group hover:-translate-y-1">
                <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-600 group-hover:bg-green-50 group-hover:text-green-600 transition-colors">
                    <i class="fas fa-headset text-lg"></i>
                </div>
                <span class="text-xs font-bold text-gray-600">Support</span>
            </button>

            <!-- Settings -->
            <a href="{{ route('parent.profile.edit') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col items-center justify-center gap-2 text-center group hover:-translate-y-1">
                <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-600 group-hover:bg-orange-50 group-hover:text-orange-600 transition-colors">
                    <i class="fas fa-cog text-lg"></i>
                </div>
                <span class="text-xs font-bold text-gray-600">Settings</span>
            </a>
        </div>
        
        <!-- Recent Transactions -->
        <div id="transactions" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Recent Transactions</h3>
                <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-700">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($transactions as $transaction)
                    <div class="p-5 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-green-50 border border-green-100 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $transaction->remarks ?? 'Tuition/Fee Payment' }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($transaction->paid_at)->format('F d, Y • h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">₱{{ number_format((float) $transaction->amount_paid, 2) }}</p>
                            <div class="flex items-center justify-end gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Successful
                                </span>
                                <a href="{{ route('parent.receipts.download', $transaction->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors" title="Download Receipt">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-receipt text-gray-300 text-xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No recent transactions found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column (Notifications & Upcoming) -->
    <div class="space-y-6">
        
        <!-- Notifications Card -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden relative group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-50 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="p-6 border-b border-gray-50 relative z-10 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-bell text-blue-500"></i> Notifications
                </h3>
                @if(count($notifications) > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm shadow-red-200">{{ count($notifications) }}</span>
                @endif
            </div>

            <div class="p-4 space-y-2 relative z-10 max-h-[350px] overflow-y-auto custom-scrollbar">
                @forelse($notifications as $notification)
                    <div class="flex gap-3 p-3 rounded-2xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 rounded-full bg-blue-500 ring-4 ring-blue-50"></div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800 font-medium leading-relaxed">{{ $notification->data ?? 'New notification' }}</p>
                            <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wide">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-400 italic">You're all caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Fees -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-900">Upcoming Due</h3>
                <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                @forelse($upcomingFees->take(3) as $fee)
                    @php
                        $isOverdue = $fee['due_date'] && \Carbon\Carbon::parse($fee['due_date'])->isPast();
                        $dateColor = $isOverdue ? 'text-red-600' : 'text-gray-500';
                        $iconColor = $isOverdue ? 'text-red-500' : 'text-orange-500';
                        $bgColor = $isOverdue ? 'bg-red-50' : 'bg-orange-50';
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-2xl border border-gray-50 hover:border-gray-200 hover:shadow-sm transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $bgColor }} flex items-center justify-center {{ $iconColor }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ Str::limit($fee['notes'], 15) }}</p>
                                <p class="text-xs {{ $dateColor }} font-medium">
                                    {{ $fee['due_date'] ? \Carbon\Carbon::parse($fee['due_date'])->format('M d') : 'N/A' }}
                                    @if($isOverdue) <span class="text-[10px] font-bold bg-red-100 text-red-600 px-1 rounded ml-1">LATE</span> @endif
                                </p>
                            </div>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">₱{{ number_format($fee['balance'], 2) }}</p>
                    </div>
                @empty
                    <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-2xl">
                        <p class="text-sm text-gray-500">No upcoming fees.</p>
                    </div>
                @endforelse
            </div>
            
            @if($upcomingFees->count() > 3)
                <button class="w-full mt-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
                    View All {{ $upcomingFees->count() }} Fees
                </button>
            @endif
        </div>
    </div>
</div>
@else
<!-- Empty State (Redesigned) -->
<div class="min-h-[500px] flex flex-col items-center justify-center text-center p-8 bg-white rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
    <div class="absolute inset-0 bg-grid-slate-50 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] bg-center"></div>
    
    <div class="relative z-10 max-w-md mx-auto">
        <div class="w-24 h-24 bg-gradient-to-tr from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner relative">
            <div class="absolute inset-0 rounded-full animate-ping bg-blue-50 opacity-50"></div>
            <i class="fas fa-child text-4xl text-blue-600"></i>
        </div>
        
        <h2 class="text-2xl font-extrabold text-gray-900 mb-3">Select a Student Profile</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Please select one of your linked children from the navigation bar above to view their dashboard, manage fees, and track progress.
        </p>
        
        <div class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-full animate-bounce">
            <i class="fas fa-arrow-up"></i>
            <span>Select from above</span>
        </div>
    </div>
</div>
@endif
