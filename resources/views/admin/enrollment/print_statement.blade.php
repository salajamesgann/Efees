<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Ledger - {{ $student->full_name }}</title>
    <style>
        @media print {
            @page { margin: 1.5cm; }
            .no-print { display: none !important; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        th {
            font-weight: bold;
            text-align: center;
        }
        .outer-table th,
        .outer-table td {
            border-width: 1px;
        }
        .title-cell {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .label-cell {
            width: 120px;
            border-right: none;
        }
        .value-cell {
            border-left: none;
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 12px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; font-weight: bold;">Print</button>
    </div>

    <table class="outer-table">
        <tr>
            <td class="title-cell" colspan="7">Student Ledger</td>
        </tr>
        <tr>
            <td class="label-cell">Name:</td>
            <td class="value-cell" colspan="6">{{ $student->full_name }}</td>
        </tr>
        <tr>
            <td class="label-cell">Grade &amp; Section:</td>
            <td class="value-cell" colspan="6">{{ $student->level }} - {{ $student->section }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Debit (₱)</th>
            <th>Credit (₱)</th>
            <th>Balance (₱)</th>
            <th>Reference #</th>
            <th>Notes</th>
        </tr>
        @php
            $records = $student->feeRecords->sortBy('created_at');
        @endphp
        @forelse($records as $record)
            <tr>
                <td style="text-align: center;">
                    {{ optional($record->payment_date ?? $record->created_at)->format('Y-m-d') }}
                </td>
                <td>
                    {{ ucfirst($record->record_type) }}
                </td>
                <td style="text-align: right;">
                    @if(! in_array($record->record_type, ['payment', 'discount']))
                        {{ number_format($record->amount, 2) }}
                    @endif
                </td>
                <td style="text-align: right;">
                    @if(in_array($record->record_type, ['payment', 'discount']))
                        {{ number_format($record->amount, 2) }}
                    @endif
                </td>
                <td style="text-align: right;">
                    {{ number_format($record->balance, 2) }}
                </td>
                <td style="text-align: center;">
                    {{ $record->reference_number ?? '' }}
                </td>
                <td>
                    {{ $record->notes ?? '' }}
                </td>
            </tr>
        @empty
            @for($i = 0; $i < 6; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        @endforelse
    </table>

</body>
</html>
