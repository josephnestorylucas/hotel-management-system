<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movement Report</title>
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
            max-width: 1100px;
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

        .hotel-logo { max-height: 60px; margin-bottom: 8px; }

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
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a1a1a;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 11px;
            color: #666;
        }

        .filter-info {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 11px;
            color: #555;
        }

        .filter-info strong { color: #333; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead th {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
        }

        tbody td {
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) { background: #fafafa; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .type-in { background: #d1fae5; color: #065f46; }
        .type-out { background: #fee2e2; color: #991b1b; }
        .type-adj { background: #dbeafe; color: #1e40af; }

        .qty-in { color: #059669; font-weight: 600; }
        .qty-out { color: #dc2626; font-weight: 600; }

        .footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #999;
        }

        .print-actions {
            text-align: center;
            margin: 20px 0;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-primary:hover { background: #1d4ed8; }

        .summary-cards {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }

        .summary-card {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            text-align: center;
        }

        .summary-card .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-card .value {
            font-size: 20px;
            font-weight: 700;
        }

        @media print {
            body { background: white; font-size: 11px; }
            .document-wrapper {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .no-print { display: none !important; }
            .header { padding-bottom: 12px; margin-bottom: 14px; }
            .hotel-name { font-size: 20px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }
            .footer { margin-top: 14px; padding-top: 8px; }
        }

        @page {
            size: A4 landscape;
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
    $now = now()->format('d M Y H:i');
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
        <div class="doc-title">Stock Movement Report</div>
    </div>

    <!-- Report Meta -->
    <div class="report-meta">
        <div><strong>Generated:</strong> {{ $now }}</div>
        <div><strong>Total Records:</strong> {{ $movements->count() }}</div>
    </div>

    <!-- Filter Summary -->
    @if(!empty($filterSummary) && count($filterSummary) > 0)
    <div class="filter-info">
        <strong>Active Filters:</strong>
        @foreach($filterSummary as $key => $value)
            <span style="margin-left: 12px;">{{ ucfirst($key) }}: <strong>{{ $value }}</strong></span>
        @endforeach
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="summary-cards">
        @php
            $totalIn = $movements->whereIN('type', ['restock', 'transfer_in'])->count();
            $totalOut = $movements->whereIN('type', ['damage', 'transfer_out', 'internal_use'])->count();
            $totalAdj = $movements->where('type', 'adjustment')->count();
        @endphp
        <div class="summary-card">
            <div class="label">Total Movements</div>
            <div class="value" style="color: #1f2937;">{{ $movements->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Stock In</div>
            <div class="value" style="color: #059669;">{{ $totalIn }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Stock Out</div>
            <div class="value" style="color: #dc2626;">{{ $totalOut }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Adjustments</div>
            <div class="value" style="color: #2563eb;">{{ $totalAdj }}</div>
        </div>
    </div>

    <!-- Movements Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 13%">Date/Time</th>
                <th style="width: 18%">Product</th>
                <th style="width: 13%">Location</th>
                <th style="width: 11%">Type</th>
                <th style="width: 8%" class="text-right">Qty</th>
                <th style="width: 8%" class="text-right">Before</th>
                <th style="width: 8%" class="text-right">After</th>
                <th style="width: 12%">Reference</th>
                <th style="width: 8%">By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $m)
            <tr>
                <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                <td><strong>{{ $m->product->name }}</strong></td>
                <td>{{ $m->location->name }}</td>
                <td>
                    <span class="type-badge
                        @if(in_array($m->type, ['restock','transfer_in'])) type-in
                        @elseif(in_array($m->type, ['damage','transfer_out','internal_use'])) type-out
                        @else type-adj @endif">
                        {{ ucwords(str_replace('_', ' ', $m->type)) }}
                    </span>
                </td>
                <td class="text-right {{ in_array($m->type, ['restock', 'transfer_in']) ? 'qty-in' : 'qty-out' }}">
                    {{ in_array($m->type, ['restock', 'transfer_in']) ? '+' : '-' }}{{ $m->quantity }}
                </td>
                <td class="text-right">{{ $m->quantity_before }}</td>
                <td class="text-right"><strong>{{ $m->quantity_after }}</strong></td>
                <td>{{ $m->reference ?? '—' }}</td>
                <td>{{ $m->actor->name ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px; color: #999;">No movements found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>Stock Movement Report &mdash; {{ config('app.name', 'HMS') }}</div>
        <div>Printed by {{ auth()->user()->name ?? 'System' }} on {{ $now }}</div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn-primary">
            Print Report
        </button>
    </div>
</div>

</body>
</html>
