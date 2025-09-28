<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Student Profile</title>
  <link rel="preconnect" href="https://fonts.gstatic.com/">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <!-- styles removed: using Tailwind utility classes -->
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-200">
  <div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl md:text-3xl font-semibold text-orange-400">Student Profile</h1>
      <a href="{{ route('user_dashboard') }}" class="text-sm text-orange-400 hover:text-orange-300">&larr; Back to Dashboard</a>
    </div>

    @if(session('success'))
      <div class="mb-4 border border-green-600/40 text-green-300 bg-green-900/20 rounded-lg px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 border border-red-600/40 text-red-300 bg-red-900/20 rounded-lg px-4 py-3">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="bg-neutral-900 rounded-2xl shadow-lg border border-neutral-800 p-6">
      <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="flex items-center gap-4">
          <div class="w-20 h-20 rounded-full overflow-hidden bg-neutral-900 border border-neutral-700 flex items-center justify-center">
            @if(($student->profile_picture_url ?? null))
              <img src="{{ $student->profile_picture_url }}" alt="Profile Picture" class="w-full h-full object-cover"/>
            @else
              <span class="text-xl font-bold text-black bg-gradient-to-br from-orange-500 to-yellow-400 w-full h-full flex items-center justify-center">
                {{ strtoupper(substr($student->first_name ?? 'U',0,1) . substr($student->last_name ?? 'N',0,1)) }}
              </span>
            @endif
          </div>
          <div>
            <label for="profile_picture" class="block text-sm font-medium">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="mt-1 block w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" />
            <p class="text-xs text-neutral-400 mt-1">Accepted: JPG, PNG, WEBP. Max 2MB.</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium" for="student_id">Student ID</label>
          <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 px-3" type="text" id="student_id" name="student_id" value="{{ $student->student_id }}" readonly />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium" for="first_name">First Name</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required />
          </div>
          <div>
            <label class="block text-sm font-medium" for="middle_initial">Middle Initial</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="middle_initial" name="middle_initial" value="{{ old('middle_initial', $student->middle_initial) }}" maxlength="1" />
          </div>
          <div>
            <label class="block text-sm font-medium" for="last_name">Last Name</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium" for="email">Email</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
          </div>
          <div>
            <label class="block text-sm font-medium" for="contact_number">Contact Number</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium" for="sex">Sex</label>
            <select class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="sex" name="sex" required>
              <option value="">Select sex</option>
              <option value="Male" {{ old('sex', $student->sex) == 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex', $student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium" for="year">Year Level</label>
            <select class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" id="year" name="year" required>
              <option value="">Select year level</option>
              <option value="1st Year" {{ old('year', $student->year) == '1st Year' ? 'selected' : '' }}>1st Year</option>
              <option value="2nd Year" {{ old('year', $student->year) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
              <option value="3rd Year" {{ old('year', $student->year) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
              <option value="4th Year" {{ old('year', $student->year) == '4th Year' ? 'selected' : '' }}>4th Year</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium" for="section">Section</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="text" id="section" name="section" value="{{ old('section', $student->section) }}" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium" for="new_password">New Password</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current" />
          </div>
          <div>
            <label class="block text-sm font-medium" for="new_password_confirmation">Confirm New Password</label>
            <input class="w-full h-11 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 px-3" type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter new password" />
          </div>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ route('user_dashboard') }}" class="px-4 h-11 inline-flex items-center rounded-lg border border-neutral-700 text-neutral-200 hover:bg-neutral-800">Cancel</a>
          <button type="submit" class="px-4 h-11 inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold shadow-lg">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
  <script>
    // Preview profile picture on file select
    const fileInput = document.getElementById('profile_picture');
    const avatarContainer = document.querySelector('.w-20.h-20.rounded-full.overflow-hidden');
    if (fileInput && avatarContainer) {
      fileInput.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
          const img = avatarContainer.querySelector('img');
          if (img) {
            img.src = e.target.result;
          } else {
            avatarContainer.innerHTML = '<img alt="Profile Picture" class="w-full h-full object-cover"/>';
            avatarContainer.querySelector('img').src = e.target.result;
          }
        };
        reader.readAsDataURL(file);
      });
    }
  </script>
</body>
</html>
