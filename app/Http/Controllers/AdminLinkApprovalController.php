<?php

namespace App\Http\Controllers;

use App\Models\StudentLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminLinkApprovalController extends Controller
{
    /**
     * Display the list of student link/unlink requests.
     */
    public function index(Request $request): View
    {
        $query = StudentLinkRequest::with(['parent', 'student', 'reviewer']);

        // Filter by status
        $status = $request->input('status', 'pending');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhereHas('parent', fn ($pq) => $pq->where('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('student', fn ($sq) => $sq->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        $requests = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        $pendingCount = StudentLinkRequest::where('status', 'pending')->count();

        return view('admin.link_approvals.index', compact('requests', 'pendingCount', 'status'));
    }

    /**
     * Approve a link/unlink request.
     */
    public function approve(StudentLinkRequest $linkRequest): RedirectResponse
    {
        if (! $linkRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        $requestType = strtolower(trim((string) $linkRequest->type));
        if (! in_array($requestType, ['link', 'unlink'], true)) {
            return back()->with('error', 'Invalid request type.');
        }

        $action = 'updated';

        DB::transaction(function () use ($linkRequest, $requestType, &$action) {
            $linkRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::user()->user_id,
                'reviewed_at' => now(),
            ]);

            if ($requestType === 'link') {
                $alreadyLinked = DB::table('parent_student')
                    ->where('parent_id', $linkRequest->parent_id)
                    ->where('student_id', $linkRequest->student_id)
                    ->exists();

                if ($alreadyLinked) {
                    $action = 'already_linked';
                    DB::table('parent_student')
                        ->where('parent_id', $linkRequest->parent_id)
                        ->where('student_id', $linkRequest->student_id)
                        ->update([
                            'relationship' => $linkRequest->relationship ?? 'Parent',
                            'updated_at' => now(),
                        ]);
                } else {
                    $action = 'linked';
                    DB::table('parent_student')->insert([
                        'parent_id' => $linkRequest->parent_id,
                        'student_id' => $linkRequest->student_id,
                        'relationship' => $linkRequest->relationship ?? 'Parent',
                        'is_primary' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                $action = 'unlinked';
                DB::table('parent_student')
                    ->where('parent_id', $linkRequest->parent_id)
                    ->where('student_id', $linkRequest->student_id)
                    ->delete();
            }

            // Verify final pivot state before commit to avoid false-positive success.
            $isLinkedAfter = DB::table('parent_student')
                ->where('parent_id', $linkRequest->parent_id)
                ->where('student_id', $linkRequest->student_id)
                ->exists();

            if ($requestType === 'link' && ! $isLinkedAfter) {
                throw new \RuntimeException('Failed to persist parent-student link.');
            }
            if ($requestType === 'unlink' && $isLinkedAfter) {
                throw new \RuntimeException('Failed to remove parent-student link.');
            }
        });

        // Notify parent after successful commit; this should not affect link persistence.
        try {
            $parentUser = \App\Models\User::where('roleable_type', \App\Models\ParentContact::class)
                ->where('roleable_id', $linkRequest->parent_id)
                ->first();

            if ($parentUser) {
                $student = $linkRequest->student;
                $verb = $requestType === 'link' ? 'linked to' : 'unlinked from';
                DB::table('notifications')->insert([
                    'user_id' => $parentUser->user_id,
                    'title' => ucfirst($requestType) . ' Request Approved',
                    'body' => 'Your request to have ' . ($student ? $student->full_name : $linkRequest->student_id) . " {$verb} your account has been approved.",
                    'created_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Link approval notification failed: ' . $e->getMessage());
        }

        if ($action === 'already_linked') {
            return back()->with('success', 'Link request approved. Student was already linked to this parent.');
        }

        return back()->with('success', ucfirst($requestType) . ' request approved successfully.');
    }

    /**
     * Reject a link/unlink request.
     */
    public function reject(Request $request, StudentLinkRequest $linkRequest): RedirectResponse
    {
        if (! $linkRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $linkRequest->update([
            'status' => 'rejected',
            'admin_remarks' => $request->input('admin_remarks'),
            'reviewed_by' => Auth::user()->user_id,
            'reviewed_at' => now(),
        ]);

        // Notify the parent
        try {
            $parentUser = \App\Models\User::where('roleable_type', \App\Models\ParentContact::class)
                ->where('roleable_id', $linkRequest->parent_id)
                ->first();

            if ($parentUser) {
                $student = $linkRequest->student;
                $remarks = $request->input('admin_remarks') ? " Reason: {$request->input('admin_remarks')}" : '';
                DB::table('notifications')->insert([
                    'user_id' => $parentUser->user_id,
                    'title' => ucfirst($linkRequest->type) . ' Request Rejected',
                    'body' => 'Your request to ' . $linkRequest->type . ' ' . ($student ? $student->full_name : $linkRequest->student_id) . " was rejected.{$remarks}",
                    'created_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Link rejection notification failed: ' . $e->getMessage());
        }

        return back()->with('success', ucfirst($linkRequest->type) . ' request rejected.');
    }
}
