<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $parent ? 'Edit Parent' : 'Add Parent' }} - Efees Admin</title>
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;family=Noto+Sans:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
      body { font-family: 'Inter', 'Noto Sans', sans-serif; }
      [x-cloak] { display: none !important; }
      
      /* Custom Scrollbar */
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
  @include('layouts.admin_sidebar')

  <div class="flex-1 flex flex-col h-screen overflow-hidden">
    <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shrink-0">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white">
          <i class="fas fa-user-shield text-lg"></i>
        </div>
        <span class="font-bold text-lg text-blue-900">Efees</span>
      </div>
      <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700">
        <i class="fas fa-bars text-xl"></i>
      </button>
    </div>

    <main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8 custom-scrollbar">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">{{ $parent ? 'Edit Parent' : 'Add Parent' }}</h1>
        <a href="{{ route('admin.parents.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm hover:text-blue-600 hover:shadow-md">
          <i class="fas fa-arrow-left"></i>
          Back to Parents
        </a>
      </div>

      <form method="POST" action="{{ $parent ? route('admin.parents.update', $parent) : route('admin.parents.store') }}" class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 space-y-4" x-data="{ firstName: '{{ old('first_name', $parent->first_name ?? '') }}', middleName: '{{ old('middle_name', $parent->middle_name ?? '') }}', lastName: '{{ old('last_name', $parent->last_name ?? '') }}', rel: '{{ old('relationship', $parent->relationship ?? '') }}' }">
        @csrf
        @if($parent) @method('put') @endif
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">Parent ID</label>
            <input type="text" name="parent_id" value="{{ $parent->parent_id ?? 'Will be generated upon save' }}" class="w-full rounded-xl border-slate-200 bg-slate-100 text-slate-500 px-4 py-2.5 focus:outline-none" disabled/>
          </div>
          <div class="md:col-span-2">
            <input type="hidden" name="full_name" :value="`${firstName} ${middleName ? middleName + ' ' : ''}${lastName}`.trim()">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">First Name</label>
                <input type="text" name="first_name" x-model="firstName" value="{{ old('first_name', $parent->first_name ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required/>
              </div>
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Middle Name</label>
                <input type="text" name="middle_name" x-model="middleName" value="{{ old('middle_name', $parent->middle_name ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
              </div>
              <div>
                <label class="block text-sm font-semibold mb-2 text-slate-700">Last Name</label>
                <input type="text" name="last_name" x-model="lastName" value="{{ old('last_name', $parent->last_name ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required/>
              </div>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">Relationship to Student</label>
            <select name="relationship" x-model="rel" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" required>
              @foreach(['Mother','Father','Guardian','Other'] as $opt)
              <option value="{{ $opt }}" {{ old('relationship', $parent->relationship ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
          <div class="md:col-span-2" x-show="rel === 'Other'" x-cloak>
            <label class="block text-sm font-semibold mb-2 text-slate-700">Specify Relationship</label>
            <input type="text" name="relationship_other" value="{{ old('relationship_other', $parent->relationship_other ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">Primary Mobile Number (SMS-enabled)</label>
            <input type="text" name="phone" value="{{ old('phone', $parent->phone ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="09xxxxxxxxx"/>
          </div>
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">Secondary Mobile Number (optional)</label>
            <input type="text" name="phone_secondary" value="{{ old('phone_secondary', $parent->phone_secondary ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Optional"/>
          </div>
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $parent->email ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
          </div>
        </div>

        <div class="mt-2">
          <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Home Address</h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-4">
              <label class="block text-sm font-semibold mb-2 text-slate-700">Street</label>
              <input type="text" name="address_street" value="{{ old('address_street', $parent->address_street ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="House No., Street Name"/>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2 text-slate-700">Barangay</label>
              <input type="text" name="address_barangay" value="{{ old('address_barangay', $parent->address_barangay ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2 text-slate-700">City/Municipality</label>
              <input type="text" name="address_city" value="{{ old('address_city', $parent->address_city ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2 text-slate-700">Province</label>
              <input type="text" name="address_province" value="{{ old('address_province', $parent->address_province ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-2 text-slate-700">ZIP Code</label>
              <input type="text" name="address_zip" value="{{ old('address_zip', $parent->address_zip ?? '') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"/>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">
              {{ $parent ? 'New Password' : 'Password' }}
              @if($parent)<span class="text-slate-400 font-normal">(Leave blank to keep current)</span>@endif
            </label>
            <input name="password" type="password" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" @if(!$parent) required @endif />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-2 text-slate-700">
              {{ $parent ? 'Confirm New Password' : 'Confirm Password' }}
            </label>
            <input name="password_confirmation" type="password" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" @if(!$parent) required @endif />
          </div>
        </div>

        

        <div class="flex items-center gap-3">
          <button class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-sm">{{ $parent ? 'Update' : 'Create' }}</button>
          <a href="{{ route('admin.parents.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold rounded-xl transition-all duration-200 shadow-sm">Cancel</a>
        </div>
      </form>

      @if($parent)
      <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 space-y-4 mt-8">
        <h2 class="text-lg font-semibold">Link to Student</h2>
        <form method="POST" action="{{ route('admin.parents.link', $parent) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
          @csrf
          <input type="text" name="student_id" class="rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm md:col-span-2" placeholder="Student ID" required/>
          <select name="relationship" class="rounded-xl border-slate-200 bg-slate-50 text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
            @foreach(['Father','Mother','Guardian','Grandparent','Other'] as $rel)
            <option value="{{ $rel }}">{{ $rel }}</option>
            @endforeach
          </select>
          <label class="inline-flex items-center gap-2 text-slate-700">
            <input type="checkbox" name="is_primary" value="1" class="rounded"/> Primary
          </label>
          <button class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-sm md:col-span-4">Link</button>
        </form>

        <div>
          <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Linked Students</h3>
          <ul class="divide-y divide-slate-100">
            @foreach($parent->students as $s)
            <li class="py-2 flex items-center justify-between">
              <div>{{ $s->full_name }} ({{ $s->student_id }}) — {{ $s->pivot->relationship }}@if($s->pivot->is_primary) • primary @endif</div>
              <form method="POST" action="{{ route('admin.parents.unlink', $parent) }}">
                @csrf @method('delete')
                <input type="hidden" name="student_id" value="{{ $s->student_id }}"/>
                <button class="text-red-600 font-semibold">Unlink</button>
              </form>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
      @endif
    </main>
  </div>
</body>
</html>
