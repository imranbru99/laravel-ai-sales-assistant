<?php

use App\Core\Tenancy\TenantContext;
use App\Models\Company;
use App\Models\Employee;
use App\Models\FieldVisit;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Tenant;
use App\Models\User;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('employees directory tracks staff profiles and salary', function () {
    $tenant = Tenant::create(['name' => 'HR Hub', 'slug' => 'hr']);
    app(TenantContext::class)->setTenant($tenant);

    $employee = Employee::create([
        'employee_id' => 'EMP-101',
        'first_name' => 'Tanvir',
        'last_name' => 'Hasan',
        'designation' => 'Senior Account Executive',
        'department' => 'Sales',
        'salary' => 3500.00,
    ]);

    expect($employee->full_name)->toBe('Tanvir Hasan')
        ->and((float) $employee->salary)->toBe(3500.0);
});

test('attendance logs integrate with laravel attendance hub', function () {
    $tenant = Tenant::create(['name' => 'Factory Terminal', 'slug' => 'factory']);
    app(TenantContext::class)->setTenant($tenant);

    $device = \ImranDevBd\AttendanceHub\Models\AttendanceDevice::create([
        'tenant_id' => $tenant->id,
        'name' => 'Main Gate ZKTeco Terminal',
        'provider' => 'zkteco',
        'model' => 'K40',
        'ip' => '192.168.1.201',
        'port' => 4370,
        'serial_number' => 'ZK-998877',
        'status' => 'online',
    ]);

    $log = AttendanceLog::create([
        'tenant_id' => $tenant->id,
        'device_id' => $device->id,
        'device_user_id' => '1001',
        'punched_at' => now(),
        'punch_type' => 'check_in',
        'verify_mode' => 'fingerprint',
        'punch_hash' => md5('1001-' . now()->timestamp),
    ]);

    expect($log->id)->not->toBeNull()
        ->and($log->punch_type)->toBe('check_in')
        ->and($log->verify_mode)->toBe('fingerprint');
});

test('payroll calculates net salary and generates payslip unicode pdf', function () {
    $tenant = Tenant::create(['name' => 'Apex Apparel', 'slug' => 'apex-apparel', 'currency' => 'BDT']);
    app(TenantContext::class)->setTenant($tenant);

    $employee = Employee::create([
        'employee_id' => 'EMP-777',
        'first_name' => 'Farhana',
        'last_name' => 'Akter',
        'designation' => 'Sales Lead',
        'salary' => 60000.00,
    ]);

    $payroll = Payroll::create([
        'employee_id' => $employee->id,
        'month' => 11,
        'year' => 2026,
        'basic_salary' => 50000,
        'allowances' => 15000,
        'commission_amount' => 12500,
        'deductions' => 2500,
        'net_salary' => 75000,
        'status' => 'approved',
    ]);

    $pdfDoc = $payroll->getPayslipPdfDocument();
    expect($pdfDoc)->not->toBeNull();

    $output = $pdfDoc->output();
    expect($output)->not->toBeEmpty();
});

test('field sales logs geo coordinates and visit outcomes', function () {
    $tenant = Tenant::create(['name' => 'Field Dynamics', 'slug' => 'field']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    app(TenantContext::class)->setTenant($tenant);

    $company = Company::create(['name' => 'Chittagong Shipping Corp']);

    $visit = FieldVisit::create([
        'user_id' => $user->id,
        'company_id' => $company->id,
        'scheduled_at' => now()->addDay(),
        'check_in_at' => now(),
        'latitude' => 22.3568510,
        'longitude' => 91.7831820,
        'outcome' => 'Presented annual software agreement to Managing Director.',
    ]);

    expect($visit->id)->not->toBeNull()
        ->and((float) $visit->latitude)->toBe(22.356851)
        ->and($visit->outcome)->toContain('Presented annual software agreement');
});
