<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'deductions' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getPayslipPdfDocument()
    {
        $preset = ($this->employee?->tenant?->currency === 'BDT') ? 'bengali' : 'english';
        return UnicodePdf::preset($preset)
            ->loadView('documents.payslip-pdf', ['payroll' => $this])
            ->a4();
    }
}
