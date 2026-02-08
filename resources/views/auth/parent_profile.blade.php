@extends('auth.user_dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
        <p class="text-gray-600 mt-1">Manage your contact information and account security.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <i class="fas fa-exclamation-circle"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-3xl mx-auto mb-4 border-4 border-white shadow-lg">
                    {{ substr($parent->full_name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $parent->full_name }}</h2>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mt-1">Parent Account</p>
                
                <div class="mt-6 pt-6 border-t border-gray-100 text-left space-y-3">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <span class="font-mono text-xs">{{ $user->email }}</span> <!-- Using email as username display -->
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                            <i class="fas fa-child"></i>
                        </div>
                        <span>{{ $parent->students()->count() }} Linked Students</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Edit Information</h3>
                </div>
                
                <form action="{{ route('parent.profile.update') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone -->
                        <div class="col-span-1">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $parent->phone) }}" required
                                       class="block w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-span-1">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email', $parent->email) }}"
                                       class="block w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-span-2">
                            <label for="address_street" class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 text-gray-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <textarea name="address_street" id="address_street" rows="2"
                                          class="block w-full pl-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">{{ old('address_street', $parent->address_street) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-bell text-blue-500"></i> Notification Preferences
                        </h4>
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer group">
                                <input type="checkbox" name="sms_reminders" value="1" {{ old('sms_reminders', $preferences->sms_reminders ?? false) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">SMS Reminders</div>
                                    <div class="text-xs text-gray-500">Receive SMS for due dates and important announcements.</div>
                                </div>
                                <i class="fas fa-sms text-gray-300 group-hover:text-blue-400 transition-colors"></i>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer group">
                                <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $preferences->email_notifications ?? false) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Email Notifications</div>
                                    <div class="text-xs text-gray-500">Receive digital receipts and monthly statements via email.</div>
                                </div>
                                <i class="fas fa-envelope-open-text text-gray-300 group-hover:text-blue-400 transition-colors"></i>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer group">
                                <input type="checkbox" name="payment_reminders" value="1" {{ old('payment_reminders', $preferences->payment_reminders ?? false) ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Payment Confirmations</div>
                                    <div class="text-xs text-gray-500">Get notified instantly when a payment is processed.</div>
                                </div>
                                <i class="fas fa-check-circle text-gray-300 group-hover:text-blue-400 transition-colors"></i>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-lock text-gray-400"></i> Change Password
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" name="password" id="password" autocomplete="new-password"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"
                                       placeholder="Leave blank to keep current">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="reset" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
