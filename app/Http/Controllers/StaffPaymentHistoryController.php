<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffPaymentHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $from = $request->get('from');
        $to = $request->get('to');
        $method = $request->get('method');

        $query = Payment::with(['student', 'receipt']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($from) {
            $query->whereDate('paid_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('paid_at', '<=', $to);
        }
        if ($method) {
            $query->where('method', $method);
        }

        $payments = $query->orderBy('paid_at', 'desc')->paginate(15);
        $methods = Payment::select('method')->distinct()->pluck('method');

        return view('auth.staff_payment_history', compact('payments', 'search', 'from', 'to', 'method', 'methods'));
    }
}
