<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Ledger - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        :root {
            --bg: #f1f5f9;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe1ea;
            --header: #1e293b;
            --accent: #0ea5e9;
            --success: #16a34a;
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: linear-gradient(160deg, #e2e8f0 0%, #f8fafc 55%, #f1f5f9 100%);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.45;
            padding: 28px;
        }

        .page {
            max-width: 1200px;
            margin: 0 auto;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 14px;
        }

        .btn {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-print {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
        }

        .ledger-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .ledger-header {
            background: linear-gradient(140deg, #0f172a 0%, #1e293b 60%, #334155 100%);
            color: #ffffff;
            padding: 22px 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .ledger-title {
            margin: 0;
            font-size: 26px;
            letter-spacing: 0.2px;
        }

        .ledger-subtitle {
            margin-top: 4px;
            color: #cbd5e1;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .generated {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 10px;
            padding: 8px 10px;
            text-align: right;
            font-size: 12px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--line);
            background: #f8fafc;
        }

        .meta-item {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
        }

        .meta-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            word-break: break-word;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            padding: 16px 24px;
        }

        .summary-box {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            background: #ffffff;
        }

        .summary-box .label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .summary-box .amount {
            margin-top: 6px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .summary-box.debit .amount {
            color: #1e293b;
        }

        .summary-box.credit .amount {
            color: var(--success);
        }

        .summary-box.balance .amount {
            color: var(--danger);
        }

        .table-wrap {
            padding: 0 24px 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        thead th {
            background: var(--header);
            color: #ffffff;
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 11px 10px;
            border: 1px solid #334155;
            text-align: center;
        }

        tbody td {
            padding: 10px;
            border: 1px solid var(--line);
            vertical-align: top;
            background: #ffffff;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .desc {
            font-weight: 600;
            color: #0f172a;
        }

        .notes {
            color: #334155;
        }

        .muted {
            color: var(--muted);
        }

        .empty-row td {
            height: 36px;
        }

        @media print {
            @page {
                margin: 1.2cm;
                size: auto;
            }

            body {
                background: #ffffff;
                padding: 0;
                font-size: 11px;
            }

            .toolbar {
                display: none !important;
            }

            .ledger-card {
                border-radius: 0;
                box-shadow: none;
                border: 1px solid #000;
            }

            .ledger-header {
                background: #ffffff !important;
                color: #000000;
                border-bottom: 1px solid #000;
                padding: 10px 12px;
            }

            .ledger-subtitle,
            .generated,
            .muted {
                color: #333333 !important;
            }

            .meta-grid,
            .summary-grid,
            .table-wrap {
                padding: 10px 12px;
            }

            .meta-grid,
            .summary-grid {
                gap: 8px;
                border-bottom: 1px solid #000;
                background: #ffffff;
            }

            .meta-item,
            .summary-box {
                border: 1px solid #000;
                border-radius: 0;
                box-shadow: none;
                background: #ffffff;
            }

            thead th {
                background: #ffffff;
                color: #000000;
                border: 1px solid #000;
            }

            tbody td {
                border: 1px solid #000;
                background: #ffffff !important;
                padding: 7px 6px;
            }

            .summary-box .amount {
                color: #000000 !important;
            }
        }

        @media (max-width: 900px) {
            body {
                padding: 14px;
            }

            .ledger-header,
            .meta-grid,
            .summary-grid,
            .table-wrap {
                padding-left: 14px;
                padding-right: 14px;
            }

            .meta-grid,
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .ledger-title {
                font-size: 22px;
            }

            .generated {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    @php
        $records = $student->feeRecords->sortBy('created_at');
        $totalDebit = (float) $records->whereNotIn('record_type', ['payment', 'discount'])->sum('amount');
        $totalCredit = (float) $records->whereIn('record_type', ['payment', 'discount'])->sum('amount');
        $endingBalance = (float) optional($records->last())->balance;
    @endphp

    <div class="page">
        <div class="toolbar no-print">
            <button class="btn" onclick="window.history.back()">Back</button>
            <button class="btn btn-print" onclick="window.print()">Print Ledger</button>
        </div>

        <section class="ledger-card">
            <header class="ledger-header">
                <div>
                    <h1 class="ledger-title">Student Ledger</h1>
                    <div class="ledger-subtitle">Financial Statement and Running Balance</div>
                </div>
                <div class="generated">
                    <div><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</div>
                    <div class="muted">For viewing and print reference</div>
                </div>
            </header>

            <div class="meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Student Name</span>
                    <div class="meta-value">{{ $student->first_name }} {{ $student->last_name }}</div>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Grade and Section</span>
                    <div class="meta-value">{{ $student->level }} - {{ $student->section }}</div>
                </div>
                <div class="meta-item">
                    <span class="meta-label">School Year</span>
                    <div class="meta-value">{{ $student->school_year ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-box debit">
                    <div class="label">Total Debit</div>
                    <div class="amount">P{{ number_format($totalDebit, 2) }}</div>
                </div>
                <div class="summary-box credit">
                    <div class="label">Total Credit</div>
                    <div class="amount">P{{ number_format($totalCredit, 2) }}</div>
                </div>
                <div class="summary-box balance">
                    <div class="label">Ending Balance</div>
                    <div class="amount">P{{ number_format($endingBalance, 2) }}</div>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Debit (P)</th>
                            <th>Credit (P)</th>
                            <th>Balance (P)</th>
                            <th>Reference #</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td class="text-center">
                                    {{ optional($record->payment_date ?? $record->created_at)->format('Y-m-d') }}
                                </td>
                                <td class="desc">
                                    {{ ucwords(str_replace('_', ' ', (string) $record->record_type)) }}
                                </td>
                                <td class="text-right">
                                    @if(! in_array($record->record_type, ['payment', 'discount']))
                                        {{ number_format($record->amount, 2) }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if(in_array($record->record_type, ['payment', 'discount']))
                                        {{ number_format($record->amount, 2) }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ number_format($record->balance, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ $record->reference_number ?? '' }}
                                </td>
                                <td class="notes">
                                    {{ $record->notes ?? '' }}
                                </td>
                            </tr>
                        @empty
                            @for($i = 0; $i < 6; $i++)
                                <tr class="empty-row">
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
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
