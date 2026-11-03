<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $quotation->quote_number }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'DejaVu Sans', sans-serif; font-size: 13px; color: #1e293b; line-height: 1.5; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: #4338ca; }
        .meta-table { width: 100%; margin-bottom: 25px; }
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
            <div class="title">{{ $quotation->currency === 'BDT' ? 'মূল্য প্রস্তাবনা / কোটেশন' : 'PRICE QUOTATION' }}</div>
            <div>Quote #: <strong>{{ $quotation->quote_number }}</strong></div>
            <div>Valid Until: {{ $quotation->valid_until ? $quotation->valid_until->format('M j, Y') : '30 days from issue' }}</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: bold;">{{ $quotation->tenant?->name ?? 'AI Sales Assistant' }}</div>
            <div>Status: <strong style="color: #4338ca;">{{ strtoupper($quotation->status) }}</strong></div>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <strong style="color: #64748b; font-size: 11px; text-transform: uppercase;">Prepared For:</strong><br>
                <strong>{{ $quotation->company?->name ?? $quotation->contact?->full_name ?? 'Client' }}</strong><br>
                {{ $quotation->contact?->email ?? '' }}
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
            @foreach ($quotation->items ?? [] as $item)
                <tr>
                    <td><strong>{{ $item['name'] ?? 'Item' }}</strong></td>
                    <td style="text-align: center;">{{ $item['quantity'] ?? $item['qty'] ?? 1 }}</td>
                    <td style="text-align: right;">{{ $quotation->currency }} {{ number_format($item['price'] ?? 0, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ $quotation->currency }} {{ number_format(($item['quantity'] ?? $item['qty'] ?? 1) * ($item['price'] ?? 0), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <table>
            <tr class="total-row">
                <td>Total Quotation:</td>
                <td style="text-align: right;">{{ $quotation->currency }} {{ number_format($quotation->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
