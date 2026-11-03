<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'DejaVu Sans', sans-serif; font-size: 13px; color: #1e293b; line-height: 1.5; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #4338ca; }
        .meta-table { width: 100%; margin-bottom: 25px; }
        .meta-table td { vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background: #f1f5f9; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; }
        .items-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .total-box { float: right; width: 280px; }
        .total-box table { width: 100%; border-collapse: collapse; }
        .total-box td { padding: 5px 0; }
        .total-row { font-size: 16px; font-weight: bold; color: #4338ca; border-top: 2px solid #cbd5e1; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">{{ $invoice->currency === 'BDT' ? 'চালানপত্র / বিল' : 'COMMERCIAL INVOICE' }}</div>
            <div>Invoice #: <strong>{{ $invoice->invoice_number }}</strong></div>
            <div>Date: {{ $invoice->created_at->format('M j, Y') }}</div>
            <div>Due Date: {{ $invoice->due_date ? $invoice->due_date->format('M j, Y') : 'Due on receipt' }}</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: bold;">{{ $invoice->tenant?->name ?? 'AI Sales Assistant' }}</div>
            <div>Status: <strong style="color: {{ $invoice->status === 'paid' ? '#16a34a' : '#d97706' }};">{{ strtoupper($invoice->status) }}</strong></div>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <strong style="color: #64748b; font-size: 11px; text-transform: uppercase;">Billed To:</strong><br>
                <strong>{{ $invoice->company?->name ?? $invoice->contact?->full_name ?? 'Valued Customer' }}</strong><br>
                {{ $invoice->contact?->email ?? '' }}<br>
                {{ $invoice->contact?->phone ?? '' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: center; width: 80px;">Qty</th>
                <th style="text-align: right; width: 100px;">Price</th>
                <th style="text-align: right; width: 120px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items ?? [] as $item)
                <tr>
                    <td>
                        <strong>{{ $item['name'] ?? 'Product Item' }}</strong>
                        @if (!empty($item['description']))
                            <br><span style="font-size: 11px; color: #64748b;">{{ $item['description'] }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item['quantity'] ?? $item['qty'] ?? 1 }}</td>
                    <td style="text-align: right;">{{ $invoice->currency }} {{ number_format($item['price'] ?? $item['unit_price'] ?? 0, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ $invoice->currency }} {{ number_format(($item['quantity'] ?? $item['qty'] ?? 1) * ($item['price'] ?? $item['unit_price'] ?? 0), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if ($invoice->tax_amount > 0)
                <tr>
                    <td>Tax:</td>
                    <td style="text-align: right;">{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Due:</td>
                <td style="text-align: right;">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
