<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AdminPasswordRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = PasswordResetRequest::query();

        // Search by email
        if ($search = $request->input('search')) {
            $query->where('email', 'like', '%'.$search.'%');
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('auth.admin_password_requests', compact('requests'))
            ->with('request', $request); // Pass request to view for form values
    }

    public function approve(PasswordResetRequest $request): RedirectResponse
    {
        // Attempt to send the reset link
        $status = Password::sendResetLink(['email' => $request->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $request->update(['status' => 'approved']);

            return back()->with('success', 'Reset link sent successfully to '.$request->email);
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function reject(PasswordResetRequest $request): RedirectResponse
    {
        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Request rejected.');
    }

    public function destroy(PasswordResetRequest $request): RedirectResponse
    {
        $request->delete();

        return back()->with('success', 'Request deleted.');
    }
}
