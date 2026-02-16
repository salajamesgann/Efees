<div class="bg-white rounded-xl shadow border border-gray-200 p-4" x-data="{ groupType: 'grade' }">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-900">Assign Discount to Group</h3>
  </div>
  <form method="POST" action="{{ route('admin.fees.assign-discount-group') }}" class="space-y-4">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
        <select name="discount_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
          @foreach(($discounts ?? collect()) as $discount)
            <option value="{{ $discount->id }}">{{ $discount->discount_name }} ({{ $discount->type === 'percentage' ? $discount->value.'%' : '₱'.number_format($discount->value, 2) }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Group Type</label>
        <select name="group_type" x-model="groupType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
          <option value="grade">Grade Level</option>
          <option value="section">Section</option>
          <option value="strand">Strand</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Group Value</label>
        <div x-show="groupType === 'grade'">
          <select name="group_value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :disabled="groupType !== 'grade'">
            @foreach(($gradeLevels ?? []) as $gl)
              <option value="{{ $gl }}">{{ $gl }}</option>
            @endforeach
          </select>
        </div>
        <div x-show="groupType !== 'grade'" x-cloak>
          <input type="text" name="group_value" placeholder="Enter section or strand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" :disabled="groupType === 'grade'">
        </div>
      </div>
    </div>
    <div class="pt-2">
      <button type="submit" class="inline-flex items-center h-10 px-4 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors">
        <i class="fas fa-users mr-2"></i> Assign to Group
      </button>
    </div>
  </form>
</div>
<div class="bg-white rounded-xl shadow border border-gray-200 p-4 mt-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-900">Discount Rules</h3>
    <a href="{{ route('admin.fees.create-discount') }}" class="inline-flex items-center h-9 px-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors">
      <i class="fas fa-plus mr-2"></i> Add Discount
    </a>
  </div>
  @if(session('success'))
    <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-2 text-sm">
        {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-2 text-sm">
        {{ session('error') }}
    </div>
  @endif
  <div class="overflow-x-auto max-h-[60vh] overflow-y-auto custom-scrollbar">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Name</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Type</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Value</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Scope</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Auto</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Stackable</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Priority</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Active</th>
          <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Actions</th>
        </tr>
      </thead>
      <tbody id="discounts-table-body" class="divide-y divide-gray-200 bg-white">
        @forelse(($discounts ?? collect()) as $discount)
          <tr class="hover:bg-gray-50">
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->discount_name }}</td>
            <td class="px-3 py-2 text-sm text-gray-600">{{ ucfirst($discount->type) }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">
              @if($discount->type === 'percentage')
                {{ $discount->value }}%
              @else
                ₱{{ number_format($discount->value, 2) }}
              @endif
            </td>
            <td class="px-3 py-2 text-sm text-gray-600 capitalize">{{ str_replace('_',' ', $discount->getApplyScope()) }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->is_automatic ? 'Yes' : 'No' }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->isStackable() ? 'Yes' : 'No' }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->priority }}</td>
            <td class="px-3 py-2 text-sm text-gray-900">{{ $discount->is_active ? 'Yes' : 'No' }}</td>
            <td class="px-3 py-2">
              <a href="{{ route('admin.fees.edit-discount', $discount) }}" class="inline-flex items-center h-8 px-2 rounded-lg bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600">
                <i class="fas fa-edit mr-1"></i> Edit
              </a>
              <form method="POST" action="{{ route('admin.fees.destroy-discount', $discount) }}" class="inline-block ml-1" onsubmit="return confirm('Are you sure you want to delete this discount?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center h-8 px-2 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600">
                  <i class="fas fa-trash mr-1"></i> Delete
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="px-4 py-6 text-center text-gray-600 text-sm">No discounts found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
