<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        /* ═══ RESET & BASE ═══ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            background: #f5f5f5;
        }

        .receipt-wrapper {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* ═══ HEADER ═══ */
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .hotel-contact {
            font-size: 11px;
            color: #666;
            margin-bottom: 12px;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 12px;
        }

        .receipt-number {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 4px;
        }

        /* ═══ STATUS BADGES ═══ */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .status-paid { background: #dcfce7; color: #166534; }
        .status-partial { background: #fef3c7; color: #92400e; }
        .status-unpaid { background: #fee2e2; color: #991b1b; }
        .status-refunded { background: #f3e8ff; color: #6b21a8; }

        .reprint-notice {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            font-size: 11px;
            margin-top: 8px;
            border-radius: 4px;
        }

        /* ═══ META INFO ═══ */
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .meta-block {
            flex: 1;
        }

        .meta-block p {
            margin-bottom: 4px;
        }

        .meta-block.right {
            text-align: right;
        }

        .label {
            color: #666;
            font-size: 11px;
        }

        .module-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #3730a3;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ═══ ITEMS TABLE ═══ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .item-name { font-weight: 500; }
        .item-details { font-size: 10px; color: #666; }

        /* ═══ TOTALS ═══ */
        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
        }

        .totals {
            width: 280px;
        }

        .totals td {
            padding: 6px 8px;
            border: none;
        }

        .totals .label-cell {
            color: #666;
            font-size: 12px;
        }

        .totals .value-cell {
            text-align: right;
            font-weight: 500;
        }

        .totals .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .totals .grand-total td {
            padding-top: 12px;
        }

        .balance-due {
            color: #dc2626;
            font-weight: bold;
        }

        .fully-paid {
            color: #16a34a;
        }

        /* ═══ PAYMENT INFO ═══ */
        .payment-info {
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            margin-top: 16px;
        }

        .payment-method {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        /* ═══ NOTES ═══ */
        .notes {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 10px 12px;
            margin-top: 16px;
            font-size: 11px;
        }

        /* ═══ FOOTER ═══ */
        .footer {
            text-align: center;
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }

        .footer-thank-you {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .footer-message {
            color: #666;
            font-size: 11px;
        }

        .footer-legal {
            margin-top: 12px;
            font-size: 9px;
            color: #9ca3af;
        }

        /* ═══ PRINT BUTTON ═══ */
        .print-actions {
            text-align: center;
            margin-top: 20px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .btn {
            display: inline-block;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            margin: 0 4px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .print-info {
            font-size: 11px;
            color: #64748b;
            margin-top: 8px;
        }

        /* ═══ PRINT STYLES ═══ */
        @media print {
            body {
                background: white;
                font-size: 11px;
            }

            .receipt-wrapper {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .header {
                padding-bottom: 12px;
                margin-bottom: 16px;
            }

            .hotel-name {
                font-size: 20px;
            }

            table {
                page-break-inside: avoid;
            }

            .footer {
                margin-top: 16px;
                padding-top: 12px;
            }
        }

        /* ═══ THERMAL PRINTER STYLES (58mm / 80mm) ═══ */
        @media print and (max-width: 80mm) {
            body {
                font-size: 10px;
                width: 80mm;
            }

            .receipt-wrapper {
                padding: 8px;
                width: 100%;
            }

            .header {
                padding-bottom: 8px;
                margin-bottom: 10px;
            }

            .hotel-name {
                font-size: 14px;
            }

            .hotel-contact {
                font-size: 9px;
            }

            .receipt-title {
                font-size: 12px;
            }

            .receipt-number {
                font-size: 11px;
            }

            .meta {
                flex-direction: column;
                gap: 8px;
            }

            .meta-block.right {
                text-align: left;
            }

            th, td {
                padding: 4px 2px;
                font-size: 9px;
            }

            .totals {
                width: 100%;
            }

            .totals .grand-total {
                font-size: 12px;
            }

            .footer-thank-you {
                font-size: 11px;
            }

            .footer-message,
            .footer-legal {
                font-size: 8px;
            }
        }

        /* ═══ A4 PRINT STYLES ═══ */
        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
</head>
<body>
@php
    $statusClass = match($receipt->payment_status) {
        'paid' => 'status-paid',
        'partial' => 'status-partial',
        'refunded' => 'status-refunded',
        default => 'status-unpaid',
    };
@endphp

<div class="receipt-wrapper">
    <!-- Header -->
    <div class="header">
        <div class="hotel-name">{{ config('app.name', 'HOTEL NAME') }}</div>
        <div class="hotel-contact">
            123 Hotel Street, Dar es Salaam, Tanzania<br>
            Tel: +255 XXX XXX XXX | info@hotel.com
        </div>
        <div class="receipt-title">Official Receipt</div>
        <div class="receipt-number">{{ $receipt->receipt_number }}</div>
        <div class="status-badge {{ $statusClass }}">{{ $receipt->payment_status_label }}</div>
        
        @if($receipt->is_refund)
            <div class="status-badge status-refunded">REFUND</div>
        @endif

        @if(isset($isReprint) && $isReprint)
            <div class="reprint-notice">*** REPRINT (Copy {{ $receipt->print_count }}) ***</div>
        @endif
    </div>

    <!-- Meta Information -->
    <div class="meta">
        <div class="meta-block">
            <p><span class="label">Customer:</span> <strong>{{ $receipt->customer_name ?? 'Walk-in Customer' }}</strong></p>
            @if($receipt->customer_phone)
                <p><span class="label">Phone:</span> {{ $receipt->customer_phone }}</p>
            @endif
            <p><span class="label">Module:</span> <span class="module-badge">{{ $receipt->module_label }}</span></p>
        </div>
        <div class="meta-block right">
            <p><span class="label">Receipt No:</span> <strong>{{ $receipt->receipt_number }}</strong></p>
            <p><span class="label">Date:</span> {{ $receipt->issued_at->format('d M Y H:i') }}</p>
            <p><span class="label">Cashier:</span> {{ $receipt->cashier_name ?? '—' }}</p>
            @if($receipt->transaction_reference)
                <p><span class="label">Ref:</span> {{ $receipt->transaction_reference }}</p>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    @if($receipt->items_snapshot && count($receipt->items_snapshot) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items_snapshot as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item['name'] ?? $item['description'] ?? 'Item' }}</div>
                    @if(!empty($item['details']))
                        <div class="item-details">{{ $item['details'] }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                <td class="text-right">{{ number_format($item['unit_price'] ?? 0, 0) }}</td>
                <td class="text-right">{{ number_format($item['amount'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)), 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Totals -->
    <div class="totals-wrapper">
        <table class="totals">
            @if($receipt->subtotal != $receipt->total)
            <tr>
                <td class="label-cell">Subtotal:</td>
                <td class="value-cell">{{ number_format($receipt->subtotal, 0) }} {{ $receipt->currency }}</td>
            </tr>
            @endif

            @if($receipt->discount > 0)
            <tr>
                <td class="label-cell">Discount:</td>
                <td class="value-cell" style="color: #dc2626;">-{{ number_format($receipt->discount, 0) }} {{ $receipt->currency }}</td>
            </tr>
            @endif

            @if($receipt->tax > 0)
            <tr>
                <td class="label-cell">Tax (VAT):</td>
                <td class="value-cell">{{ number_format($receipt->tax, 0) }} {{ $receipt->currency }}</td>
            </tr>
            @endif

            <tr class="grand-total">
                <td>TOTAL:</td>
                <td class="value-cell">{{ number_format($receipt->total, 0) }} {{ $receipt->currency }}</td>
            </tr>

            <tr>
                <td class="label-cell">Amount Paid:</td>
                <td class="value-cell {{ $receipt->amount_paid >= $receipt->total ? 'fully-paid' : '' }}">
                    {{ number_format($receipt->amount_paid, 0) }} {{ $receipt->currency }}
                </td>
            </tr>

            @if($receipt->balance > 0)
            <tr>
                <td class="label-cell">Balance Due:</td>
                <td class="value-cell balance-due">{{ number_format($receipt->balance, 0) }} {{ $receipt->currency }}</td>
            </tr>
            @elseif($receipt->balance < 0)
            <tr>
                <td class="label-cell">Change:</td>
                <td class="value-cell">{{ number_format(abs($receipt->balance), 0) }} {{ $receipt->currency }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Payment Information -->
    @if($receipt->payment_method)
    <div class="payment-info">
        <span class="label">Payment Method:</span>
        <span class="payment-method">{{ ucwords(str_replace('_', ' ', $receipt->payment_method)) }}</span>
    </div>
    @endif

    <!-- Notes -->
    @if($receipt->notes)
    <div class="notes">
        <strong>Note:</strong> {{ $receipt->notes }}
    </div>
    @endif

    <!-- Unpaid Warning -->
    @if($receipt->payment_status === 'unpaid')
    <div class="notes" style="background: #fee2e2; border-color: #dc2626; margin-top: 16px;">
        <strong style="color: #991b1b;">NOT PAID</strong> — This receipt is for reference only. Payment is still due.
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="footer-thank-you">Thank you for your business!</div>
        <div class="footer-message">We appreciate your patronage and look forward to serving you again.</div>
        <div class="footer-legal">
            This is an official receipt. For queries, please contact us at info@hotel.com<br>
            Receipt generated on {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>

    <!-- Print Actions (No Print) -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print Receipt
        </button>
        <a href="{{ route('receipts.reprint', $receipt->receipt_number) }}" class="btn btn-secondary">
            📄 Reprint
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            ← Go Back
        </a>
        <div class="print-info">
            Print count: {{ $receipt->print_count }} | 
            First issued: {{ $receipt->issued_at->format('d M Y H:i') }}
            @if($receipt->printed_at)
                | Last printed: {{ $receipt->printed_at->format('d M Y H:i') }}
            @endif
        </div>
    </div>
</div>

<script>
    // Mark receipt as printed when print dialog is triggered
    window.addEventListener('beforeprint', function() {
        fetch('{{ route("receipts.printed", $receipt->uuid) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).catch(function(err) {
            console.log('Could not update print count:', err);
        });
    });
</script>
</body>
</html>
