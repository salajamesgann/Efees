<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminStudentController extends Controller
{
    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        return view('auth.admin_students_create');
    }

    /**
     * Show Manage Students page with list and create form.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $studentsQuery = Student::with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('student_id', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('section', 'like', "%{$q}%")
                        ->orWhere('department', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('email', 'like', "%{$q}%");
                        });
                });
            })
            ->orderBy('student_id', 'desc');

        $students = $studentsQuery->paginate(10)->withQueryString();

        return view('auth.admin_students', [
            'students' => $students,
            'q' => $q,
        ]);
    }

    /**
     * Store a newly created Student and associated User with student role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'min:7', 'max:20'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Generate unique student ID similar to public registration
        $year = date('Y');
        $prefix = 'STU' . $year;
        $lastId = Student::where('student_id', 'like', $prefix . '%')->max('student_id');
        $nextNumber = 1;
        if ($lastId) {
            $suffix = substr($lastId, -4);
            $nextNumber = ((int) $suffix) + 1;
        }
        $studentId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Create student with signup information
        $student = Student::create([
            'student_id' => $studentId,
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'contact_number' => $validated['contact_number'],
            'sex' => $validated['sex'],
            'level' => $validated['level'],
            'section' => $validated['section'],
        ]);

        // Link a user with role=student
        $studentRole = Role::where('role_name', 'student')->first();

        User::create([
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role_id' => $studentRole ? $studentRole->role_id : null,
            'roleable_type' => 'App\\Models\\Student',
            'roleable_id' => $student->student_id,
        ]);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student created successfully with signup credentials.');
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): View
    {
        $student->load('user');
        return view('auth.admin_students_edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->load('user');

        $user = $student->user;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'min:7', 'max:20'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($user?->user_id ?? 'NULL') . ',user_id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($student, $user, $validated) {
            $student->update([
                'first_name' => $validated['first_name'],
                'middle_initial' => $validated['middle_initial'] ?? null,
                'last_name' => $validated['last_name'],
                'contact_number' => $validated['contact_number'],
                'sex' => $validated['sex'],
                'level' => $validated['level'],
                'section' => $validated['section'],
            ]);

            if ($user) {
                $user->email = strtolower($validated['email']);
                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                }
                $user->save();
            }
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully with signup information.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student) {
            // Delete associated user first (if any)
            $user = $student->user;
            if ($user) {
                $user->delete();
            }
            // Then delete the student
            $student->delete();
        });

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
