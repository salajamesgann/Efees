<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Services\AuditService;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSmsController extends Controller
{
    protected $smsGateway;

    public function __construct(SmsGatewayService $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    public function templates(): View
    {
        $templates = SmsTemplate::orderBy('name')->paginate(15);

        return view('auth.admin_sms_templates', compact('templates'));
    }

    public function createTemplate(): View
    {
        return view('auth.admin_sms_template_form', ['template' => new SmsTemplate]);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);
        $template = SmsTemplate::create($data);

        // Audit Log
        try {
            AuditService::log(
                'SMS Template Created',
                $template,
                "Created SMS template: {$template->name}",
                null,
                $template->toArray()
            );
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.sms.templates')->with('success', 'Template created');
    }

    public function editTemplate(SmsTemplate $template): View
    {
        return view('auth.admin_sms_template_form', compact('template'));
    }

    public function updateTemplate(Request $request, SmsTemplate $template): RedirectResponse
    {
        $oldValues = $template->toArray();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);
        $template->update($data);

        // Audit Log
        try {
            AuditService::log(
                'SMS Template Updated',
                $template,
                "Updated SMS template: {$template->name}",
                $oldValues,
                $template->toArray()
            );
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.sms.templates')->with('success', 'Template updated');
    }

    public function destroy(SmsTemplate $template): RedirectResponse
    {
        $oldValues = $template->toArray();
        $templateName = $template->name;
        $template->delete();

        // Audit Log
        try {
            AuditService::log(
                'SMS Template Deleted',
                $template,
                "Deleted SMS template: {$templateName}",
                $oldValues,
                null
            );
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.sms.templates')->with('success', 'Template deleted successfully');
    }

    public function logs(Request $request): View
    {
        $status = $request->get('status');
        $search = $request->get('search');
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Audit Log
        try {
            AuditService::log(
                'View SMS Logs',
                null,
                "Viewed SMS logs (Status: {$status}, Type: {$type}, Search: {$search})",
                null,
                $request->all()
            );
        } catch (\Throwable $e) {
        }

        // Statistics
        $stats = [
            'total' => SmsLog::count(),
            'sent' => SmsLog::whereIn('status', ['sent', 'delivered'])->count(),
            'failed' => SmsLog::where('status', 'failed')->count(),
            'queued' => SmsLog::where('status', 'queued')->count(),
            'today' => SmsLog::whereDate('sent_at', now())->count(),
        ];

        $logs = SmsLog::with(['student', 'user.roleable'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($type, fn ($q) => $q->where('message_type', $type))
            ->when($startDate, fn ($q) => $q->whereDate('sent_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('sent_at', '<=', $endDate))
            ->when($search, function ($q) use ($search) {
                $operator = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where(function ($subQ) use ($search, $operator) {
                    $subQ->where('mobile_number', 'like', "%{$search}%")
                        ->orWhere('message', $operator, "%{$search}%")
                        ->orWhereHas('student', function ($sq) use ($search, $operator) {
                            $sq->where('first_name', $operator, "%{$search}%")
                                ->orWhere('last_name', $operator, "%{$search}%")
                                ->orWhere('student_id', $operator, "%{$search}%");
                        });
                });
            })
            ->orderBy('sent_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $templates = SmsTemplate::orderBy('name')->paginate(15);

        return view('auth.admin_sms_logs', compact('logs', 'status', 'search', 'type', 'startDate', 'endDate', 'stats', 'templates'));
    }

    public function resend(SmsLog $log): RedirectResponse
    {
        $status = 'queued';
        $sentAt = null;
        $providerResponse = null;

        // Try to resend immediately
        try {
            $response = $this->smsGateway->send($log->mobile_number, $log->message);
            $status = $response['status'];
            $providerResponse = $response['response'] ?? null;
            if ($response['success']) {
                $sentAt = now();
            }
        } catch (\Exception $e) {
            $status = 'failed';
            $providerResponse = $e->getMessage();
        }

        $newLog = SmsLog::create([
            'student_id' => $log->student_id,
            'user_id' => auth()->id(), // Current admin
            'mobile_number' => $log->mobile_number,
            'message' => $log->message,
            'message_type' => $log->message_type,
            'status' => $status,
            'sent_at' => $sentAt ?? now(),
            'provider_response' => $providerResponse,
        ]);

        // Audit Log
        try {
            AuditService::log(
                'Resend SMS',
                $newLog,
                "Resent SMS to {$newLog->mobile_number}",
                null,
                ['original_log_id' => $log->id, 'new_log_id' => $newLog->id]
            );
        } catch (\Throwable $e) {
        }

        // In a real app, dispatch job here: SmsDispatchJob::dispatch($newLog);

        return back()->with('success', 'Message queued for resending.');
    }

    public function statuses(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = collect($request->input('ids', []))->filter()->values();
        $logs = SmsLog::whereIn('id', $ids)->select('id', 'status')->get();

        return response()->json($logs->map(fn ($l) => ['id' => $l->id, 'status' => $l->status]));
    }

    public function simulate(Request $request, SmsLog $log): \Illuminate\Http\JsonResponse
    {
        $status = $request->input('status');
        if (! in_array($status, ['sent', 'delivered', 'failed', 'queued'])) {
            return response()->json(['error' => 'invalid'], 422);
        }
        if (! $log->gateway_message_id) {
            $log->gateway_message_id = 'SIM-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12));
        }
        $log->status = $status;
        $log->save();

        return response()->json(['ok' => true, 'id' => $log->id, 'status' => $log->status]);
    }

    public function twilioCallback(Request $request): \Illuminate\Http\Response
    {
        $sid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        if (! $sid) {
            return response('Missing MessageSid', 400);
        }

        $log = SmsLog::where('gateway_message_id', $sid)->first();
        if ($log) {
            $mapped = in_array($status, ['delivered']) ? 'delivered' : (in_array($status, ['failed', 'undelivered']) ? 'failed' : 'sent');
            $log->update(['status' => $mapped]);
        }

        return response('OK', 200);
    }
}
