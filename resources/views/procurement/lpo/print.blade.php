<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $localPurchaseOrder->lpo_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            background: #f5f5f5;
        }

        .document-wrapper {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .hotel-logo {
            max-height: 60px;
            margin-bottom: 8px;
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

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 12px;
            letter-spacing: 2px;
        }

        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .status-draft { background: #f3f4f6; color: #6b7280; }
        .status-pending_approval { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-fully_received { background: #e0e7ff; color: #3730a3; }
        .status-partially_received { background: #fef3c7; color: #92400e; }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-label {
            color: #6b7280;
            font-size: 11px;
        }

        .info-value {
            font-weight: 600;
            font-size: 12px;
            text-align: right;
        }

        .supplier-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .supplier-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #1e293b;
        }

        .supplier-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 16px;
        }

        .supplier-details .detail {
            font-size: 11px;
            line-height: 1.6;
        }

        .supplier-details .detail .lbl {
            color: #6b7280;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        td {
            padding: 9px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .item-name { font-weight: 500; }
        .item-sku { font-size: 10px; color: #9ca3af; }

        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
        }

        .totals {
            width: 300px;
        }

        .totals td {
            padding: 6px 10px;
            border: none;
        }

        .totals .lbl {
            color: #6b7280;
            font-size: 12px;
        }

        .totals .val {
            text-align: right;
            font-weight: 500;
            font-size: 12px;
        }

        .totals .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .totals .grand-total td {
            padding-top: 12px;
        }

        .terms-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }

        .terms-box .lbl {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .terms-box .val {
            font-size: 12px;
            color: #374151;
        }

        .footer {
            text-align: center;
            margin-top: 28px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }

        .footer-text {
            color: #9ca3af;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 40px;
        }

        .signature-line {
            flex: 1;
            border-top: 1px solid #333;
            padding-top: 4px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

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

        .btn-primary:hover { background: #1d4ed8; }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover { background: #cbd5e1; }

        .rejection-notice {
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 12px;
        }

        .rejection-notice strong {
            color: #991b1b;
        }

        @media print {
            body {
                background: white;
                font-size: 11px;
            }

            .document-wrapper {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .no-print { display: none !important; }

            .header {
                padding-bottom: 12px;
                margin-bottom: 14px;
            }

            .hotel-name { font-size: 20px; }

            table { page-break-inside: avoid; }

            .section { page-break-inside: avoid; }

            .footer { margin-top: 18px; padding-top: 10px; }
        }

        @page {
            size: A4;
            margin: 12mm;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/logo.png');
    $hasLogo = file_exists($logoPath);
    $hotelAddress = config('app.hotel_address', '123 Hotel Street, Dar es Salaam, Tanzania');
    $hotelPhone = config('app.hotel_phone', '+255 XXX XXX XXX');
    $hotelEmail = config('app.hotel_email', 'info@hotel.com');
    $statusLabels = [
        'draft' => 'Draft',
        'pending_approval' => 'Pending Approval',
        'approved' => 'Approved',
        'sent' => 'Sent to Supplier',
        'rejected' => 'Rejected',
        'partially_received' => 'Partially Received',
        'fully_received' => 'Fully Received',
    ];
    $statusBadgeClass = match($localPurchaseOrder->status) {
        'draft' => 'status-draft',
        'pending_approval' => 'status-pending_approval',
        'approved' => 'status-approved',
        'sent' => 'status-sent',
        'rejected' => 'status-rejected',
        'fully_received' => 'status-fully_received',
        'partially_received' => 'status-partially_received',
        default => 'status-draft',
    };
@endphp

<div class="document-wrapper">
    <!-- Header -->
    <div class="header">
        @if($hasLogo)
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="hotel-logo">
        @endif
        <div class="hotel-name">{{ config('app.hotel_name', config('app.name', 'HMS')) }}</div>
        <div class="hotel-contact">
            {{ $hotelAddress }}<br>
            Tel: {{ $hotelPhone }} | {{ $hotelEmail }}
        </div>
        <div class="doc-title">PURCHASE ORDER</div>
        <div class="doc-number">{{ $localPurchaseOrder->lpo_number }}</div>
        <div class="status-badge {{ $statusBadgeClass }}">
            {{ $statusLabels[$localPurchaseOrder->status] ?? ucfirst($localPurchaseOrder->status) }}
        </div>
    </div>

    <!-- Order Info -->
    <div class="section">
        <div class="section-title">Order Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">LPO Number</span>
                <span class="info-value">{{ $localPurchaseOrder->lpo_number }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Order Date</span>
                <span class="info-value">{{ $localPurchaseOrder->order_date->format('d M, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Expected Delivery</span>
                <span class="info-value">{{ $localPurchaseOrder->expected_delivery_date?->format('d M, Y') ?? '—' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Requested By</span>
                <span class="info-value">{{ $localPurchaseOrder->creator?->name ?? '—' }}</span>
            </div>
            @if($localPurchaseOrder->approver)
            <div class="info-item">
                <span class="info-label">Approved By</span>
                <span class="info-value">{{ $localPurchaseOrder->approver->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Approved On</span>
                <span class="info-value">{{ $localPurchaseOrder->approved_at?->format('d M, Y H:i') ?? '—' }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Rejection Notice -->
    @if($localPurchaseOrder->status === 'rejected' && $localPurchaseOrder->rejection_reason)
    <div class="rejection-notice">
        <strong>Rejected:</strong> {{ $localPurchaseOrder->rejection_reason }}
    </div>
    @endif

    <!-- Supplier Info -->
    <div class="supplier-section">
        <div class="section-title">Supplier</div>
        <div class="supplier-name">{{ $localPurchaseOrder->supplierName }}</div>
        @if($localPurchaseOrder->supplier)
        <div class="supplier-details">
            @if($localPurchaseOrder->supplier->contact_person)
            <div class="detail"><span class="lbl">Contact Person:</span> {{ $localPurchaseOrder->supplier->contact_person }}</div>
            @endif
            @if($localPurchaseOrder->supplier->phone)
            <div class="detail"><span class="lbl">Phone:</span> {{ $localPurchaseOrder->supplier->phone }}</div>
            @endif
            @if($localPurchaseOrder->supplier->email)
            <div class="detail"><span class="lbl">Email:</span> {{ $localPurchaseOrder->supplier->email }}</div>
            @endif
            @if($localPurchaseOrder->supplier->address)
            <div class="detail"><span class="lbl">Address:</span> {{ $localPurchaseOrder->supplier->address }}</div>
            @endif
            @if($localPurchaseOrder->supplier->tin_number)
            <div class="detail"><span class="lbl">TIN:</span> {{ $localPurchaseOrder->supplier->tin_number }}</div>
            @endif
            @if($localPurchaseOrder->supplier->vrn_number)
            <div class="detail"><span class="lbl">VRN:</span> {{ $localPurchaseOrder->supplier->vrn_number }}</div>
            @endif
        </div>
        @endif
    </div>

    <!-- Items Table -->
    <div class="section">
        <div class="section-title">Order Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Item</th>
                    <th style="width: 12%;">Unit</th>
                    <th class="text-right" style="width: 13%;">Quantity</th>
                    <th class="text-right" style="width: 17%;">Unit Price</th>
                    <th class="text-right" style="width: 18%;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($localPurchaseOrder->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->item_name }}</div>
                        @if($item->product?->sku)
                            <div class="item-sku">{{ $item->product->sku }}</div>
                        @endif
                    </td>
                    <td>{{ $item->unit ?? '—' }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="totals-wrapper">
        <table class="totals">
            <tr>
                <td class="lbl">Subtotal:</td>
                <td class="val">{{ number_format($localPurchaseOrder->subtotal, 0) }} TZS</td>
            </tr>
            <tr>
                <td class="lbl">Tax (18% VAT):</td>
                <td class="val">{{ number_format($localPurchaseOrder->tax_amount, 0) }} TZS</td>
            </tr>
            <tr class="grand-total">
                <td style="font-size: 14px;">TOTAL COST:</td>
                <td class="val" style="font-size: 16px;">{{ number_format($localPurchaseOrder->grand_total, 0) }} TZS</td>
            </tr>
        </table>
    </div>

    <!-- Notes & Terms -->
    @if($localPurchaseOrder->notes)
    <div class="terms-box">
        <div class="lbl">Notes</div>
        <div class="val">{{ $localPurchaseOrder->notes }}</div>
    </div>
    @endif

    @if($localPurchaseOrder->terms)
    <div class="terms-box">
        <div class="lbl">Terms &amp; Conditions</div>
        <div class="val">{{ $localPurchaseOrder->terms }}</div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="footer-signatures">
        <div class="signature-line">Requested By</div>
        <div class="signature-line">Approved By</div>
        <div class="signature-line">Received By</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-text">{{ config('app.hotel_name', config('app.name', 'HMS')) }} &mdash; {{ $hotelAddress }}</div>
        <div class="footer-text">{{ $hotelPhone }} | {{ $hotelEmail }}</div>
        <div class="footer-text" style="margin-top: 6px;">
            Generated on {{ now()->format('d M Y H:i:s') }}
            @if($localPurchaseOrder->status === 'approved' || $localPurchaseOrder->status === 'sent')
                &mdash; This is an official purchase order document.
            @endif
        </div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print Purchase Order
        </button>
    </div>
</div>

</body>
</html>
