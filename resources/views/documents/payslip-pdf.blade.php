<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip {{ $payroll->month }}/{{ $payroll->year }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'DejaVu Sans', sans-serif; font-size: 13px; color: #1e293b; line-height: 1.5; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 22px; font-weight: bold; color: #4338ca; }
        .meta-table, .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .salary-table th { background: #f8fafc; padding: 8px 10px; text-align: left; border-bottom: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; }
        .salary-table td { padding: 9px 10px; border-bottom: 1px solid #e2e8f0; }
        .net-salary { font-size: 18px; font-weight: bold; color: #16a34a; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">{{ $payroll->employee?->tenant?->currency === 'BDT' ? 'বেতন স্লিপ / পে-স্লিপ' : 'EMPLOYEE SALARY PAYSLIP' }}</div>
            <div>Month / Period: <strong>{{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }} {{ $payroll->year }}</strong></div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: bold;">{{ $payroll->tenant?->name ?? 'AI Sales Assistant' }}</div>
            <div>Status: <strong style="color: #16a34a;">{{ strtoupper($payroll->status) }}</strong></div>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                Employee: <strong>{{ $payroll->employee?->full_name }}</strong><br>
                ID: {{ $payroll->employee?->employee_id }}<br>
                Designation: {{ $payroll->employee?->designation }}
            </td>
            <td style="width: 50%; text-align: right;">
                Department: {{ $payroll->employee?->department }}<br>
                Currency: {{ $payroll->employee?->tenant?->currency ?? 'USD' }}
            </td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Earnings Component</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td style="text-align: right;">{{ number_format($payroll->basic_salary, 2) }}</td>
            </tr>
            <tr>
                <td>Allowances (House, Medical, Travel)</td>
                <td style="text-align: right;">{{ number_format($payroll->allowances, 2) }}</td>
            </tr>
            <tr>
                <td>Sales Commission Payout</td>
                <td style="text-align: right;">{{ number_format($payroll->commission_amount, 2) }}</td>
            </tr>
            <tr>
                <td style="color: #dc2626;">Deductions (Taxes, Leaves)</td>
                <td style="text-align: right; color: #dc2626;">- {{ number_format($payroll->deductions, 2) }}</td>
            </tr>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td class="net-salary">NET PAYABLE SALARY</td>
                <td style="text-align: right;" class="net-salary">{{ number_format($payroll->net_salary, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
