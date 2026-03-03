<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - {{ $payment->reference_number ?? 'REC-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
        .container { padding: 40px; max-width: 500px; margin: 0 auto; }
        
        .header { text-align: center; padding: 20px; background: #2563eb; color: white; border-radius: 8px 8px 0 0; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header .subtitle { font-size: 10px; opacity: 0.9; margin-top: 3px; }
        
        .body { background: white; padding: 25px; border: 1px solid #e5e7eb; border-top: none; }
        
        .success-badge { text-align: center; margin-bottom: 20px; }
        .success-icon { display: inline-block; width: 50px; height: 50px; background: #dcfce7; border-radius: 50%; line-height: 50px; font-size: 24px; color: #16a34a; margin-bottom: 8px; }
        .success-text { font-size: 16px; font-weight: bold; color: #111827; }
        
        .amount { text-align: center; margin: 20px 0; padding: 15px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; }
        .amount-value { font-size: 28px; font-weight: bold; color: #111827; }
        .amount-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: bold; margin-top: 3px; }
        
        .details { margin: 20px 0; }
        .detail-row { display: table; width: 100%; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
        .detail-label { display: table-cell; width: 40%; color: #6b7280; font-size: 10px; padding: 4px 0; }
        .detail-value { display: table-cell; width: 60%; text-align: right; font-weight: 600; color: #111827; font-size: 10px; padding: 4px 0; }
        
        .footer { text-align: center; padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; }
        .footer p { font-size: 9px; color: #9ca3af; }
        .ref { font-family: monospace; font-size: 9px; color: #9ca3af; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>E-Fees Portal</h1>
            <div class="subtitle">Official Payment Receipt</div>
        </div>

        <!-- Body -->
        <div class="body">
            <!-- Success Badge -->
            <div class="success-badge">
                <div class="success-icon">&#10003;</div>
                <div class="success-text">Payment Successful</div>
            </div>

            <!-- Amount -->
            <div class="amount">
                <div class="amount-value">&#8369;{{ number_format($payment->amount_paid, 2) }}</div>
                <div class="amount-label">Total Amount Paid</div>
            </div>

            <!-- Details -->
            <div class="details">
                <div class="detail-row">
                    <div class="detail-label">School</div>
                    <div class="detail-value">{{ $schoolName }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">School Year</div>
                    <div class="detail-value">{{ $schoolYear }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Student Name</div>
                    <div class="detail-value">{{ $payment->student->full_name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Student ID</div>
                    <div class="detail-value">{{ $payment->student->student_id }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $payment->method ?? 'Cash') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date & Time</div>
                    <div class="detail-value">{{ $payment->created_at->format('M d, Y - h:i A') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value" style="color: #16a34a;">Completed</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated receipt. No signature required.</p>
            <p style="margin-top: 3px;">For inquiries, contact the Finance Office.</p>
            <div class="ref">REF: {{ $payment->reference_number ?? 'REC-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
</body>
</html>
