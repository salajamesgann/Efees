<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Student;
use App\Models\User;

class StudentProfileController extends Controller
{
    /**
     * Show the authenticated student's profile.
     */
    public function show(Request $request): View
    {
        $user = Auth::user();

        if (!$user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $student = $user->roleable; // Morph relation to Student

        // Get notifications for the user
        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('auth.student_profile', [
            'user' => $user,
            'student' => $student,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Show the student settings page.
     */
    public function settings(Request $request): View
    {
        $user = Auth::user();

        if (!$user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $student = $user->roleable; // Morph relation to Student

        return view('auth.student_settings', [
            'user' => $user,
            'student' => $student,
        ]);
    }

    /**
     * Update the authenticated student's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            abort(403);
        }

        $student = $user->roleable; // Student model instance

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'],
            'contact_number' => ['required', 'string', 'min:7', 'max:20'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
        ]);

        // Update student fields
        $student->first_name = $validated['first_name'];
        $student->middle_initial = $validated['middle_initial'] ?? null;
        $student->last_name = $validated['last_name'];
        $student->contact_number = $validated['contact_number'];
        $student->sex = $validated['sex'];
        $student->level = $validated['level'];
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
        if (!empty($validated['new_password'] ?? '')) {
            $user->password = Hash::make($validated['new_password']);
        }
        $user->save();

        return redirect()->route('user_dashboard')->with('success', 'Profile updated successfully.');
    }
}
