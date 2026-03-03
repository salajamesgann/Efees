<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Link Approvals | Efees Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="flex flex-col md:flex-row min-h-screen bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
  <!-- Mobile Sidebar Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" x-cloak></div>

  <!-- Sidebar -->
  @include('layouts.admin_sidebar')

  <!-- Main Content -->
  <main class="flex-1 overflow-x-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Mobile menu button -->
      <div class="md:hidden mb-4">
        <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
              <i class="fas fa-link text-blue-600"></i>
            </div>
            Student Link Approvals
          </h1>
          <p class="text-sm text-gray-500 mt-1">Review and approve parent-student link/unlink requests</p>
        </div>
        @if($pendingCount > 0)
        <span class="mt-3 sm:mt-0 inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-4 py-1.5 text-sm font-semibold">
          <i class="fas fa-clock"></i> {{ $pendingCount }} Pending
        </span>
        @endif
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or student ID..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
          </div>
          <select name="status" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
          </select>
          <select name="type" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
            <option value="">All Types</option>
            <option value="link" {{ request('type') === 'link' ? 'selected' : '' }}>Link</option>
            <option value="unlink" {{ request('type') === 'unlink' ? 'selected' : '' }}>Unlink</option>
          </select>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
            <i class="fas fa-filter mr-1"></i> Filter
          </button>
        </form>
      </div>

      <!-- Flash Messages -->
      @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
          <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
          <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
      @endif

      <!-- Requests Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($requests->isEmpty())
          <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-check-circle text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-1">No Requests</h3>
            <p class="text-sm text-gray-400">There are no {{ $status !== 'all' ? $status : '' }} link requests to display.</p>
          </div>
        @else
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Parent</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Relationship</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reason</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Submitted</th>
                  <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($requests as $linkReq)
                <tr class="hover:bg-gray-50 transition-colors">
                  <!-- Type Badge -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    @if($linkReq->type === 'link')
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fas fa-link text-[10px]"></i> Link
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">
                        <i class="fas fa-unlink text-[10px]"></i> Unlink
                      </span>
                    @endif
                  </td>

                  <!-- Parent -->
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $linkReq->parent->full_name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ $linkReq->parent->email ?? '' }}</div>
                  </td>

                  <!-- Student -->
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $linkReq->student ? $linkReq->student->full_name : 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ $linkReq->student_id }}</div>
                  </td>

                  <!-- Relationship -->
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $linkReq->relationship ?: '—' }}</td>

                  <!-- Reason -->
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-[200px] truncate" title="{{ $linkReq->reason }}">{{ $linkReq->reason ?: '—' }}</p>
                    @if($linkReq->admin_remarks)
                      <p class="text-xs text-red-500 mt-0.5 truncate" title="{{ $linkReq->admin_remarks }}">Admin: {{ $linkReq->admin_remarks }}</p>
                    @endif
                  </td>

                  <!-- Status -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    @if($linkReq->status === 'pending')
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <i class="fas fa-clock text-[10px]"></i> Pending
                      </span>
                    @elseif($linkReq->status === 'approved')
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                        <i class="fas fa-check text-[10px]"></i> Approved
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                        <i class="fas fa-times text-[10px]"></i> Rejected
                      </span>
                    @endif
                  </td>

                  <!-- Date -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">{{ $linkReq->created_at->format('M d, Y') }}</div>
                    <div class="text-xs text-gray-400">{{ $linkReq->created_at->format('h:i A') }}</div>
                    @if($linkReq->reviewer)
                      <div class="text-xs text-gray-400 mt-0.5">by {{ $linkReq->reviewer->email }}</div>
                    @endif
                  </td>

                  <!-- Actions -->
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($linkReq->status === 'pending')
                      <div class="flex items-center justify-center gap-2">
                        <form action="{{ route('admin.link_approvals.approve', $linkReq->id) }}" method="POST" onsubmit="return confirm('Approve this {{ $linkReq->type }} request?');">
                          @csrf
                          <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-xs font-bold transition-colors border border-green-200">
                            <i class="fas fa-check mr-1"></i> Approve
                          </button>
                        </form>
                        <button @click="$dispatch('open-reject-modal', { id: '{{ $linkReq->id }}', type: '{{ $linkReq->type }}' })" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition-colors border border-red-200">
                          <i class="fas fa-times mr-1"></i> Reject
                        </button>
                      </div>
                    @else
                      <span class="text-xs text-gray-400">Processed</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-100">
            {{ $requests->links() }}
          </div>
        @endif
      </div>
    </div>
  </main>

  <!-- Reject Modal -->
  <div x-data="{ open: false, requestId: null, requestType: null }" @open-reject-modal.window="open = true; requestId = $event.detail.id; requestType = $event.detail.type" class="relative z-50" x-show="open" x-cloak>
    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="open = false"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
          <form :action="`{{ url('admin/link-approvals') }}/${requestId}/reject`" method="POST">
            @csrf
            <div class="bg-white px-6 pt-6 pb-4">
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                  <h3 class="text-base font-semibold text-gray-900">Reject <span x-text="requestType"></span> Request</h3>
                  <p class="text-sm text-gray-500 mt-1">The parent will be notified of the rejection.</p>
                  <div class="mt-4">
                    <label for="admin_remarks" class="block text-sm font-medium text-gray-700">Reason (optional)</label>
                    <textarea name="admin_remarks" id="admin_remarks" rows="3" class="mt-1 w-full border border-gray-300 rounded-lg text-sm p-3 focus:ring-red-500 focus:border-red-500" placeholder="e.g. Unable to verify parent-student relationship..."></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-3">
              <button type="submit" class="inline-flex justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Reject Request</button>
              <button type="button" @click="open = false" class="inline-flex justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
