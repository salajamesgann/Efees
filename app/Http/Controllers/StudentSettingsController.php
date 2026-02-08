<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class StudentSettingsController extends Controller
{
    /**
     * Show the authenticated student's settings page.
     */
    public function show(Request $request): View
    {
        $user = Auth::user();

        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $student = $user->roleable; // Morph relation to Student

        // Get notifications for the user
        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get user's communication preferences (if they exist)
        $preferences = DB::table('user_preferences')
            ->where('user_id', $user->user_id)
            ->first();

        // Get linked payment methods (if they exist)
        $paymentMethods = DB::table('payment_methods')
            ->where('user_id', $user->user_id)
            ->where('is_active', true)
            ->get();

        return view('auth.student_settings', [
            'user' => $user,
            'student' => $student,
            'notifications' => $notifications,
            'preferences' => $preferences,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Update the authenticated student's personal information.
     */
    public function updatePersonalInfo(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $student = $user->roleable;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->user_id.',user_id'],
            'address' => ['nullable', 'string', 'max:500'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'year' => ['required', 'string', 'in:1st Year,2nd Year,3rd Year,4th Year'],
            'section' => ['required', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
        ]);

        // Update student fields
        $student->first_name = $validated['first_name'];
        $student->middle_initial = $validated['middle_initial'] ?? null;
        $student->last_name = $validated['last_name'];
        $student->address = $validated['address'] ?? null;
        $student->sex = $validated['sex'];
        $student->year = $validated['year'];
        $student->section = $validated['section'];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile_pictures', 'public');
            $student->profile_picture_url = Storage::url($path);
        }

        $student->save();

        // Update user's email
        $user->email = strtolower($validated['email']);
        $user->save();

        return redirect()->route('student.settings')->with('success', 'Personal information updated successfully.');
    }

    /**
     * Update the authenticated student's account settings.
     */
    public function updateAccountSettings(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,email,'.$user->user_id.',user_id'],
            'current_password' => ['required', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update username (email)
        $user->email = strtolower($validated['username']);

        // Update password if provided
        if (! empty($validated['new_password'] ?? '')) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('student.settings')->with('success', 'Account settings updated successfully.');
    }

    /**
     * Update the authenticated student's communication preferences.
     */
    public function updateCommunicationPreferences(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $validated = $request->validate([
            'sms_reminders' => ['boolean'],
            'email_notifications' => ['boolean'],
            'payment_reminders' => ['boolean'],
            'system_updates' => ['boolean'],
        ]);

        // Update or insert preferences
        DB::table('user_preferences')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'sms_reminders' => $validated['sms_reminders'] ?? false,
                'email_notifications' => $validated['email_notifications'] ?? false,
                'payment_reminders' => $validated['payment_reminders'] ?? false,
                'system_updates' => $validated['system_updates'] ?? false,
                'updated_at' => now(),
            ]
        );

        return redirect()->route('student.settings')->with('success', 'Communication preferences updated successfully.');
    }

    /**
     * Add a new payment method for the authenticated student.
     */
    public function addPaymentMethod(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $validated = $request->validate([
            'payment_type' => ['required', 'string', 'in:credit_card,debit_card,bank_account,paypal'],
            'card_number' => ['nullable', 'string', 'regex:/^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/'],
            'expiry_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'expiry_year' => ['nullable', 'integer', 'min:'.date('Y'), 'max:'.(date('Y') + 20)],
            'cvv' => ['nullable', 'string', 'regex:/^\d{3,4}$/'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'routing_number' => ['nullable', 'string', 'max:255'],
        ]);

        // In a real application, you would integrate with a payment processor
        // For now, we'll just store the encrypted data
        DB::table('payment_methods')->insert([
            'user_id' => $user->user_id,
            'payment_type' => $validated['payment_type'],
            'card_number' => encrypt($validated['card_number'] ?? ''),
            'expiry_month' => $validated['expiry_month'],
            'expiry_year' => $validated['expiry_year'],
            'cvv' => encrypt($validated['cvv'] ?? ''),
            'account_holder_name' => $validated['account_holder_name'],
            'bank_name' => $validated['bank_name'],
            'account_number' => encrypt($validated['account_number'] ?? ''),
            'routing_number' => encrypt($validated['routing_number'] ?? ''),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('student.settings')->with('success', 'Payment method added successfully.');
    }

    /**
     * Remove a payment method for the authenticated student.
     */
    public function removePaymentMethod(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
        ]);

        // Verify ownership
        $paymentMethod = DB::table('payment_methods')
            ->where('id', $validated['payment_method_id'])
            ->where('user_id', $user->user_id)
            ->first();

        if (! $paymentMethod) {
            abort(403);
        }

        DB::table('payment_methods')
            ->where('id', $validated['payment_method_id'])
            ->update(['is_active' => false]);

        return redirect()->route('student.settings')->with('success', 'Payment method removed successfully.');
    }
}
