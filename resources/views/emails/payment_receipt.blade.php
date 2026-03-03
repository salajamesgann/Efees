<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6; color: #1a1a1a; }
        .wrapper { max-width: 560px; margin: 0 auto; padding: 30px 20px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #2563eb; padding: 24px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 20px; margin: 0 0 4px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 12px; margin: 0; }
        .body { padding: 28px 24px; }
        .success { text-align: center; margin-bottom: 24px; }
        .success-icon { display: inline-block; width: 48px; height: 48px; background: #dcfce7; color: #16a34a; border-radius: 50%; line-height: 48px; font-size: 22px; font-weight: bold; }
        .success h2 { font-size: 18px; color: #111827; margin: 10px 0 4px; }
        .success p { font-size: 13px; color: #6b7280; margin: 0; }
        .amount-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; text-align: center; margin-bottom: 24px; }
        .amount-value { font-size: 32px; font-weight: bold; color: #111827; }
        .amount-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 600; margin-top: 4px; }
        .details { width: 100%; border-collapse: collapse; }
        .details tr { border-bottom: 1px solid #f3f4f6; }
        .details td { padding: 10px 0; font-size: 13px; }
        .details .label { color: #6b7280; width: 40%; }
        .details .value { text-align: right; font-weight: 600; color: #111827; }
        .footer { padding: 16px 24px; background: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 11px; color: #9ca3af; margin: 2px 0; }
        .ref { font-family: monospace; font-size: 11px; color: #9ca3af; margin-top: 8px; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; font-size: 14px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <h1>{{ $schoolName }}</h1>
                <p>Official Payment Receipt</p>
            </div>

            <!-- Body -->
            <div class="body">
                <!-- Success Badge -->
                <div class="success">
                    <div class="success-icon">&#10003;</div>
                    <h2>Payment Received!</h2>
                    <p>Thank you for your payment. Here's your receipt.</p>
                </div>

                <!-- Amount -->
                <div class="amount-box">
                    <div class="amount-value">&#8369;{{ number_format($payment->amount_paid, 2) }}</div>
                    <div class="amount-label">Total Amount Paid</div>
                </div>

                <!-- Details -->
                <table class="details">
                    <tr>
                        <td class="label">Student Name</td>
                        <td class="value">{{ $payment->student->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Student ID</td>
                        <td class="value">{{ $payment->student->student_id }}</td>
                    </tr>
                    @if($schoolYear)
                    <tr>
                        <td class="label">School Year</td>
                        <td class="value">{{ $schoolYear }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Payment Method</td>
                        <td class="value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $payment->method ?? 'Cash') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date & Time</td>
                        <td class="value">{{ $payment->created_at->format('M d, Y - h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value" style="color: #16a34a;">&#10003; Completed</td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This is a computer-generated receipt. No signature required.</p>
                <p>For inquiries, contact the Finance Office.</p>
                <div class="ref">REF: {{ $payment->reference_number ?? 'REC-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>
</body>
</html>
