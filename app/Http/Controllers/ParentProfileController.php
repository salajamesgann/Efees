<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParentProfileController extends Controller
{
    /**
     * Show the parent profile settings page.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $parent = $user->roleable;

        if (! $parent instanceof ParentContact) {
            abort(403, 'Unauthorized access.');
        }

        // Data for sidebar
        $myChildren = $parent->students()->get();

        // Get communication preferences
        $preferences = \Illuminate\Support\Facades\DB::table('user_preferences')
            ->where('user_id', $user->user_id)
            ->first();

        return view('auth.parent_profile', [
            'user' => $user,
            'parent' => $parent,
            'isParent' => true,
            'myChildren' => $myChildren,
            'selectedChild' => null,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update the parent profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $parent = $user->roleable;

        if (! $parent instanceof ParentContact) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('parents', 'phone')->ignore($parent->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'address_street' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'sms_reminders' => ['boolean'],
            'email_notifications' => ['boolean'],
            'payment_reminders' => ['boolean'],
        ]);

        // Update Parent Contact Info
        $parent->update([
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address_street' => $validated['address_street'],
        ]);

        // Update Notification Preferences
        \Illuminate\Support\Facades\DB::table('user_preferences')->updateOrInsert(
            ['user_id' => $user->user_id],
            [
                'sms_reminders' => $request->has('sms_reminders'),
                'email_notifications' => $request->has('email_notifications'),
                'payment_reminders' => $request->has('payment_reminders'),
                'updated_at' => now(),
            ]
        );

        // Update User Password if provided
        if (! empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Also update the User email if it matches the parent email (optional, but good for consistency)
        if ($validated['email'] && $user->email !== $validated['email']) {
            // Check if email is already taken by another user
            if (! User::where('email', $validated['email'])->where('user_id', '!=', $user->user_id)->exists()) {
                $user->update(['email' => $validated['email']]);
            }
        }

        // Log the action
        try {
            AuditService::log(
                'Parent Profile Update',
                $user,
                "Parent {$parent->full_name} updated their profile.",
                null,
                $request->except('password', 'password_confirmation')
            );
        } catch (\Throwable $e) {
        }

        return redirect()->route('parent.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
