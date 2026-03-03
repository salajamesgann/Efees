<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Statement of Account - {{ $student->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
        .container { padding: 30px 40px; }
        .header { display: table; width: 100%; margin-bottom: 25px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .header-left { display: table-cell; vertical-align: top; width: 60%; }
        .header-right { display: table-cell; vertical-align: top; width: 40%; text-align: right; }
        .title { font-size: 22px; font-weight: bold; color: #1e3a5f; margin-bottom: 4px; }
        .subtitle { font-size: 10px; color: #6b7280; }
        .school-name { font-size: 14px; font-weight: bold; color: #1e3a5f; margin-bottom: 2px; }
        .school-info { font-size: 9px; color: #6b7280; }
        
        .info-section { display: table; width: 100%; margin-bottom: 20px; background: #f9fafb; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 4px; }
        .info-left { display: table-cell; vertical-align: top; width: 50%; }
        .info-right { display: table-cell; vertical-align: top; width: 50%; text-align: right; }
        .info-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; font-weight: bold; margin-bottom: 4px; }
        .info-value { font-size: 12px; font-weight: bold; color: #111827; }
        .info-sub { font-size: 10px; color: #6b7280; }
        
        .section-title { font-size: 13px; font-weight: bold; color: #111827; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; font-size: 10px; font-weight: 600; color: #374151; padding: 8px 12px; text-align: left; border-bottom: 1px solid #d1d5db; }
        td { padding: 7px 12px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        
        .summary-row { display: table; width: 100%; }
        .summary-item { display: table-cell; padding: 4px 0; }
        .summary-label { color: #6b7280; }
        .summary-value { font-weight: bold; }
        
        .discount-row { background: #f0fdf4; }
        .discount-row td { color: #15803d; }
        
        .total-row { background: #f9fafb; border-top: 2px solid #d1d5db; }
        .total-row td { font-weight: bold; font-size: 11px; padding: 10px 12px; }
        
        .footer { text-align: center; font-size: 9px; color: #9ca3af; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="title">Statement of Account</div>
                <div class="subtitle">Generated on {{ now()->format('F d, Y') }}</div>
            </div>
            <div class="header-right">
                <div class="school-name">EFees School Management</div>
                <div class="school-info">123 Education Lane<br>Knowledge City, KC 12345<br>finance@efees.edu</div>
            </div>
        </div>

        <!-- Student Info -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-label">Student Details</div>
                <div class="info-value">{{ $student->full_name }}</div>
                <div class="info-sub">{{ $student->student_id }}</div>
                <div class="info-sub">{{ $student->level }} - {{ $student->section }}</div>
            </div>
            <div class="info-right">
                <div class="info-label">Account Summary</div>
                <div style="margin-bottom: 2px;">
                    <span class="info-sub">Total Fees:</span>
                    <span class="font-bold">&#8369;{{ number_format((float) ($totals['totalAmount'] ?? 0), 2) }}</span>
                </div>
                <div style="margin-bottom: 2px;">
                    <span class="info-sub">Total Paid:</span>
                    <span class="font-bold text-green">-&#8369;{{ number_format((float) ($totals['paidAmount'] ?? 0), 2) }}</span>
                </div>
                <div style="border-top: 1px solid #d1d5db; padding-top: 3px; margin-top: 3px;">
                    <span class="font-bold">Balance Due:</span>
                    <span class="font-bold text-blue">&#8369;{{ number_format((float) ($totals['remainingBalance'] ?? 0), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Fee Breakdown -->
        <div class="section-title">Fee Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($assignment)
                    <tr>
                        <td>Base Tuition</td>
                        <td class="text-right">&#8369;{{ number_format($assignment->base_tuition, 2) }}</td>
                    </tr>
                    @foreach($assignment->additionalCharges as $charge)
                        <tr>
                            <td style="color: #6b7280;">{{ $charge->name }}</td>
                            <td class="text-right" style="color: #6b7280;">&#8369;{{ number_format($charge->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    @foreach($assignment->discounts as $discount)
                        <tr class="discount-row">
                            <td>{{ $discount->discount_name }} <span style="font-size: 8px;">(Discount)</span></td>
                            <td class="text-right">-&#8369;{{ number_format($discount->pivot->applied_amount ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="text-center" style="color: #9ca3af;">No fee assessment found.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Transaction History -->
        <div class="section-title">Transaction History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Debit (Fee)</th>
                    <th class="text-right">Credit (Payment)</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $runningBalance = 0; @endphp
                @forelse($transactions as $trx)
                    @php
                        $debit = $trx->record_type !== 'payment' ? $trx->amount : 0;
                        $credit = $trx->record_type === 'payment' ? $trx->amount : 0;
                        if ($trx->record_type !== 'payment') {
                            $runningBalance += $debit;
                        } else {
                            $runningBalance -= $credit;
                        }
                    @endphp
                    <tr>
                        <td>{{ $trx->created_at->format('M d, Y') }}</td>
                        <td>
                            {{ $trx->description ?? $trx->notes }}
                            @if($trx->record_type === 'payment' && $trx->reference_number)
                                <br><span style="font-size: 8px; color: #9ca3af;">Ref: {{ $trx->reference_number }}</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $debit > 0 ? '₱' . number_format($debit, 2) : '-' }}</td>
                        <td class="text-right text-green">{{ $credit > 0 ? '₱' . number_format($credit, 2) : '-' }}</td>
                        <td class="text-right font-bold">&#8369;{{ number_format($runningBalance, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #9ca3af; padding: 20px;">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated statement. No signature required.</p>
            <p style="margin-top: 3px;">For any discrepancies, please contact the Finance Office immediately.</p>
        </div>
    </div>
</body>
</html>
